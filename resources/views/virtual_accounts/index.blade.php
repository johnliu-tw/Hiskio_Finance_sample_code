<input type="button" id="submit" value="測試虛擬帳號(簡易)">
<input type="button" id="advance_submit" value="測試虛擬帳號(進階)">
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

  $('#advance_submit').click(function(){
        var data = {
            "data": JSON.stringify({
                "acc": "000000000000",
                "date": "20160301",
                "txseq": "CCH0000000000000",
                "ubnotify": "record",
                "amt": "00000000124500",
                "wdacc": "0000000000000000",
                "wdbank": "000",
                "stan": "9896700154",
                "to": "10169",
                "time": "164456",
                "ecacc": "10169123456789",
                "status": "0",
                "txnid": "14I5"
            }),
            "signature": "lvP+BOPzBsN3pkuEyeTbqmDXR+rqqkR0PqWMDN8/WZk176Gw3ptecHcU8RBo0/fa6cli1E+Rqqu1tzq/ekEH6rI/nZCE5wBKpKsyS6uID8K0AeXF5XRzdNrcj4avpLadLkVWaSrut06QaO2QHv6paPnBjIQY7XMl0UfzJLW/5JTyeleofyqvPZo6MBvXI6y1sWIYKw0EOk46hawpZWfbPX7QQ9pfSg3DcFantms+9SeaFvCD1YZ5qy01eryErsyIeJzbOcFBak17aWRNLckaXtcTbdMru/vnh/UMkO5Td/3NOtwTNUqp3OvKeh51ikhPCMHB5I1EgXt4RTuRccMXXg==",
            "mac": "D5wPflgGypoRoNcmPelFt0/i298n4FfS5bsDy5XAR26UVwdXavSmz8SmcUjt0RZQ"
        }
        $.post('/virtualAccounts/advanced',JSON.stringify(data),function (res) {})
    })
</script>
