<?php

namespace Sifat\WebScrapingCurl\Factory;

use DOMDocument;
use DOMXPath;
use Sifat\WebScrapingCurl\Config;

class JobDetailsParser implements Parser 
{
    public function parse($htmlContent) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $type = Config::get('type') ?? 'all';
        $job_details = [];
        $job_details['type'] = $type;

        // die(__FILE__ . __LINE__ . " " . __METHOD__ . " " . __LINE__);
        $job_details['jobLink'] = $xpath->query('//a[@class="job-link"]/@href')->item(0)?->nodeValue;

        $jobTitle = $xpath->query('//h4[@class="job-title"]/text()')->item(0)?->nodeValue;
        $companyName = $xpath->query('//h2[@class="company-name"]/text()')->item(0)?->nodeValue;


        $companyNameNode = $xpath->query('//h2[@class="cname"]/text()')->item(0);
        $job_details['companyName'] = $companyNameNode ? $companyNameNode->nodeValue : null;
        $job_details['jobTitle'] = $xpath->query('//h2[@class="jtitle"]/text()')->item(0)?->nodeValue;


        $job_details['application_deadline'] = $xpath->query('//span[@class="deadlinetxt"]/text()')->item(0)?->nodeValue;

        $summaryItems = $xpath->query('//div[@class="summery__crd"]//ul[@class="summery__items"]/li');
        foreach ($summaryItems as $item) {
            $text = trim($item->textContent);
            $keyValue = explode(':', $text, 2);
            if (count($keyValue) == 2) {
                $job_details[trim($keyValue[0])] = trim($keyValue[1]);
            }
        }

        $jobContentDivs = $xpath->query('//div[contains(@class, "jobcontent")]');
        foreach ($jobContentDivs as $div) {
            $classes = explode(' ', $div->getAttribute('class'));
            $variableName = $classes[1] ?? 'jobcontent';
            $job_details[$variableName] = $dom->saveHTML($div);
        }
        $job_details['is_pdf'] = 'false';
        if(!empty($jobTitle) && !empty($companyName) && $type == 'govt'){
            $job_details['jobTitle'] = $jobTitle;
            $job_details['companyName'] = $companyName;
            $job_details['imageLink'] = $xpath->query('//div[@class="image"]/img')->item(0)?->getAttribute('src');
            $job_details['is_pdf'] = 'false';
        }
        if($type == 'govt'){
            $pdf_link = $xpath->query(
                '//div[contains(@class,"jobcontent") and contains(@class,"readbefapply")]//a'
            )->item(0)?->getAttribute('href');
            $job_details['pdf_link'] = $pdf_link;
            if(isset($pdf_link) && !empty($pdf_link)){
                $job_details['is_pdf'] = 'true';
            }
        }
        return $job_details;
    }
}
