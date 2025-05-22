<?php

namespace Sifat\WebScrapingCurl;

class Config
{
    public static $globalSettings = [
        // Add other global settings here
    ];

    // Optional: Add methods if you need dynamic configuration
    public static function set($key, $value)
    {
        self::$globalSettings[$key] = $value;
    }

    public static function get($key)
    {
        return self::$globalSettings[$key] ?? null;
    }
}