<button class="btn btn-primary" id="submit">付款</button>
<input id="transactionId">
<button class="btn btn-success" id="confirm">確認付款</button>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script>
  $('#submit').click(function(){
    $.ajax({
      url: "/LinePay/request",
      method: "POST",
    }).done(function( msg ) {
      msg = JSON.parse(msg)
      if(msg.returnCode == '0000'){
        alert('付款成功! 交易序號為:' + msg.info.transactionId)
        window.location.href = msg.info.paymentUrl.web
      }
    });
  })

  $('#confirm').click(function(){
    $.ajax({
      url: "/LinePay/confirm",
      method: "POST",
      data: {transactionId: $('#transactionId').val()}
    }).done(function( msg ) {
      msg = JSON.parse(msg)
      if(msg.returnCode == '0000'){
        alert('確認成功!')
      }
      else{
        alert('錯誤代碼 ' + msg.returnCode)
      }
    });
  })
</script>