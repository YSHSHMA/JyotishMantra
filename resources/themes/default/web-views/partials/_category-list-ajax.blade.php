@if($category->childes->count()>0)
    @foreach($category->childes as $categoryChild)
        <div class="col-md-3 mt-4">
            <label class="text-center get-view-by-onclick category-list-in-header"
                   data-link="{{ route('products-slug',['slug'=> $categoryChild['slug'], 'data_from'=>'category','page'=>1]) }}">
                {{$categoryChild['name']}}
            </label>
            <ul class="list-group">
                @foreach($categoryChild->childes as $child)
                    <li class="list-group-item cursor-pointer get-view-by-onclick"
                        data-link="{{ route('products-slug',['slug'=> $child['slug'], 'data_from'=>'category','page'=>1]) }}">
                        {{$child['name']}}
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
@else
    <div class="col-md-12 text-center mt-5">
        <a href="{{route('products-slug',['slug'=> $category['slug'], 'data_from'=>'category','page'=>1])}}"
           class="btn btn--primary">{{ translate('view_Products') }}</a>
    </div>
@endif
