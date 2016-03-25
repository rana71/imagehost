<div class='panel panel-default'>
    <div class="panel-body">
        {{IF $arrItems}}
        <div class='small-list'>
            <ul>
                {{BEGIN arrItems}}
                    <li class='clearfix'>
                        <a href="{{this::url('Details', $slug, $id)}}" title='{{q($title)}}'>
                            <div class="image">
                                <img class="media-object" src="{{$thumb_url}}" alt="{{q($title)}}" />
                            </div>
                            <div class="body">
                                <h4>{{q($title)}}</h4>
                            </div>
                        </a>
                    </li>
                {{END}}
            </ul>
        </div>
        {{END if-list}}

    </div>
</div>