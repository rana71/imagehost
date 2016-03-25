<li>
    <figure class="element">
        <div class='thumb'>
            <div class="options-overlay">
                <div class="sharing">
                        <!--<div class='text'>Udostępnij:</div>
                        <div class="fb-like" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                        <div class="fb-share-button" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-layout="button_count"></div>
                        <a class="twitter-share-button" href="https://twitter.com/intent/tweet?url={{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-count="horizontal">Tweet</a>
                        <script>
                            var wykop_url=location.href;
                            var wykop_title=encodeURIComponent(document.title);
                            document.write('<iframe src="http://www.wykop.pl/dataprovider/diggerwidget/?url='+encodeURIComponent(wykop_url)+'&title='+(wykop_title)+'&bg=FFFFFF&type=compact2" style="border:none;height:20px; width: 90px; overflow:hidden;margin:0;padding:0;" frameborder="0" border="0"></iframe>');
                        </script>
                        <a href="//pl.pinterest.com/pin/create/button/?url={{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-pin-color="red" style="position:relative; bottom: 7px; ">
                            <img src="/imagehost3/img/pinit_fg_en_rect_red_20.png" />
                        </a>

                        <div class="g-plusone" data-size="medium" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}"></div>-->
                    </div>
                <ul>
                    <li>
                        <a class='preview' data-element-id='{{$arrElement.id}}' href='{{$arrElement.thumb_url}}' title='{{q($arrElement.title)}}' target="_blank">
                            <div class='l zoom'><span>Powiększ obrazek</span></div>
                        </a>
                    </li>
                    <li><a href="{{this::url('Upload::index')}}#przejmij-fote:{{base64_encode($arrElement.item_element_id)}}" title="Przejmij tę fotę" class='l grab-photo'><span>Przejmuj tę fotę</span></a></li>
                    <li><a href="{{this::currentUrl()}}#pobierz-kod-elementu:{{$arrElement.id}}" class='l get-code' title="Pobierz kod tego obrazka" data-element-id='{{$arrElement.id}}'><span>Pobierz kod tego obrazka</span></a></li>
                    <li><div class='l get-link'><span>Pobierz link do tej strony</span></div></li>
                    <li><a href="mailto:?subject=Super%20obrazek%21%20Zobacz%20-%20{{q($arrArtifact.title)}}&body=Znalaz%C5%82em%20%C5%9Bwietny%20obrazek%20na%20{{this::url('Homepage')}}%20%3AD%20Zobacz%3A%20{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" title="Wyślij e-mailem" class='l send-email'><span>Wyślij e-mailem link do tej strony</span></a></li>
                </ul>
            </div>
            <a href='{{$arrElement.thumb_url}}' title='{{q($arrElement.title)}}' target="_blank" class='object'>
                <img src="{{$arrElement.thumb_url}}" alt="{{q($arrElement.title)}}" />
            </a>
        </div>
        
        {{IF $arrElement.author}}
            <small>Autor zdjęcia: <i>{{$arrElement.author}}</i></small>
        {{END if-list}}
        <div class="info">
            <div class="title">
                <h2>{{q($arrElement.title)}}</h2>
                <div class="options">Opcje</div>
                <div class="options-tooltip">
                    <ul>
                        <li>
                            <a href="{{this::currentUrl()}}#pobierz-kod-elementu:{{$arrElement.id}}" class="element-code" title="Pobierz kod tego obrazka" data-element-id='{{$arrElement.id}}'>Pobierz kod obrazka</a>
                        </li>
                    </ul>
                    {{IF $arrElement.author || $arrElement.width || $arrElement.height || $arrElement.weight_kb || $arrElement.pretty_exif.strCreatedBy || $arrElement.pretty_exif.strExposure || $arrElement.pretty_exif.strAperture || $arrElement.pretty_exif.strIso || $arrElement.pretty_exif.strCreateDateTime}}
                        <div class="details">
                            <strong>Szczegóły obrazka</strong>
                            <ul>
                                {{IF $arrElement.author}}
                                <li>Autor zdjęcia: <span>{{$arrElement.author}}</span></li>
                                {{END if-list}}
                                {{IF $arrElement.width && $arrElement.height}}
                                    <li>Rozdzielczość: <span>{{$arrElement.width}}x{{$arrElement.height}} px</span></li>
                                {{END if-list}}
                                {{IF $arrElement.weight_kb}}
                                    <li>Waga obrazka: <span>{{$arrElement.weight_kb}} kB</span></li>
                                {{END if-list}}

                                {{IF $arrElement.pretty_exif.strCreatedBy}}
                                    <li>Typ aparatu: <span>{{$arrElement.pretty_exif.strCreatedBy}}</span></li>
                                {{END if-list}}
                                {{IF $arrElement.pretty_exif.strExposure}}
                                    <li>Ekspozycja: <span>{{$arrElement.pretty_exif.strExposure}}</span></li>
                                {{END if-list}}
                                {{IF $arrElement.pretty_exif.strAperture}}
                                    <li>Przesłona: <span>{{$arrElement.pretty_exif.strAperture}}</span></li>
                                {{END if-list}}
                                {{IF $arrElement.pretty_exif.strIso}}
                                    <li>ISO: <span>{{$arrElement.pretty_exif.strIso}}</span></li>
                                {{END if-list}}
                                {{IF $arrElement.pretty_exif.strCreateDateTime}}
                                    <li>Data wykonania: <span>{{this::prettyDate($arrElement.pretty_exif.strCreateDateTime)}}</span></li>
                                {{END if-list}}

                            </ul>
                        </div>
                    {{END if-list}}
                </div>
            </div>
                {{IF $arrElement.description}}
                <div class="desc">
                    <figcaption>{{this::html($arrElement.description)}}</figcaption>
                </div>
            {{END if-list}}
          
        </div>
    </figure>
            
    <div id="modal-embeed-element-{{$arrElement.id}}" class="modal fade element-embeed">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">
                        <img src="{{$arrElement.thumb_url}}" alt="{{q($arrElement.title)}}" />
                        Pobierz kod obrazka
                    </h4>
                </div>
                <div class="modal-body">
                    <form>
                         <div class='r'>
                            <strong>Link bezpośredni do obrazka</strong>
                            <input type='text' readonly='readonly' class='form-control' value='{{$arrElement.thumb_url}}' onfocus='javascript:this.select();' />
                        </div>
                        <div class='r'>
                            <strong>Umieść na swojej stronie obrazek</strong>
                            <input type='text' readonly='readonly' class='form-control' value='<img src="{{$arrElement.thumb_url}}" alt="{{$arrElement.title}}" />' onfocus='javascript:this.select();' />
                        </div>
                        <div class='r'>
                            <strong>Obrazek w BBCode</strong>
                            <input type='text' readonly='readonly' class='form-control' value='[img]{{$arrElement.thumb_url}}[img]' onfocus='javascript:this.select();' />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-preview-{{$arrElement.id}}" class="modal fade element-preview">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">
                        {{q($arrElement.title)}}
                    </h4>
                </div>
                <div class="modal-body">
                    <img src='{{$arrElement.thumb_url}}' alt='{{q($arrElement.title)}}' />
                    <div>
                        <!--<div class="fb-like" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                        <div class="fb-share-button" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-layout="button_count"></div>
                        <a class="twitter-share-button" href="https://twitter.com/intent/tweet?url={{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-count="horizontal">Tweet</a>
                        <script>
                            var wykop_url=location.href;
                            var wykop_title=encodeURIComponent(document.title);
                            document.write('<iframe src="http://www.wykop.pl/dataprovider/diggerwidget/?url='+encodeURIComponent(wykop_url)+'&title='+(wykop_title)+'&bg=FFFFFF&type=compact2" style="border:none;height:20px; width: 90px; overflow:hidden;margin:0;padding:0;" frameborder="0" border="0"></iframe>');
                        </script>
                        <a href="//pl.pinterest.com/pin/create/button/?url={{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}" data-pin-color="red" style="position:relative; bottom: 7px; ">
                            <img src="/imagehost3/img/pinit_fg_en_rect_red_20.png" />
                        </a>
                        <div class="g-plusone" data-size="medium" data-href="{{this::url('Details', $arrArtifact.id, $arrArtifact.slug)}}"></div>-->
                    </div>
                    {{IF $arrPreviewPuppy.code}}
                        <div class='puppy preview-modal'>{{$arrPreviewPuppy.code}}</div>
                    {{END if-list}}
                </div>
            </div>
        </div>
    </div>
                        
</li>