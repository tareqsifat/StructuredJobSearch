<?php

namespace Sifat\WebScrapingCurl;

use Exception;
use DOMDocument;
use DOMXPath;
use Sifat\WebScrapingCurl\BaseController;
use Sifat\WebScrapingCurl\Factory\LogFactory;
use Sifat\WebScrapingCurl\Factory\ParserFactory;

class CategoryJobController extends BaseController
{
    private $jsonFile;
    private $categorizedJobList;
    private $categorizedJobListJson;
    private $categorizedJobDetails;
    private $categorizedJobDetailsJsonFolder;
    public function __construct()
    {
        parent::__construct();
        $this->jsonFile = $this->SavedJson . DIRECTORY_SEPARATOR . 'Categorized-jobs.json';
        $this->categorizedJobList = $this->SavedHtml . DIRECTORY_SEPARATOR . 'categorizedJobList';
        $this->categorizedJobListJson = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorized-job-list.json';
        $this->categorizedJobDetails = $this->SavedHtml . DIRECTORY_SEPARATOR . 'categorizedJobDetails';
        $this->categorizedJobDetailsJsonFolder = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorizedJobDetails';

    }
    public function saveCategorizedJobs(){
        $jsonData = file_get_contents($this->jsonFile);

        // Decode JSON into an associative array
        $data = json_decode($jsonData, true);

        // Check if decoding was successful
        if ($data === null) {
            LogFactory::saveLog('message: Error decoding JSON');
        }
        $folderName = $this->SavedJson . DIRECTORY_SEPARATOR . 'category_jobs';
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        foreach($data as $jobs){
            $url =  $jobs['href'];
            $category_page = $this->call_curl($url);
            $titleParser = ParserFactory::createParser('title');
            $title = $titleParser->parse($category_page['content']);
            if (!is_dir($this->categorizedJobList)) {
                mkdir($this->categorizedJobList, 0777, true);
            }
            $categorizedJobListPath = $this->categorizedJobList . DIRECTORY_SEPARATOR . $title . '.html';
            if (file_put_contents($categorizedJobListPath, $category_page['content']) !== false) {
                $message = "File: $title.html saved successfully to : $categorizedJobListPath\n";
                echo $message;
                LogFactory::saveLog($message);
            } else {
                LogFactory::saveLog("categorizedJobList Page Save failed");
            }
            $random_sleep = rand(10, 40);
            echo "\n<br> Script will sleep for $random_sleep<br>\n";
            die;
            sleep($random_sleep);
        }
    }
    
    public function saveCategoryJobs($htmlContent){
        $jsonFile = 'Categorized-job-details-link.json';
        $fullPath = $this->SavedJson . DIRECTORY_SEPARATOR . $jsonFile;
        if(empty($htmlContent)){
            return new Exception('Failed to read html');
        }
        $home = ParserFactory::createParser('homepage');
        $category_links = $home->parse($htmlContent);
        if (file_put_contents($fullPath, json_encode($category_links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
            $message = "Links saved to $fullPath\n";
            LogFactory::saveLog($message);
        } else {
            LogFactory::saveLog(new Exception('Failed to save links to JSON file.'));
        }
    }
    public function getAllCategoryJobs($url){
        $category_page = $this->call_curl($url . '&pg=' . 1);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($category_page['content']);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        // grab all the <li> under <div class="pagination" id="topPagging">
        $liNodes = $xpath->query(
            "//div[@class='pagination' and @id='topPagging']//ul/li"
        );

        // get the count
        $liCount = $liNodes->length;
        for($i=2;$i<=$liCount;$i++){
            $category_page = $this->call_curl($url . '&pg=' . $i);
            $titleParser = ParserFactory::createParser('title');
            $title = $titleParser->parse($category_page['content']);
            if(isset($category_page['content']) && !empty($category_page['content'])){
                $message = "Content of $title.html is retrieved successfully\n";
                echo $message . __FILE__ . __LINE__;
                LogFactory::saveLog($message);
                return $category_page['content'];
            } else {
                $message = "Content of the $url can not be downloaded\n";
                LogFactory::saveLog($message);
                return false;
            }
            $random_sleep = rand(10, 40);
            echo "\n<br> Script will sleep for $random_sleep<br>\n";
            // break;
            sleep($random_sleep);
        }
    }
    public function saveAllJobLinks($htmlContent){
        $links = [];

        try {
            if(!$htmlContent){
                $message = "Html file not Found";
                LogFactory::saveLog($message); // Merge into a single array
                exit($message);
            }
            $jobListParser = ParserFactory::createParser('joblist');
            $links = $jobListParser->parse($htmlContent);
            foreach ($links as $link) {
                $message = "job " . $link['title'] . " link retrieved successfully\n" . __FILE__ . __LINE__;
                LogFactory::saveLog($message); // Merge into a single array
                echo $message;
            }

            return $links;
        } catch (Exception $e) {
            LogFactory::saveLog($e->getMessage());
            echo 'Error: ' . $e->getMessage();
            return false;
        }

    }
    public function saveAllJobDetails($data){
        foreach ($data as $key => $item) {
            $url =  $item['href'];
            $job_details_page = $this->call_curl($url);
            $titleParser = ParserFactory::createParser('title');
            $title = $titleParser->parse($job_details_page['content']);
            if (!is_dir($this->categorizedJobDetails)) {
                mkdir($this->categorizedJobDetails, 0777, true);
            }
            $categorizedJobDetailsPath = $this->categorizedJobDetails . DIRECTORY_SEPARATOR . $title . '-' .$key. '.html';
            if (file_put_contents($categorizedJobDetailsPath, $job_details_page['content']) !== false) {
                $jobDetailsParser = ParserFactory::createParser('jobdetails');
                $details = $jobDetailsParser->parse($job_details_page['content']);
                $message = "File: $title-$key.html saved successfully to : $categorizedJobDetailsPath\n";
                echo $message;
                LogFactory::saveLog($message);
            }else {
                LogFactory::saveLog("categorizedJobDetails Page Save failed");
            }
            // decide on one JSON file
            $jsonFile = $this->categorizedJobDetailsJsonFolder . DIRECTORY_SEPARATOR . 'all_jobs.json';

            // make sure the dir exists
            if (!is_dir($this->categorizedJobDetailsJsonFolder)) {
                mkdir($this->categorizedJobDetailsJsonFolder, 0777, true);
            }

            // load existing data (or start fresh)
            if (file_exists($jsonFile)) {
                $raw = file_get_contents($jsonFile);
                $jobs = json_decode($raw, true);
                if (!is_array($jobs)) {
                    // if the file was empty or invalid JSON, reset
                    $jobs = [];
                }
            } else {
                $jobs = [];
            }

            // add your new job details
            $details['application_link'] = $url;
            $details['id'] = explode('=',explode('&', explode('?', $url)[1])[0])[1];


            //upload jobs to the server
            $UploadJobsController = new UploadJobsController();
            $UploadJobsController->uploadJobs($details);
            $jobs[] = $details;

            // save it back out
            file_put_contents(
                $jsonFile,
                json_encode($jobs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            $random_sleep = rand(10, 20);
            echo "\n<br> Script will sleep for $random_sleep<br>\n";
            sleep($random_sleep);
        }
    }
}
/*
featured-wrap
sout-jobs-wrapper
norm-jobs-wrapper
*/