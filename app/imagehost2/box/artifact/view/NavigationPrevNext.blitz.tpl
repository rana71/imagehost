<div class='row artifact-navigation'>
    <div class="navigation btn-group col-xs-12" role="group" aria-label="Nawigacja">
        {{IF $strUrlPrev}}
            <a href='{{$strUrlPrev}}' title='Zobacz poprzedni' class="btn btn-default pull-left">
                <span class='glyphicon glyphicon-hand-left'></span> Poprzedni
            </a>
        {{END if-list}}
        {{IF $strUrlNext}}
            <a href='{{$strUrlNext}}' title='Zobacz następny' class="btn btn-success pull-right">
                Następny <span class='glyphicon glyphicon-hand-right'></span>
            </a>
        {{END if-list}}
    </div>
</div>
    