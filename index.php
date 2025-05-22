<?php

require __DIR__ . '/vendor/autoload.php'; // Load Composer's autoloader

use Sifat\WebScrapingCurl\BaseController;
use Sifat\WebScrapingCurl\CategoryJobController;
use Sifat\WebScrapingCurl\Config;
use Sifat\WebScrapingCurl\Factory\LogFactory;
use Sifat\WebScrapingCurl\Factory\ParserFactory;
use Sifat\WebScrapingCurl\HomePageController;
use Sifat\WebScrapingCurl\Scraper;

// $options = getopt("", ["type:"]);
// $type = $options['type'] ?? 'all';
// Config::set('type', $type);
// $links = [];
// $target_url = "https://jobs.bdjobs.com/jobsearch.asp?txtsearch=&fcat=-1&qOT=1&iCat=0&Country=0&qPosted=0&qDeadline=0&Newspaper=0&qJobNature=0&qJobLevel=0&qExp=0&qAge=0&hidOrder=%27%27&rpp=100&hidJobSearch=JobSearch&MPostings=&ver=&strFlid_fvalue=&strFilterName=&hClickLog=1&earlyAccess=0&fcatId=-1&hPopUpVal=1";
$categorizedJobs = new CategoryJobController();
// $content = $categorizedJobs->getAllCategoryJobs($target_url);
// if (!empty($content)) {
//     $links = $categorizedJobs->saveAllJobLinks($content);
// }
$links = array();
$links[]['href'] = 'https://jobs.bdjobs.com/jobdetails.asp?id=279511&fcatId=-1&ln=2';

if (!empty($links)) {
    $categorizedJobs->saveAllJobDetails($links);
} else {
    LogFactory::saveLog('message: No links found');
}

echo "done";
