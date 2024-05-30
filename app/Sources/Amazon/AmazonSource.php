<?php

namespace App\Sources\Amazon;

use App\Sources\AbstractSource;

class AmazonSource extends AbstractSource
{
    public function query($isbn): array
    {
        $searchItemRequest = [
            'PartnerType' => "Associates",
            'PartnerTag' => config('sources.amazon.associateId'),
            'Keywords' => "Harry",
            'SearchIndex' => "All",
            'Resources' => ["Images.Primary.Small", "ItemInfo.Title", "Offers.Listings.Price"],
        ];
        $host = "webservices.amazon.com";
        $path = "/paapi5/searchitems";
        $payload = json_encode($searchItemRequest);
        $awsv4 = new AmazonAPI(config('sources.amazon.accessKey'), config('sources.amazon.secretKey'));
        $awsv4->setRegionName("us-east-1");
        $awsv4->setServiceName("ProductAdvertisingAPI");
        $awsv4->setPath($path);
        $awsv4->setPayload($payload);
        $awsv4->setRequestMethod("POST");
        $awsv4->addHeader('content-encoding', 'amz-1.0');
        $awsv4->addHeader('content-type', 'application/json; charset=utf-8');
        $awsv4->addHeader('host', $host);
        $awsv4->addHeader('x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems');
        $headers = $awsv4->getHeaders();
        $headerString = "";
        foreach ($headers as $key => $value) {
            $headerString .= $key . ': ' . $value . "\r\n";
        }
        $params = array(
            'http' => array(
                'header' => $headerString,
                'method' => 'POST',
                'content' => $payload
            )
        );
        $stream = stream_context_create($params);

        $url = 'https://' . $host . $path;
        $fp = @fopen($url, 'rb', false, $stream);

        if (!$fp) {
            dd($url, $params, $stream);
            throw new \Exception ("Exception Occured opening $url");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new \Exception ("Exception Occured getting stream from $url");
        }
        dd($response);

    }

}
