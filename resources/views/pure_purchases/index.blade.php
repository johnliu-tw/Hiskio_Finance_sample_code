<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<form class="container mx-auto w-25 pt-5" action="/purePurchases" method="post">
  <select name='productId' class="form-control form-control mt-3">
    @foreach( $products as $product )
      <option value='{{ $product->id }}'>{{ $product->name }}</option>
    @endforeach
  </select>
  <select name='method' class="form-control form-control mt-3">
    <option value='atm'>ATM</option>
    <option value='credit'>信用卡</option>
  </select>
  <button type="submit" class="btn btn-primary mt-3">純粹的送出</button>
</form>