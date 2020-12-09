<?php


namespace App\Services\OpenID;


use App\Models\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
{
    protected $tokenSigner;

    public function __construct(Signer\Rsa $tokenSigner)
    {
        $this->tokenSigner = $tokenSigner;
    }


    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $result = parent::getExtraParams($accessToken);

        // Add IDToken to scope request.
        if($this->isOpenIDRequest($accessToken->getScopes())) {
            $result['id_token'] = $this->getIdToken($accessToken)->toString();
        }

        // Return the token with the extra parameters.
        return $result;
    }

    protected function getIdToken(AccessTokenEntityInterface $accessToken): Token\Plain
    {
        /** @var User $user */
        $user = User::where(['id' => $accessToken->getUserIdentifier()])->first();

        $builder = resolve(Builder::class)
            ->permittedFor($accessToken->getClient()->getIdentifier())
            ->expiresAt($accessToken->getExpiryDateTime())
            ->relatedTo($user->id);

        $scopes = array_map(function (ScopeEntityInterface $key) {
            return $key->getIdentifier();
        }, $this->accessToken->getScopes());

        foreach ($user->getClaims($scopes) as $claimName => $claimValue) {
            $builder = $builder->withClaim($claimName, $claimValue);
        }

        return $builder->getToken(
            $this->tokenSigner,
            Signer\Key\LocalFileReference::file($this->privateKey->getKeyPath(), $this->privateKey->getPassPhrase() ?? '')
        );
    }

    /**
     * Checks if the request is an openID request from the tokens.
     *
     * @param ScopeEntityInterface[] $scopes
     * @return boolean
     */
    private function isOpenIDRequest($scopes)
    {
        foreach ($scopes as $scope) {
            if($scope->getIdentifier() === 'openid') {
                return true;
            }
        }
        return false;
    }
}
