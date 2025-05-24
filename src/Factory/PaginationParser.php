<?php 

namespace Sifat\WebScrapingCurl\Factory;

use DOMDocument;
use DOMXPath;

class PaginationParser
{
    public static function parse($htmlContent)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();
    
        // Find the target div
        $xpath = new DOMXPath($dom);

        // Find the div with class 'pagination' and id 'topPagging'
        $paginationDivs = $xpath->query('//div[@id="topPagging" and contains(concat(" ", normalize-space(@class), " "), " pagination ")]');

        // Check if such a div is found
        if ($paginationDivs->length === 0) {
            return 0; // Or handle accordingly
        }

        // Get the first matching div
        $paginationDiv = $paginationDivs->item(0);

        // Query all <li> elements within this div
        $liItems = $xpath->query('.//li', $paginationDiv);

        // Return the count of <li> elements
        return $liItems->length;

    }
}