<div class="navigation-thumbs">
    <h6>Zobacz inne:</h6>
    <ul>
        {{BEGIN arrItems}}
            <li>
                <a href="{{this::url('Details', $slug, $id)}}" title="{{q($title)}}">
                    <div class="thumb">
                        <img src="{{$thumb_url}}" alt="{{q($title)}}" />
                    </div>
                    <p>{{q($title)}}</p>
                </a>
            </li>
        {{END if-list}}
    </ul>
</div>
