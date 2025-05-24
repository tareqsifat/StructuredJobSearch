<?php

use Sifat\WebScrapingCurl\UploadJobsController;

require __DIR__ . '/vendor/autoload.php'; // Load Composer's autoloader
$data = [
    "type" => "govt",
    "jobLink" => null,
    "companyName" => "চট্টগ্রাম বন্দর কর্তৃপক্ষ",
    "jobTitle" => "সহকারী প্রকৌশলী মেরিন",
    "application_deadline" => null,
    "is_pdf" => "false",
    "image_link" => "http://imgs.bdjobs.com/scannedjobads/279706.jpg",
    "pdf_link" => null,
    "job_url" => "https://jobs.bdjobs.com/jobdetails.asp?id=279706&fcatId=-1&ln=2",
    "job_source" => "bdjobs",
    "id" => "279706"
];

$UploadJobsController = new UploadJobsController();
$UploadJobsController->uploadJobs($data);
echo "done";