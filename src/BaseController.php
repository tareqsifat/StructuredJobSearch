<?php

namespace Sifat\WebScrapingCurl;

use Exception;
use Sifat\WebScrapingCurl\Factory\LogFactory;

class BaseController {
    public $SavedHtml;
    public $SavedJson;
    public function __construct() {
        $baseDir = dirname(__DIR__, 1);
        $this->SavedHtml = $baseDir . '/SavedHtml';
        $this->SavedJson = $baseDir . '/SavedJson';

        if (!is_dir($this->SavedHtml)) {
            mkdir($this->SavedHtml, 0777, true);
        }

        if (!is_dir($this->SavedJson)) {
            mkdir($this->SavedJson, 0777, true);
        }
    }
    public function fetch($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        if (empty($response)) {
            return [
                'status' => false,
                'error' => new Exception('Failed to read html')
            ];
        } else {
            return [
                'status' => true,
                'content' => $response
            ];
        }
    }
    function call_curl($url, $method = 'GET', $data = []) {
        $ch = curl_init();
        
        // Set URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (if required)
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
        
        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            $error = 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            return [
                'status' => false,
                'error' => $error
            ];
        }
        
        curl_close($ch);
        
        return [
            'status' => true,
            'content' => $response
        ];
    }
    public function saveJson($jsonFile, $links, $append = false) {
        $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        $jsonData = json_encode($links, $jsonOptions);
        
        if ($append && file_exists($jsonFile)) {
            $existingData = json_decode(file_get_contents($jsonFile), true);
            if (is_array($existingData)) {
                $links = array_merge($existingData, $links);
                $jsonData = json_encode($links, $jsonOptions);
            }
        }

        if (file_put_contents($jsonFile, $jsonData) !== false) {
            echo "Links saved to $jsonFile\n";
        } else {
            throw new Exception('Failed to save links to JSON file.');
        }

        // Free the memory of $links
        unset($links);
    }
    
    
}
