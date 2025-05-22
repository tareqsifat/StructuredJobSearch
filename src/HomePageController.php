<?php

namespace Sifat\WebScrapingCurl;

use Exception;
use Sifat\WebScrapingCurl\Factory\LogFactory;
use Sifat\WebScrapingCurl\Factory\ParserFactory;

class HomePageController extends BaseController{
    public function __construct()
    {
        parent::__construct();
    }

    public function SaveHomePageHtml($url)
    {
        $home = $this->call_curl($url);
        if($home['status']){
            $titleParser = ParserFactory::createParser('title');
            $title = $titleParser->parse($home['content']);
            $fullPath = $this->SavedHtml . DIRECTORY_SEPARATOR . $title . '.html';
            if (file_put_contents($fullPath, $home['content']) !== false) {
                $message = "File: $title.html saved successfully to : $fullPath\n";
                echo $message;
                LogFactory::saveLog($message);
                return $home['content'];
            }else {
                LogFactory::saveLog("Home Page Save failed");
            }
        }
    }
    public function saveCategoryJobs($htmlContent){
        $jsonFile = 'Categorized-jobs.json';
        $fullPath = $this->SavedJson . DIRECTORY_SEPARATOR . $jsonFile;
        if(empty($htmlContent)){
            return new Exception('Failed to read html');
        }
        $home = ParserFactory::createParser('joblist');
        $category_links = $home->parse($htmlContent);
        if (file_put_contents($fullPath, json_encode($category_links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
            $message = "Links saved to $fullPath\n";
            LogFactory::saveLog($message);
        } else {
            LogFactory::saveLog(new Exception('Failed to save links to JSON file.'));
        }
    }


}