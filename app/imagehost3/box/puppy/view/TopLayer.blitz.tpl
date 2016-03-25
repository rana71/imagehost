{{IF $strPuppyCode}}
    <div id="modal-top-layer" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    {{$strPuppyCode}}
                    <input type='hidden' class='toplayer-width' value='{{$numWidth}}' />
                    <input type='hidden' class='toplayer-height' value='{{$numHeight}}' />
                </div>
            </div>
        </div>
    </div>
{{END if-list}}