<?php


namespace App\Com;


class RedisKeyMap
{
    const TOKEN = "token";

    const TOKEN_CACHE = "token_cache";

    const RULE_PERMISSION_CACHE = "rule_permission_cache";

    const REQUEST_LIMIT = 'request_limit';

    /**
     * @param string $key 使用 self::xxx的形式传入
     * @param array $args
     * @return string
     */
    public static function build(string $key, array $args = []):string
    {
        $prefix = "txf:";
        if (empty($args)) {
            return $prefix . $key;
        } else {
            $key = $prefix . $key;

            foreach ($args as $k => $v) {
                if (!is_numeric($k)) {
                    $key .= ":{$k}_{$v}";
                } else {
                    $key .= ":{$v}";
                }
            }
            return $key;
        }
    }
}