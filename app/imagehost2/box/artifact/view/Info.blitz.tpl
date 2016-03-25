{{BEGIN arrInfo}}
    <div class='file-data'>
        <ul class="list-group">

            {{IF author_display_name}}
                    <li class="list-group-item">Autor: <span class='value'><a href="{{this::url('Listing::userUploads', $author_display_name)}}" title="{{q($author_display_name)}} - obrazki">{{q($author_display_name)}}</a></span></li>
            {{END if-list}}
            {{IF add_timestamp}}
                <li class="list-group-item">Data dodania: <span class='value'>{{this::prettyDate($add_timestamp)}}</span></li>
            {{END if-list}}
                
            {{IF width && height}}
                <li class="list-group-item">Oryginalna rozdzielczość: <span class='value'>{{$width}}x{{$height}} px</span></li>
            {{END if-list}}
                
            {{IF weight}}
                <li class="list-group-item">Waga obrazka: <span class='value'>{{$weight_kb}} kB</span></li>
            {{END if-list}}
            {{IF prettyExif.strCreatedBy}}
                <li class="list-group-item">
                    Typ aparatu: <span class='value'>{{$prettyExif.strCreatedBy}}</span>
                </li>
            {{END if-list}}
            {{IF prettyExif.strExposure}}
                <li class="list-group-item">
                    Ekspozycja: <span class='value'>{{$prettyExif.strExposure}}</span>
                </li>
            {{END if-list}}
            {{IF prettyExif.strAperture}}
                <li class="list-group-item">
                    Przesłona: <span class='value'>{{$prettyExif.strAperture}}</span>
                </li>
            {{END if-list}}
            {{IF prettyExif.strIso}}
            <li class="list-group-item">
                ISO: <span class='value'>{{$prettyExif.strIso}}</span>
            </li>
            {{END if-list}}
            {{IF prettyExif.strCreateDateTime}}
            <li class="list-group-item">
                Data wykonania: <span class='value'>{{this::prettyDate($prettyExif.strCreateDateTime)}}</span>
            </li>
            {{END if-list}}
        </ul>
    </div>
{{END}}