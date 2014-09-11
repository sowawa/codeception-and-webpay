<?php
require_once '../vendor/autoload.php';
use WebPay\WebPay;
require_once 'config.php';

// 支払金額。実際には商品番号などを送信し、それに対応する金額をデータベースから引きます
$amount = $_POST['amount'];
// トークン
$token = $_POST['webpay-token'];

// WebPayインスタンスを非公開鍵で作成
// 宛先サーバを configurable にする
$apiBase = getenv('WEBPAY_API_BASE');
$webpay = $apiBase ? new WebPay(SECRET_KEY, ['api_base' => $apiBase]) : new WebPay(SECRET_KEY);

try {
    // 決済を実行
    $result = $webpay->charge->create(array(
       "amount" => intval($amount, 10),
       "currency"=>"jpy",
       "card" => $token,
       "description" => "PHP からのアイテムの購入"
    ));
    // 以下エラーハンドリング
} catch (\WebPay\ErrorResponse\CardException $e) {
    $data = $e->getData()->error;
    // カードが拒否された場合
    header('Content-Type: text/plain; charset=utf-8');
    print('Status is:' . $e->getStatus() . "\n");
    print('Type is:' . $data->type . "\n");
    print('Code is:' . $data->code . "\n");
    print('Param is:' . $data->param . "\n");
    print('Message is:' . $data->message . "\n");
    exit('Error');
} catch (\WebPay\ErrorResponse\InvalidRequestException $e) {
    // リクエストで指定したパラメータが不正な場合
    print("InvalidRequestException\n");
    print('Param is:' . $e->getParam() . "\n");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
} catch (\WebPay\ErrorResponse\AuthenticationException $e) {
    // 認証に失敗した場合
    print("AuthenticationException\n");
    print('Param is:' . $e->getParam() . "\n");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
} catch (\WebPay\ErrorResponse\ApiException $e) {
    // WebPayのサーバでエラーが起きた場合
    print("ApiException\n");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
} catch (\WebPay\ApiConnectionException $e) {
    // APIへの接続エラーが起きた場合
    print("ApiConnectionException\n");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
} catch (\WebPay\InvalidRequestException $e) {
    // リクエストで指定したパラメータが不正で、リクエストがおこなえなかった場合
    print("InvalidRequestException");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
} catch (\Exception $e) {
    // WebPayとは関係ない例外の場合
    print("Unexpected exception\n");
    print('Message is:' . $e->getMessage() . "\n");
    exit('Error');
}

// 処理終了後、 https://webpay.jp/test/charges で課金が発生したことが分かります。
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>WebPay PHP sample</title>
  </head>
  <body>
    <h1>お支払いありがとうございました</h1>
    <ul>
      <li>お支払い金額: <?php print($result->amount); ?></li>
      <li>カード名義: <?php print($result->card->name); ?></li>
      <li>カード番号: ****-****-****-<?php print($result->card->last4); ?></li>
    </ul>
  </body>
</html>
