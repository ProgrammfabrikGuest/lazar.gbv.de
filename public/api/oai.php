<?php

require '../../vendor/autoload.php';

// initialize proxy
$proxy = new GBV\OAI\Proxy([
    'backend' => 'https://lazardb.gbv.de/api/plugin/base/oai/oai/request',
    'baseUrl' => 'http://lazar.gbv.de/api/oai',
    'xslt'    => 'oai.xsl',
    'instructions' => [
        'css' => '../css/bootstrap.min.css ../css/bootstrap-lazar.css',
        'brand' => 'LaZAR OAI-PMH',
        'brandUrl' => '../api'
    ],
    'pretty' => true,
    'formats' => [
        'datacite' => [
            'schema' => 'https://schema.datacite.org/meta/kernel-4.1/metadata.xsd',
            'namespace' => 'http://datacite.org/schema/kernel-4',
            'pipeline' => [
                'easydb',
                '../../xslt/easydb2datacite.xsl',
            ]
        ]
    ]
]);

// handle request
$request = GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = $proxy($request)->wait();

// emit response
header_remove();

foreach ($response->getHeaders() as $key => $value) {
    if ($key != 'Transfer-Encoding') {
        header("$key: $value[0]");
    }
}

http_response_code($response->getStatusCode());
echo $response->getBody();
