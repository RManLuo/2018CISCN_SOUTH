@extends("layout")

@section('body')
<div class="jumbotron commodity-info">
   <h1>{{ $commodity['name'] }}</h1>
   <p class="lead" style="word-wrap:break-word">{{ $commodity['desc'] }}</p>
   <p>Price: {{ $commodity['price'] }}</p>
   <p>Amount: {{ $commodity['amount'] }}</p>
   <form action="/pay" method="post">
      <input type="hidden" name="price" value="{{ $commodity['price'] }}">
      <button class="btn btn-lg btn-success">Buy</button>
   </form>
</div>

@endsection