<?php

namespace Sifat\WebScrapingCurl\Factory;

use DOMDocument;
use DOMXPath;

class JobListParser implements Parser 
{
    public function parse($htmlContent) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//div[contains(@class, "featured-wrap")] | //div[contains(@class, "sout-jobs-wrapper")] | //div[contains(@class, "norm-jobs-wrapper")]');
        $links = [];
        if(empty($nodes)){
            return false;
        }

        foreach ($nodes as $key => $node) {
            $anchors = $node->getElementsByTagName('a');
            if(empty($anchors)){
                return false;
            }
            foreach ($anchors as $anchor) {
                if(empty($anchor)){
                    return false;
                }
                $href = $anchor->getAttribute('href');
                if ($href) {
                    $href = "https://jobs.bdjobs.com/" . $href;
                }
                $title = trim(preg_replace('/\s+/', ' ', $anchor->textContent));
                if ($href && $title) {
                    $links[] = [
                        'href' => $href,
                        'title' => $title,
                    ];
                }
            }
        }
        return $links;
    }
}