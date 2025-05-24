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
    private $checkJobUrl;
    public function __construct()
    {
        parent::__construct();
        $this->detailedJsonFolder = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorizedJobDetails';
        $this->copyDetailedJsonFolder = $this->SavedJson . DIRECTORY_SEPARATOR . 'categorizedJobDetailsUpload';
        $this->jobUploadUrl = 'https://eurostaffs.org/api/upload_scrapped_job_post';
        $this->checkJobUrl = 'http://eurostaffs.org/api/check_scrapped_job_availability';
        $this->api_key = '@Pass123!@#';
    }
    
    
    
    public function uploadJobs($job){
        if (!is_array($job) || !isset($job['jobTitle'])) {
            LogFactory::saveLog("Job title Not Found, job id is " . $job['id']);
            return;
        }
        $checked = $this->check_scrapped_job_availability($job);
        // die('message: ' . print_r($checked) . "\n");
        if ($checked['status']) {
            // decode the JSON once
            $decoded = json_decode($checked['content'], true);
            if ($decoded['status'] === false) {
                return $decoded['message'];
            }
        } 
        foreach ($job as $key => $jobPost) {
            $data[$key] = $jobPost;
        }
        $data['api_key'] = $this->api_key;
        $response = $this->call_curl($this->jobUploadUrl,'POST', $data);
        LogFactory::saveLog($response['content']);
        return 0;
    }
    public function check_scrapped_job_availability($job){
        if (!is_array($job) || !isset($job['jobTitle'])) {
            LogFactory::saveLog("Job title Not Found, job id is " . $job['id']);
            return;
        }
        $data = [
            'id' => $job['id'],
            'job_source' => $job['job_source'],
            'api_key' => $this->api_key,
        ];
        $response = $this->call_curl($this->checkJobUrl,'POST', $data);

        LogFactory::saveLog(print_r($response));
        return $response;
    }
}
