@extends("layout")

@section('body')
<div class="row marketing">

    <div class="commodity-list">
        <table class="table">
            <tr>
                <th>商品名称</th>
                <th>商品价格</th>
                <th>操作</th>
            </tr>
            @foreach ($commoditys as $data)
            <tr>
                <td class="commodity-name"><a href="/info/{{ $data['id'] }}">{{ $data['name'] }}</a></td>
                <td>{{ $data['price'] }}</td>
                <td>
                    <a href="javascript:;" onclick="document.getElementById('{{ $data['name'] }}-{{ $data['id'] }}').submit();">加入购物车</a>
                    <form action="/shopcar/add" method="post" id="{{ $data['name'] }}-{{ $data['id'] }}">
                        <input type="hidden" name="id" value="{{ $data['id'] }}">
                    </form>
                </td>
            </tr>
            @endforeach
        </table>

    </div>
    <div class="pagination col-lg-12">
        @if ($preview-1 >= 0)
        <a href="?page={{ $preview }}">上一页</a>
        @endif
        @if (count($commoditys) < $limit || count($commoditys) != $next)
        @else
        <a href="?page={{ $next }}" class="pull-right">下一页</a>
        @endif
    </div>
</div>
@endsection