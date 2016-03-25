<div class="fb-like" data-href="{{$strPageUrl}}" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
<div class="g-plus" data-action="share" data-href="{{$strPageUrl}}" data-annotation="vertical-bubble" data-height="60"></div>
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

<a class="twitter-share-button"href="https://twitter.com/intent/tweet?url={{$strPageUrl}}" data-count="vertical">Tweet</a>
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
<a href="//pl.pinterest.com/pin/create/button/?url={{$strPageUrl}}" data-pin-do="buttonPin" data-pin-config="above" data-pin-color="red">
    <img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_28.png" />
</a>