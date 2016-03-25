<div class='panel panel-default'>
    <div class="panel-body">
        
        <div class='preview-container'>

            <figure>
                <a href='{{$arrArtifact.thumb_url}}' title='{{q($arrArtifact.title)}}'>
                    <img src="{{$arrArtifact.thumb_url}}" alt='{{q($arrArtifact.title)}}' class='preview' />
                </a>
                <div class="inline-title">{{q($arrArtifact.title)}}</div>
                {{IF $arrArtifact.description}}
                    <figcaption>{{this::html($arrArtifact.description)}}</figcaption>
                {{END if-list}}
            </figure>
        </div>
        {{IF $arrArtifact.adults_only == 0}}
            <div class='puppy'>
                <script type="text/javascript">
                    (function(window, document, Adform){
                        window._adform = window._adform || [];
                        _adform.push(['5562917.on.init', function (settings) {
                            var flashvars = settings.html.flashvars;
                            flashvars.pmpId = 93028;
                        }]);
                    })(window, document, (Adform = window.Adform || {}));
                </script>
                <script data-adfscript="track.adform.net/adfscript/?bn=5562917">
                    (function(c,b,e,a,d){
                        c.getElementById("adform-adf"+b)||(a=c.createElement(b),
                        a.type="text/java"+b,a.async=a.defer=!0,a.id="adform-adf"+b,
                        a.src="http"+e+"://s1.adform.net/banners/scripts/adfscript.js?"+Math.round(new Date/6E4),
                        (d=c.getElementsByTagName(b)[0]).parentNode.insertBefore(a,d))
                    })(document,"script","https:"==location.protocol?"s":"");
                </script>
            </div>
        {{END if-list}}
        {{IF $arrAdverts.strBottom}}
            <div class='puppy'>{{$arrAdverts.strBottom}}</div>
        {{END if-list}}
        <div class='row actions-bar'>

            <div class='col-md-2 voting-container'>
                <div class="voting btn-group" role="group" aria-label="Głosowanie">
                    <button type="button" class="like btn btn-success"><span class='glyphicon glyphicon-thumbs-up'></span></button>
                    <button type="button" class="dislike btn btn-danger"><span class='glyphicon glyphicon-thumbs-down'></span></button>
                </div>
                <div class="loading-inline"></div>
            </div>
            <div class='col-md-3'>
                <strong><span id='artifact-likes'>{{$arrArtifact.likes}}</span> polubień</strong><br />
                <small>{{$arrArtifact.shows_count_fake}} wyświetleń</small>
            </div>
            <div class='col-md-7 text-right'>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        Więcej opcji
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" class='embeed-item' tabindex="-1" href="javascript:void(0);"><span class='icon glyphicon glyphicon-paperclip'></span> Pobierz kod</a></li>
                        <li role="presentation"><a role="menuitem" class='send-by-email' tabindex="-1" href="mailto:?subject=Super%20obrazek%21%20Zobacz%20-%20{{$arrArtifact.title}}&body=Znalaz%C5%82em%20%C5%9Bwietny%20obrazek%20na%20{{this::url('Homepage')}}%20%3AD%20Zobacz%3A%20{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}"><span class='icon glyphicon glyphicon-envelope'></span> Wyślij e-mailem</a></li>
                        <li role="presentation"><a role="menuitem" class='report-item' tabindex="-1" href="javascript:void(0);"><span class='icon glyphicon glyphicon-flag'></span> Zgłoś nadużycie</a></li>
                    </ul>
                </div>
            </div>
            <div class='row artifact-tags'>
                <div class='col-md-12 text-center'>
                    {{BEGIN arrTags}}
                    <a href='{{this::url('Listing::tag', $slug)}}' title='Tag {{q($name)}}' class='tag badge'>{{$name}}</a>
                    {{END}}
                </div>
            </div>
            <div class='col-md-12 share-socials text-right'>
                <div class="fb-like" data-href="{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
                <div class="g-plus" data-action="share" data-href="{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}" data-annotation="vertical-bubble" data-height="60"></div>
                <script>
                    window.___gcfg = {lang: 'pl'};

                    (function () {
                        var po = document.createElement('script');
                        po.type = 'text/javascript';
                        po.async = true;
                        po.src = 'https://apis.google.com/js/platform.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(po, s);
                    })();
                </script>
                
                <a class="twitter-share-button"href="https://twitter.com/intent/tweet?url={{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}" data-count="vertical">Tweet</a>
                <script>!function (d, s, id){
                    var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}
                    }(document, 'script', 'twitter-wjs');</script>
                    
                <script>
                // wykopywarka wersja standardowa (59x60)
                var wykop_url=location.href;// Link do strony
                var wykop_title=encodeURIComponent(document.title);	// Tytuł strony (pobierany z <title>)
                var wykop_desc=encodeURIComponent('Przykładowy opis');
                var widget_bg='FFFFFF';
                var widget_type='normal2';
                var widget_url='http://www.wykop.pl/dataprovider/diggerwidget/?url='+encodeURIComponent(wykop_url)+'&title='+(wykop_title)+'&desc='+(wykop_desc)+'&bg='+(widget_bg)+'&type='+(widget_type);
                document.write('<iframe src="'+widget_url+'" style="border:none;width:56px;height:60px;overflow:hidden;margin:0;padding:0;" frameborder="0" border="0"></iframe>');
                </script>
                <a href="//pl.pinterest.com/pin/create/button/?url={{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}" data-pin-do="buttonPin" data-pin-config="above" data-pin-color="red">
                    <img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_28.png" />
                </a>
            </div>
        </div>

    </div>
</div>
<div class="fb-comments" data-href="{{$strCurrentUrl}}" data-width="100%" data-numposts="25"></div>
<div class='file-data'>
    <ul class="list-group">
            
        {{IF arrArtifactAuthor}}
            <li class="list-group-item">Autor: <span class='value'><a href="{{this::url('User::profile', $arrArtifactAuthor.username)}}" title="{{q($arrArtifactAuthor.username)}} - obrazki">{{q($arrArtifactAuthor.username)}}</a></span></li>
        {{END if-list}}
        {{IF $arrArtifact.add_date}}
            <li class="list-group-item">Data dodania: <span class='value'>{{this::prettyDate($arrArtifact.add_date)}}</span></li>
        {{END if-list}}
        {{IF $arrArtifact.arrViewHelper.showResolution}}
            <li class="list-group-item">Oryginalna rozdzielczość: <span class='value'>{{$arrArtifact.width}}x{{$arrArtifact.height}} px</span></li>
        {{END if-list}}
        {{IF $arrArtifact.image_weight_kb}}
            <li class="list-group-item">Waga obrazka: <span class='value'>{{$arrArtifact.image_weight_kb}} kB</span></li>
        {{END if-list}}
        {{IF $arrArtifact.arrExifInfo.strCreatedBy}}
            <li class="list-group-item">
                Typ aparatu: <span class='value'>{{$arrArtifact.arrExifInfo.strCreatedBy}}</span>
            </li>
        {{END if-list}}
        {{IF $arrArtifact.arrExifInfo.strExposure}}
            <li class="list-group-item">
                Ekspozycja: <span class='value'>{{$arrArtifact.arrExifInfo.strExposure}}</span>
            </li>
        {{END if-list}}
        {{IF $arrArtifact.arrExifInfo.strAperture}}
            <li class="list-group-item">
                Przesłona: <span class='value'>{{$arrArtifact.arrExifInfo.strAperture}}</span>
            </li>
        {{END if-list}}
        {{IF $arrArtifact.arrExifInfo.strIso}}
        <li class="list-group-item">
            ISO: <span class='value'>{{$arrArtifact.arrExifInfo.strIso}}</span>
        </li>
        {{END if-list}}
        {{IF $arrArtifact.arrExifInfo.strCreateDateTime}}
        <li class="list-group-item">
            Data wykonania: <span class='value'>{{this::prettyDate($arrArtifact.arrExifInfo.strCreateDateTime)}}</span>
        </li>
        {{END if-list}}
    </ul>
</div>


<div id="modal-embeed" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Pobierz kod obrazka</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <strong>Link do tej strony</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{ this::url('Details', $arrArtifact.slug, $arrArtifact.id) }}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Link bezpośredni do obrazka</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{$arrArtifact.thumb_url}}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Umieść na swojej stronie obrazek</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{$strShareWeb}}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Obrazek w BBCode</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='[img]{{$arrArtifact.thumb_url}}[img]' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Obrazek z linkiem w BBCode</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='[url={{ this::url('Details', $arrArtifact.slug, $arrArtifact.id) }}][img]{{$arrArtifact.thumb_url}}[/img][/url]' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="modal-report" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Zgłoś nadużycie</h4>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">

                    <div class="form-group required">
                        <label for="input-name" class="col-sm-4 control-label">Twoje imię</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" required="required" value='' id="input-name" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-email" class="col-sm-4 control-label">Twój adres e-mail</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" required="required" value='' id="input-email" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-url" class="col-sm-4 control-label">Adres URL obrazka</label>
                        <div class="col-sm-8">
                            <input type="url" class="form-control" required="required" readonly='readonly' value='{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}' id="input-url" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-reason" class="col-sm-4 control-label">Wyjaśnienie zgłoszenia</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" required="required" id='input-reason' rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-link" data-dismiss="modal">Zrezygnuj</button>
                    <button type="submit" class="btn btn-primary">Wyślij zgłoszenie</button>
                </div>
            </form>
        </div>
    </div>
    <input type='hidden' readonly='readonly' id='artifact-id' value='{{$arrArtifact.id}}' />
    <input type='hidden' readonly='readonly' id='vote-environment' value='{{$numEnvironment}}' />
</div>


<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>