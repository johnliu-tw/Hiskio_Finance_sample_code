<input type="button" id="submit" value="測試虛擬帳號(簡易)">
<!-- reversal -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
  $('#submit').click(function(){
      var data = `
          <PaySvcRq>
          <PmtAddRq>
          <TDateSeqNo>20100310000029216</TDateSeqNo>
          日期+序號)
          <TxnDate>20100310</TxnDate>
          <TxnTime>080000</TxnTime>
          <ValueDate>20100310</ValueDate>
          <TxAmount>850</TxAmount>
          <BankID>0081000</BankID>
          <ActNo>00708804344</ActNo>
          <MAC></MAC>
          <PR_Key1>9216813322423450</PR_Key1>
          </PmtAddRq>
          </PaySvcRq>
      `;
      $.post('/virtualAccounts/normal',data, function (res) {})
  })
</script>
