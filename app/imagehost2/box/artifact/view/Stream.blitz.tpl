{{IF $arrList}}
    <div class="container items-rows">
        <div class='items-list'>
            {{this::makeGrid($arrList)}}
        </div>
        
        <div class='loading stream-load-more jumbotron'>
            <div class='btn btn-default disabled'>
                <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> 
                Ładuję więcej obrazków <span class='dots'>...</span>
            </div>
        </div>

        <script>
            var strStreamOptionsSerialized = '{{$strStreamOptionsSerialized}}';
        </script>
    </div>
{{ELSE}}
    <div class="container">
        <p>Nie znaleziono żadnego obrazka</p>
    </div>
{{END if-list}}