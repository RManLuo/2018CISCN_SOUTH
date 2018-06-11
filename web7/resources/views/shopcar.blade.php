@extends("layout")

@section('body')
    <div class="jumbotron">
       <h1>购物车</h1>
        @php
            try {
        @endphp
        @if ($commodity)
        <div class="shopcar_list">
            <ul class="list-group">
                <li class="list-group-item">{{ $commodity['name'] }} / {{ $commodity['price'] }}</li>

            </ul>
            <form action="" method="post">
                <input type="hidden" name="price" value="{{ $commodity['price'] }}">
                <button class="btn btn-danger" type="submit">结算</button>
            </form>
        </div>
        @endif
        @php
            } catch (\ErrorException $e) {

            }
        @endphp
    </div>
@endsection