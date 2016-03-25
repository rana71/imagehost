<div class='m-labels-list'>
    
    <h1>Najnowsze wyszukiwania</h1>

    {{IF arrQueries}}
        <ul class='list'>
            {{BEGIN arrQueries}}
            <li class='col-md-4 col-sm-6'>

                <a href="{{this::url('Listing::query', $slug)}}" title='{{q($title)}}'>{{q($title)}}</a>
                <small>{{this::prettyDateTime($last_use_timestamp)}}</small>
            </li>
            {{END}}
        </ul>
    {{ELSE}}
        <p class="empty">Brak wyszukiwań</p>
    {{end if-list}}

</div>