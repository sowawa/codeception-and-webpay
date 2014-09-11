<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('see descriptive error if my card is declined');

$I->amOnPage('/');
$I->fillField('amount', '800');

$I->waitForElement('#WP_checkoutBox', 5);
$I->executeJS('WebPay.testMode = true');

$I->click('カードで支払う');
$I->fillField('#WP_cardNumber', '4242 4242 4242 4242');
$I->selectOption('#WP_expMonth', '12');
$I->selectOption('#WP_expYear', '19');
$I->fillField('#WP_name', 'TEST TEST');
$I->fillField('#WP_cvc', '123');

// エラーレスポンスを返す
$I->pushMockResponse(402, [
    'error' => [
        'message' => 'このカードでは取引をする事が出来ません。利用出来ない理由をご契約中のカード会社へお問い合わせるか、他のカードをご利用ください。',
        'caused_by' => 'buyer',
        'type' => 'card_error',
        'code' => 'card_declined'
    ]
]);

$I->click('#WP_sendButton');

$I->see('Status is:402');
$I->see('Type is:card_error');
$I->see('Code is:card_declined');
$I->see('Message is:このカードでは取引をする事が出来ません。利用出来ない理由をご契約中のカード会社へお問い合わせるか、他のカードをご利用ください。');
