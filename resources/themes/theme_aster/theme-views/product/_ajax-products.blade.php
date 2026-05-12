@if (count($products) > 0)
    <div class="auto-col gap-3 mobile_two_items minWidth-12rem {{(session()->get('product_view_style') == 'list-view'?'product-list-view':'')}}" id="filtered-products" style="{{(count($products) > 4?'--maxWidth:1fr':'--maxWidth:14rem')}}">
        @foreach($products as $product)
            @include('theme-views.partials._product-small-card', ['product'=>$product])
        @endforeach
    </div>
@else
    <div class="text-center pt-5 pb-4">
        <h2 class="text-capitalize">{{translate('no_product_found')}}</h2>
    </div>
@endif
@if (count($products) > 0)
<div class="my-4" id="paginator-ajax">
    {!! $products->links() !!}
</div>
@endif
