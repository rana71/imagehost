
<div class="row">
    <div class="col-sm-4 col-xs-6">

        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-hourglass"></i></div>
            <span class="num" data-start="0" data-end="{{$arrStats.numTotalNotActivated}}" data-postfix="" data-duration="1500" data-delay="0">0</span> 

            <h3>Nieaktywowanych</h3>
            <p>&nbsp;</p>
        </div>

    </div>

    <div class="col-sm-4 col-xs-6">

        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-check"></i></div>
            <div class="num" data-start="0" data-end="{{$arrStats.numTotalActivated}}" data-postfix="" data-duration="1500" data-delay="600">0</div>

            <h3>Aktywowanych</h3>
            <p>&nbsp;</p>
        </div>

    </div>
            
    <div class="col-sm-4 col-xs-6">

        <div class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-trophy"></i></div>
            <div class="num" data-start="0" data-end="{{$arrStats.numTotalUsersArtifacts}}" data-postfix="" data-duration="1500" data-delay="600">0</div>

            <h3>Artefaktów</h3>
            <p>dodanych przez użytkowników</p>
        </div>

    </div>

</div>

<br />