<div class="informations">
    <div class="row">
        <div class="stats">{{$arrStats.shows_count}} wyświetleń</div>
        <div class="global-options">
            <span class="code">Pobierz link</span>
            <a class="email" href='mailto:?subject=Super%20obrazek%21%20Zobacz%20-%20{{$strArtifactTitle}}&body=Znalaz%C5%82em%20%C5%9Bwietny%20obrazek%20na%20{{this::url('Homepage')}}%20%3AD%20Zobacz%3A%20{{$strPageUrl}}' title="Wyślij e-mailem">Wyślij e-mailem</a>
            <span class="abuse">Zgłoś nadużycie</span>
        </div>
    </div>
    
    <ul class="tags">
        {{BEGIN arrTags}}
         <li><a href='{{this::url('Listing::tag', $slug)}}' title='Tag {{q($title)}}'>{{$title}}</a></li>
        {{END}}
    </ul>
    
    <div class="share">
        <div class="fb-like" data-href="{{$strPageUrl}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
        <div class="fb-share-button" data-href="{{$strPageUrl}}" data-layout="button_count"></div>
        <!--<a class="twitter-share-button" href="https://twitter.com/intent/tweet?url={{$strPageUrl}}" data-count="horizontal">Tweet</a>
        <script>
            var wykop_url=location.href;
            var wykop_title=encodeURIComponent(document.title);
            document.write('<iframe src="http://www.wykop.pl/dataprovider/diggerwidget/?url='+encodeURIComponent(wykop_url)+'&title='+(wykop_title)+'&bg=FFFFFF&type=compact2" style="border:none;height:20px; width: 90px; overflow:hidden;margin:0;padding:0;" frameborder="0" border="0"></iframe>');
        </script>
        <a href="//pl.pinterest.com/pin/create/button/?url={{$strPageUrl}}" data-pin-color="red" style="position:relative; bottom: 7px; ">
            <img src="/imagehost3/img/pinit_fg_en_rect_red_20.png" />
        </a>
        <div class="g-plusone" data-size="medium" data-href="{{$strPageUrl}}"></div>-->
    </div>
</div>

{{IF $strAddTimestamp || $arrAuthor.display_name}}
    <div class='informations-gray'>
        {{IF $strAddTimestamp}}
            Data dodania: <span class="v">{{this::prettyDateTime($strAddTimestamp)}}</span><br />
        {{END if-list}}
        {{IF $arrAuthor.display_name}}
            Autor: <a class='v' href="{{this::url('Listing::userUploads', $arrAuthor.display_name)}}" title="{{q($arrAuthor.display_name)}} - obrazki">{{q($arrAuthor.display_name)}}</a><br />
        {{END if-list}}
   </div>
{{END if-list}}


<div id="modal-embeed" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Pobierz link tej do tej strony</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class='r'>
                        <strong>Bezpośredni link do tej strony</strong>
                        <input type='text' readonly='readonly' class='form-control' value='{{$strPageUrl}}' onfocus='javascript:this.select();' />
                    </div>
                    <div class='r'>
                        <strong>Umieść link do tej strony</strong>
                        <input type='text' readonly='readonly' class='form-control' value='<a href="{{$strPageUrl}}" title="{{$strArtifactTitle}} - galeria na imgED">{{$strArtifactTitle}} - galeria na imgED</a>' onfocus='javascript:this.select();' />
                    </div>
                </form>
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
            <form>
                <div class="modal-body">
                    
                     <div class='r'>
                        <label for='input-name'>Twoje imię *</label> 
                        <input  class='req' id='input-name' type='text' placeholder='Twoje imię *' />
                    </div>
                    
                    <div class='r'>
                        <label for='input-email'>Twój adres e-mail *</label> 
                        <input  class='req' id='input-email' type='email' placeholder='Twój adres e-mail *' />
                    </div>
                    
                    <div class='r'>
                        <label for='input-url'>Zgłaszany adres URL *</label> 
                        <input  class='req' id='input-url' type='url' placeholder='Zgłaszany adres URL *' />
                    </div>
                    
                    <div class='r'>
                        <label for='input-reason'>Wyjaśnienie zgłoszenia *</label> 
                        <textarea class='req' id='input-reason' rows='5' placeholder='Wyjaśnienie zgłoszenia *'></textarea>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <div class='b'>
                        <input type="button" class="cancel" data-dismiss="modal" value='Zrezygnuj' />
                        <input type="submit" disabled='disabled' data-value='Wyślij zgłoszenie' value='Ładuję formularz...' />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <input type='hidden' readonly='readonly' id='artifact-id' value='{{$arrArtifact.id}}' />
    <input type='hidden' readonly='readonly' id='vote-environment' value='{{$numEnvironment}}' />
</div>