<div class="row">
    <div class="col-sm-3 col-xs-6 stat-user">
        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-check"></i></div>
            <span class="num active_users" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.active_users.numValue}}</span> 
            <span style='color: #fff; font-size: 20px;'>/</span> 
            <span class="num inactive_users" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.inactive_users.numValue}}</span>
            <h3>Użytkowników</h3>
            <p>ostatnia aktualizacja: <strong class='update_date'>{{$arrStats.active_users.strLastRefreshDate}}</strong></p>
        </div>
    </div>

    <div class="col-sm-3 col-xs-6 stat-artifact">
        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-trophy"></i></div>
            <span class="num visible_artifacts" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.visible_artifacts.numValue}}</span> 
            <span style='color: #fff; font-size: 20px;'>/</span> 
            <span class="num invisible_artifacts" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.invisible_artifacts.numValue}}</span>
            <h3>Artefaktów</h3>
            <p>ostatnia aktualizacja: <strong class='update_date'>{{$arrStats.visible_artifacts.strLastRefreshDate}}</strong></p>
        </div>
    </div>

    <div class="col-sm-3 col-xs-6 stat-tag">
        <div class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-tag"></i></div>
            <span class="num active_tags" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.active_tags.numValue}}</span> 
            <span style='color: #fff; font-size: 20px;'>/</span> 
            <span class="num inactive_tags" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.inactive_tags.numValue}}</span>
            <h3>Tagów</h3>
            <p>ostatnia aktualizacja: <strong class='update_date'>{{$arrStats.active_tags.strLastRefreshDate}}</strong></p>
        </div>
    </div>

    <div class="col-sm-3 col-xs-6 stat-newsletter">
        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-check"></i></div>
            <span class="num active_newsletter_emails" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.active_newsletter_emails.numValue}}</span> 
            <span style='color: #fff; font-size: 20px;'>/</span> 
            <span class="num inactive_newsletter_emails" data-start="0" data-postfix="" data-duration="1500" data-delay="600">{{$arrStats.inactive_newsletter_emails.numValue}}</span>
            <h3>Subskrybentów newslettera</h3>
            <p>ostatnia aktualizacja: <strong class='update_date'>{{$arrStats.active_newsletter_emails.strLastRefreshDate}}</strong></p>
        </div>
    </div>

</div>
<br />