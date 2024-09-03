<?php
require 'vendor/autoload.php';

// Jira email and API token
$email = 'your-email@example.com';
$apiToken = 'your-api-token';

// Encode the email and API token for Basic Authentication
$basicAuthToken = base64_encode("$email:$apiToken");

$headers = array(
    'Accept' => 'application/json',
    'Authorization' => 'Basic ' . $basicAuthToken
);

$response = Unirest\Request::get(
    'https://your-domain.atlassian.net/rest/servicedeskapi/servicedesk/{serviceDeskId}/queue/{queueId}',
    $headers
);

var_dump($response);
