<div class='container'>
    <div class="row">
        <ul class='tags-list clearfix'>
            {{BEGIN arrTags}}
            <li class='col-md-4 col-sm-6'>
                
                <a href="{{this::url('Listing::tag', $slug)}}" title='Tag {{q($title)}}' class='badge'>{{q($title)}}</a>
                <small>{{$elements_count}} elementów</small>
            </li>
            {{END}}
        </ul>
    </div>
</div>