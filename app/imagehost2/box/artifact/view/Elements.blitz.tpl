{{IF $strMainDescription}}
    <blockquote>{{$strMainDescription}}</blockquote>
{{END if-list}}
{{BEGIN arrElements}}
    <figure class="item-element">
        {{IF $type == 1 }}
            <a href='{{$thumb_url}}' title='{{q($title)}}'>
                <img src="{{$thumb_url}}" alt='{{q($title)}}' class='preview' />
            </a>
        {{ELSEIF $type == 2}}
            <iframe width="718" height="470" src="https://www.youtube.com/embed/{{$youtube_id}}" frameborder="0" allowfullscreen></iframe>
        {{ELSEIF $type == 3}}
            <a href='{{$thumb_url}}' title='{{q($title)}}'>
                <img src="{{$thumb_url}}" alt='{{q($title)}}' class='preview' />
            </a>
            <div class='meme-options'>
                <a href='{{this::url('Upload::memeGenerator')}}#meme-background:{{$meme_background_id}}' title='Stwórz mema z tego zdjęcia'>
                    <button type="button" class="btn btn-default btn-xs">Stwórz swojego mema</button>
                </a>
            </div>
        {{END if-list}}
        <div class="inline-title">{{q($title)}}</div>
        {{IF $description}}
            <figcaption>{{this::html($description)}}</figcaption>
        {{END if-list}}
    </figure>
    {{IF $_first}}
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
    {{end if-list}}
{{END}}