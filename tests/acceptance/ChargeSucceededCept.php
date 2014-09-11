<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('pay 800 yen with my card');

$I->amOnPage('/');
$I->fillField('amount', '800');

// ボタンがJSでロードされるのを待つ
$I->waitForElement('#WP_checkoutBox', 5);
// テストモードを有効にし、カードデータを送信しない
$I->executeJS('WebPay.testMode = true');

// ダイアログを開いてカード情報を入力
$I->click('カードで支払う');
$I->fillField('#WP_cardNumber', '4242 4242 4242 4242');
$I->selectOption('#WP_expMonth', '12');
$I->selectOption('#WP_expYear', '19');
$I->fillField('#WP_name', 'TEST TEST');
$I->fillField('#WP_cvc', '123');

// 次の画面遷移で呼ばれるAPIのモック
$I->pushMockResponse(201, [
    'id' => 'ch_abcdefghijklmno',
    'object' => 'charge',
    'livemode' => false,
    'currency' => 'jpy',
    'description' => 'PHP からのアイテムの購入',
    'amount' => 800,
    'amount_refunded' => 0,
    'customer' => null,
    'recursion' => null,
    'created' => time(),
    'paid' => true,
    'refunded' => false,
    'failure_message' => null,
    'card' => [
        'object' => 'card',
        'exp_year' => 2019,
        'exp_month' => 12,
        'fingerprint' => '215b5b2fe460809b8bb90bae6eeac0e0e0987bd7',
        'name' => 'TEST TEST',
        'country' => 'JP',
        'type' => 'Visa',
        'cvc_check' => 'pass',
        'last4' => '4242'
    ],
    'captured' => true,
    'expire_time' => null,
    'fees'
]);

$I->click('#WP_sendButton');

$I->see('お支払いありがとうございました');
$I->see('お支払い金額: 800');
$I->see('カード名義: TEST TEST');
$I->see('カード番号: ****-****-****-4242');
