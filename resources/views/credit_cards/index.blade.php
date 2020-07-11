<input id="export" type="button" value="匯出">
<input type="file" id="file">
<input type="button" id="import" value="匯入">

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script>
  $('#export').click(function(){
    window.location.href = 'http://localhost:8001/creditCards/export'
  })
  $('#import').click(function(){
    var form = new FormData();
    var file = $('#file')[0].files[0]
    form.append("file", file);

    $.ajax({
        method: "POST",
        url: '/creditCards/import',
        processData: false,
        mimeType: "multipart/form-data",
        contentType: false,
        data: form
    }).done(function( msg ) {
      console.log(msg)
    });
  })

</script>