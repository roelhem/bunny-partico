<?php


namespace App\GraphQL\Types;


use CommerceGuys\Intl\Language\Language;

class LanguageType
{
    protected function getLanguageInstance($input, ?string $locale = null)
    {
        if($input instanceof Language) {
            if($locale && $input->getLocale() !== $locale) {
                return \StaticData::getLanguage($input->getLanguageCode(), $locale);
            } else {
                return $input;
            }
        } else {
            return \StaticData::getLanguage($input, $locale);
        }
    }

    public function __invoke($input)
    {
        return $this->getLanguageInstance($input);
    }

    public function resolveName($input, array $args)
    {
        return $this->getLanguageInstance($input, $args['locale'])->getName();
    }

    public function resolveCode($input)
    {
        return $this->getLanguageInstance($input)->getLanguageCode();
    }
}
