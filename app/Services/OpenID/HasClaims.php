<?php


namespace App\Services\OpenID;


use Illuminate\Support\Str;

trait HasClaims
{
    /**
     * Custom claims defined by the user.
     *
     * @var array
     */
    private $customClaims = [];

    /**
     * An attribute mapping for the claims.
     *
     * @var array
     */
    protected $claims = [];

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CLAIM METHODS -------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    /**
     * Get the claim attributes defined in a method.
     *
     * @return array
     */
    public function getMethodClaims()
    {
        $class = static::class;

        if (! isset(ClaimType::$claimMethodCache[$class])) {
            static::cacheClaimAttributes($class);
        }

        return ClaimType::$claimMethodCache[$class];
    }

    public function hasMethodClaim(string $scope)
    {
        return in_array($scope, $this->getMethodClaims());
    }

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param  string  $class
     * @return void
     */
    public static function cacheClaimAttributes($class)
    {
        ClaimType::$claimMethodCache[$class] = collect(static::getClaimMethods($class))->map(function ($match) {
            return lcfirst(Str::snake($match));
        })->all();
    }

    /**
     * Returns a list of all claim methods of this class.
     *
     * @param string $class
     * @return array
     */
    protected static function getClaimMethods($class)
    {
        preg_match_all('/(?<=^|;)get([^;]+?)Claim(;|$)/', implode(';', get_class_methods($class)), $matches);
        return $matches[1];
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- CLAIM SCOPES --------------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected $claimScopesCache;

    /**
     * Returns a list of scope-names that have a claim.
     *
     * @return array
     */
    public function getClaimScopes()
    {
        if($this->claimScopesCache) {
            return $this->claimScopesCache;
        }

        $result = [];

        // Add claims from the claims property.
        foreach ($this->claims as $key => $value) {
            if(is_array($value)) {
                $result[$key] = ClaimType::CUSTOM_CLAIM;
            }
        }

        // Add the method claims.
        foreach ($this->getMethodClaims() as $scope) {
            $result[$scope] = ClaimType::CUSTOM_CLAIM;
        }

        // Add the default claims.
        foreach (ClaimType::$defaultClaims as $scope => $claimFields) {
            $result[$scope] = ClaimType::DEFAULT_CLAIM;
        }

        $this->claimScopesCache = $result;

        return $result;
    }

    public function hasClaimScope(string $scope)
    {
        return isset($this->getClaimScopes()[$scope]);
    }

    public function isDefaultClaimScope(string $scope)
    {
        if(!$this->hasClaimScope($scope)) {
            return false;
        }

        return $this->getClaimScopes()[$scope] === ClaimType::DEFAULT_CLAIM;
    }

    // ---------------------------------------------------------------------------------------------------------- //
    // ----- GETTING CLAIM VALUES ------------------------------------------------------------------------------- //
    // ---------------------------------------------------------------------------------------------------------- //

    protected function getClaimMethodValues(string $scope)
    {
        if($this->hasMethodClaim($scope)) {
            return $this->{'get'.\Str::studly($scope).'Claim'}();
        } else {
            return null;
        }
    }

    protected function getClaimValueFromAttribute(string $scope, string $key)
    {
        if(!$this->hasClaimScope($scope)) {
            return null;
        }

        if(isset($this->claims[$scope]) && is_array($this->claims[$scope]) && isset($this->claims[$scope][$key])) {
            $attribute = $this->claims[$scope][$key];
            if(is_string($attribute)) {
                return $this->{$attribute};
            }
        }

        if(isset($this->claims[$key])) {
            $attribute = $this->claims[$key];
            if(is_string($attribute)) {
                return $this->{$attribute};
            }
        }

        return null;
    }

    public function getClaimValues(string $scope)
    {
        if(!$this->hasClaimScope($scope)) {
            return [];
        }

        $result = [];

        $fromMethod = $this->getClaimMethodValues($scope);

        if($this->isDefaultClaimScope($scope)) {
            $fields = ClaimType::$defaultClaims[$scope];
            foreach ($fields as $field) {
                if($fromMethod !== null && isset($fromMethod[$field])) {
                    $result[$field] = $fromMethod[$field];
                } else {
                    $result[$field] = $this->getClaimValueFromAttribute($scope, $field);
                }
            }
        } else {
            // Get the fields from the claim method.
            if($fromMethod !== null && is_array($fromMethod)) {
                $result = $fromMethod;
            }

            // Get the fields from the claim attributes.
            if(isset($this->claims[$scope]) && is_array($this->claims[$scope])) {
                $fields = $this->claims[$scope];
                foreach ($fields as $field => $attribute)
                {
                    if(!isset($result[$field])) {
                        $result[$field] = $this->{$attribute};
                    }
                }
            }
        }

        // Filter the claims
        return array_filter($result, function($v) {
            return isset($v) && !is_null($v);
        });
    }

    public function getClaims(?array $scopes = null)
    {
        // Use all claim scopes if scopes null is given.
        if($scopes === null) {
            $scopes = array_keys($this->getClaimScopes());
        }

        // Init the result.
        $result = [];

        // Add the custom claim fields.
        foreach ($scopes as $scope) {
            if(!$this->isDefaultClaimScope($scope)) {
                foreach ($this->getClaimValues($scope) as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }

        // Add the default claim fields.
        foreach ($scopes as $scope) {
            if($this->isDefaultClaimScope($scope)) {
                foreach ($this->getClaimValues($scope) as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }

        // Return the result.
        return $result;
    }
}
