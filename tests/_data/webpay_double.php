<?php
// Mock server to pool requests and responses
// Test cases add stub responses then rake actual requests
// Use redis to communicate with the test

require_once(__DIR__ . '/../../vendor/autoload.php');

$errorResponseJson = '{"error":{"message":"Mock response is not prepared","type":"api_error","caused_by":"service"}}';

$client = new Predis\Client();
$request = array(
    'method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'body' => file_get_contents("php://input"),
);
$client->rpush('mdl_webpay_test_requests', serialize($request));
$head = $client->lpop('mdl_webpay_test_responses');
if ($head === NULL) {
    http_response_code(500);
    header('Content-Type: application/json');
    print($errorResponseJson);
} else {
    $response = unserialize($head);
    http_response_code($response['status_code']);
    header('Content-Type: application/json');
    print($response['body']);
}
