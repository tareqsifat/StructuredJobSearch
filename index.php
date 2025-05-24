<?php

require __DIR__ . '/vendor/autoload.php'; // Load Composer's autoloader

use Sifat\WebScrapingCurl\BaseController;
use Sifat\WebScrapingCurl\CategoryJobController;
use Sifat\WebScrapingCurl\Config;
use Sifat\WebScrapingCurl\Factory\LogFactory;
use Sifat\WebScrapingCurl\Factory\ParserFactory;
use Sifat\WebScrapingCurl\HomePageController;
use Sifat\WebScrapingCurl\Scraper;
use Sifat\WebScrapingCurl\UploadJobsController;
use Sifat\WebScrapingCurl\UploadsjobController;
 

$options = getopt("", ["type:"]);
$type = $options['type'] ?? 'all';
Config::set('type', $type);

$links = [];
$target_url = '';
if($type == 'govt'){
    $target_url = "https://jobs.bdjobs.com/jobsearch.asp?txtsearch=&fcat=-1&qOT=1&iCat=0&Country=0&qPosted=0&qDeadline=0&Newspaper=0&qJobNature=0&qJobLevel=0&qExp=0&qAge=0&hidOrder=%27%27&rpp=50&hidJobSearch=JobSearch&MPostings=&ver=&strFlid_fvalue=&strFilterName=&hClickLog=1&earlyAccess=0&fcatId=-1&hPopUpVal=1";
} else if($type == 'new'){
    $target_url = 'https://jobs.bdjobs.com/JobSearch.asp?icatId=&requestType=new';
} else if($type == 'all'){
    $target_url = "https://jobs.bdjobs.com/jobsearch.asp?txtsearch=&fcat=-1&qOT=0&iCat=0&Country=0&qPosted=0&qDeadline=0&Newspaper=0&qJobNature=0&qJobLevel=0&qExp=0&qAge=0&hidOrder=%27%27&rpp=50&hidJobSearch=JobSearch&MPostings=&ver=&strFlid_fvalue=&strFilterName=&hClickLog=1&earlyAccess=0&fcatId=-1&hPopUpVal=1";
}
if (empty($target_url)) {
    LogFactory::saveLog('message: No target URL found');
    die('message: No target URL found');
}
$categorizedJobs = new CategoryJobController();
$links = $categorizedJobs->getAllCategoryJobs($target_url);

if (!empty($links)) {
    $categorizedJobs->saveAllJobDetails($links);
} else {
    LogFactory::saveLog('message: No links found');
}

echo "done";
