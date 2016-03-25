<div class='m-labels-list'>
    
    <h1>Najpopularniejsze tematy</h1>
    
    <ul class='list'>
        {{BEGIN arrTags}}
        <li>

            <a href="{{this::url('Listing::tag', $slug)}}" title='Tag {{q($title)}}'>{{q($title)}}</a>
            <small>{{$elements_count}} elementów</small>
        </li>
        {{END}}
    </ul>

</div>