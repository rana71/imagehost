{{if $arrNavigation}}
<div class='row artifact-navigation'>
    <div class="navigation btn-group col-xs-12" role="group" aria-label="Nawigacja">
        {{IF $arrNavigation.arrPrev}}
            <a href='{{$arrNavigation.arrPrev.href}}' title='{{q($arrNavigation.arrPrev.title)}}' class="btn btn-default pull-left">
                <span class='glyphicon glyphicon-hand-left'></span> Poprzedni
            </a>
        {{END if-list}}
        {{IF $arrNavigation.arrNext}}
            <a href='{{$arrNavigation.arrNext.href}}' title='{{q($arrNavigation.arrNext.title)}}' class="btn btn-success pull-right">
                Następny <span class='glyphicon glyphicon-hand-right'></span>
            </a>
        {{END if-list}}
    </div>
</div>
{{END if-list}}

{{IF $strAdverts}}
    <div class='puppy'>{{$strAdverts}}</div>
{{END if-list}}

<div class='panel panel-default'>
    <div class="panel-body">
        {{IF $arrAnothers}}
        <div class='small-list'>
            <ul>
                {{BEGIN arrAnothers}}
                    <li class='clearfix'>
                        <a href="{{$href}}" title='{{q($title)}}'>
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