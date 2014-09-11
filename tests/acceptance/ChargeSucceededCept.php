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
$I->click('#WP_sendButton');

$I->see('お支払いありがとうございました');
$I->see('お支払い金額: 800');
$I->see('カード名義: TEST TEST');
$I->see('カード番号: ****-****-****-4242');
