<?php
namespace Codeception\Module;

require_once(__DIR__ . '/../../vendor/autoload.php');
use Predis\Client;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
    private $redisClient;
    private $lastRequest;

    public function __construct()
    {
        $this->redisClient = new \Predis\Client();
    }

    // テストケース完了時に呼び出される
    public function _after(\Codeception\TestCase $test)
    {
        $this->redisClient->del('mdl_webpay_test_requests');
        $this->redisClient->del('mdl_webpay_test_responses');
    }

    // ダブルサーバが受信したリクエストの最初のものを取り出す
    public function loadRequest()
    {
        $data = $this->redisClient->lpop('mdl_webpay_test_requests');
        if ($data === null) {
            throw new \Exception('Tried to load a request but no more request is recorded');
        }
        $this->lastRequest = unserialize($data);
    }

    // いまロードしているリクエストの内容を確認
    public function seeRequestTo($method, $path)
    {
        $this->assertEquals($method, $this->lastRequest['method']);
        $this->assertEquals($path, $this->lastRequest['request_uri']);
    }
    public function seeInData($key, $value = null)
    {
        $data = json_decode($this->lastRequest['body'], true);
        if ($value === null) {
            $this->assertNotEmpty($data[$key]);
        } else {
            \PHPUnit_Framework_Assert::assertSame($value, $data[$key]);
        }
    }
    public function dontSeeInData($key)
    {
        $data = json_decode($this->lastRequest['body'], true);
        $this->assertEmpty($data[$key]);
    }

    // ダミーレスポンスを追加
    public function pushMockResponse($statusCode, $body)
    {
        $data = array('status_code' => $statusCode, 'body' => json_encode($body));
        $this->redisClient->rpush('mdl_webpay_test_responses', serialize($data));
    }
}
