<?php

namespace Sifat\WebScrapingCurl\Factory;

use DOMDocument;
use DOMXPath;

class TitleParser implements Parser
{
    public function parse($htmlContent) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();
    
        // Find the target div
        $xpath = new DOMXPath($dom);
        $divs = $xpath->query('//title');
        if(empty($divs))  return;
        $title =  ($divs->length > 0) ? $divs->item(0)->nodeValue : null;
        $cleanedTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $title); // Replace special characters with hyphens
        $cleanedTitle = preg_replace('/\s+/', '-', trim($cleanedTitle));
        return $cleanedTitle;
    }
}