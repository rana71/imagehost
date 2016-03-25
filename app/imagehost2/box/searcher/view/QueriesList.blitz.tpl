<div class='container'>
    <div class="row">
        {{IF arrQueries}}
            <ul class='tags-list clearfix'>
                {{BEGIN arrQueries}}
                <li class='col-md-4 col-sm-6'>

                    <a href="{{this::url('Listing::query', $slug)}}" title='{{q($title)}}' class='badge'>{{q($title)}}</a>
                    <small>{{this::prettyDateTime($last_use_timestamp)}}</small>
                </li>
                {{END}}
            </ul>
        {{ELSE}}
            <p class="empty">Brak wyszukiwań</p>
        {{end if-list}}
    </div>
</div>