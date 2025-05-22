<?php

namespace Sifat\WebScrapingCurl\Factory;

use DOMDocument;
use DOMXPath;
use Sifat\WebScrapingCurl\Factory\Parser;

class HomePageParser implements Parser 
{
    public function parse($htmlContent) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $divs = $xpath->query('//div[contains(@class, "category-list") and contains(@class, "padding-mobile")]');

        $links = [];
        foreach ($divs as $div) {
            $anchors = $div->getElementsByTagName('a');
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href') ?: null;
                $title = $anchor->getAttribute('title') ?: null;

                if ($href && $title) {
                    $links[] = [
                        'href' => "https:$href",
                        'title' => $title,
                    ];
                }
            }
        }
        return $links;
    }
}