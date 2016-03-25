<div class='m-stream'>
    {{IF $strHeader}}<h1>{{$strHeader}}</h1>{{END if-list}}
    
    {{IF $arrList}}
        <div class="items-rows">
            <div class='items-list'>
                {{this::makeGrid($arrList, $arrViewLayout)}}
            </div>
            {{IF $boolLoadingEnabled}}
                <p class="loading"><span>Ładowanie</span></p>
                <script>
                    var strStreamOptionsSerialized = '{{$strStreamOptionsSerialized}}';
                </script>
            {{END if-list}}
        </div>
    {{ELSE}}
        <div class="container">
            <p>Nie znaleziono żadnego obrazka</p>
        </div>
    {{END if-list}}
</div>