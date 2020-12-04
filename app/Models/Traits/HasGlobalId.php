<?php


namespace App\Models\Traits;


trait HasGlobalId
{
    function getGlobalIdAttribute() {
        $key = $this->getKey();
        $table = $this->getTable();
        $b64 = base64_encode(md5($key.$table, true));
        $url = strtr($b64, '+/','-_');
        return rtrim($url, '=');
    }
}
