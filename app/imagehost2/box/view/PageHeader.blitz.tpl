<div class="topclaim jumbotron">
    <div class="container">
        {{IF $boolIncludeFacebook}}
            <div class="header-fb">
                <div class="fb-like" data-href="https://www.facebook.com/imgedPL" data-layout="box_count" data-action="like" data-show-faces="true" data-share="true"></div>
            </div>
        {{END if-list}}
        <h1>{{q($strHeading)}}</h1>
    </div>
</div>