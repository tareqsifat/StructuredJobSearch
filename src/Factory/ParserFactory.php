<?php

namespace Sifat\WebScrapingCurl\Factory;

use Exception;

class ParserFactory {
    public static function createParser($type) {
        switch ($type) {
            case 'homepage':
                return new HomePageParser();
            case 'joblist':
                return new JobListParser();
            case 'jobdetails':
                return new JobDetailsParser();
            case 'title':
                return new TitleParser();
            default:
                throw new Exception("Invalid parser type: $type");
        }
    }
}
