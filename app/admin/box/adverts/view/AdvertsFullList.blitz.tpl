<div class='col-sm-12 box-adverts-list'>
    <h3>Lista Stref reklamowych i reklam</h3>
    <table class="table table-bordered table-striped datatable dataTable no-footer" id="table-2" role="grid" aria-describedby="table-2_info">
        <thead>
            <tr role="row">
                <th>Streafa</th>
                <th colspan='2'>Podpięta reklama</th>
            </tr>
        </thead>

        <tbody>
             <!--<tr role="row" class="even">
                <td>Top Layer</td>
                <td>
                    <textarea class="form-control autogrow area-advert-code" placeholder="" style="height: 82px; width:450px; resize: none;">{{$arrToplayer.code}}</textarea><br />
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="toplayer-width" class="col-md-2 control-label">Szerokość:</label>
                            <div class="col-md-3">
                                <input type="text" id='toplayer-width' value="{{$arrToplayer.width}}" class="toplayer-width form-control" placeholder="Szerokość w pikselach" />
                            </div>
                            
                            <label for="toplayer-height" class="col-md-2 control-label">Wysokość:</label>
                            <div class="col-md-3">
                                <input type="text" id='toplayer-height' value="{{$arrToplayer.height}}" class="toplayer-height form-control" placeholder="Wysokość w pikselach" />
                            </div>
                            
                        </div>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-green btn-icon icon-left save-advert-toplayer">Zapisz <i class="entypo-check"></i></button>
                    <button type="button" class="btn btn-red btn-icon icon-left clear-advert-toplayer">Usuń reklamę <i class="entypo-cancel"></i></button>
                </td>
            </tr>-->
            {{BEGIN arrAreas}}
            <tr role="row" class="even">

                <td>{{$strDescription}}</td>
                <td>
                    <textarea class="form-control autogrow area-advert-code" placeholder="" style="height: 82px; width:450px; resize: none;">{{$strAdvertCode}}</textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-green btn-icon icon-left save-advert" data-area-id="{{$strId}}">Zapisz <i class="entypo-check"></i></button>
                    <button type="button" class="btn btn-red btn-icon icon-left clear-area" data-area-id="{{$strId}}">Usuń reklamę <i class="entypo-cancel"></i></button>
                </td>
            </tr>
            {{END}}
        </tbody>
    </table>
</div>