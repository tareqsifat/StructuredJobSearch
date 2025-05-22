<?php 

namespace Sifat\WebScrapingCurl;

use Exception;
use Sifat\WebScrapingCurl\BaseController;
use Sifat\WebScrapingCurl\Factory\LogFactory;
use Sifat\WebScrapingCurl\Factory\ParserFactory;        
class UploadJobsController extends BaseController
{
    private $detailedJsonFolder;
    private $copyDetailedJsonFolder;
    private $jobUploadUrl;
    private $api_key;
    public function __construct()
    {
        parent::__construct();
        $this->detailedJsonFolder = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorizedJobDetails';
        $this->copyDetailedJsonFolder = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorizedJobDetailsUpload';
        $this->jobUploadUrl = 'https://eurostaffs.org/api/upload_scrapped_job_post';
        $this->api_key = '@Poss123!@#';
    }
    
    
    
    public function uploadJobs($job){
        if (!is_array($job) || !isset($job['jobTitle'])) {
            LogFactory::saveLog("Job title Not Found, job id is " . $job['id']);
            return;
        }
        foreach ($job as $key => $jobPost) {
            $data[$key] = $jobPost;
        }
        $response = $this->call_curl($this->jobUploadUrl,'POST', $data);
        LogFactory::saveLog($response['content']);
        return 0;
    }
}
