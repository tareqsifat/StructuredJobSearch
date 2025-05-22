<?php

use Sifat\WebScrapingCurl\UploadJobsController;

require __DIR__ . '/vendor/autoload.php'; // Load Composer's autoloader


$UploadJobsController = new UploadJobsController();
$UploadJobsController->uploadJobs();
echo "done";