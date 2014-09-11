<?php
require_once 'config.php';
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>WebPay PHP sample</title>
  </head>
  <body>
    <h1>WebPay PHP sample</h1>
    <form action="/charge.php" method="post">
      <input type="number" name="amount" value="300" /> 円を支払います。<br />

<!-- 御自身のサーバにクレジットカード情報を送信すると、クレジットカード情報を適切に扱う義務が生じます。
     JavaScript を利用して webpay token を生成することで、クレジットカード情報を直接あつかわずに済みます。
     webpay-token という name を持つ input が自動的に追加されます。 -->
      <script src="https://checkout.webpay.jp/v2/" class="webpay-button"
              data-lang="ja"
              data-key="<?php print(PUBLIC_KEY); ?>"></script>
    </form>
  </body>
</html>
