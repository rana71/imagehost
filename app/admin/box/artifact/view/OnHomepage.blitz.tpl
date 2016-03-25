<div class='col-sm-12 box-admins-list'>
    <h3>Artefakty na stronie głównej</h3>
    <table class="table table-bordered table-striped datatable dataTable no-footer artifacts-on-hp-table" id="table-2" role="grid" aria-describedby="table-2_info">
        <thead>
            <tr role="row">
                <th>ID</th>
                <th>Miniatura</th>
                <th>Nazwa</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            {{BEGIN arrArtifacts}}
            <tr role="row" class="even" data-artifact-id="{{$id}}" >
                <td>#{{$id}}</td>
                <td><img src="{{$thumb_url}}" class="thumb" /></td>
                <td>
                    <a href='{{$strUrl}}' title='Zobacz wrzutkę' target='_blank'>
                        {{$title}} <span class='glyphicon glyphicon-new-window'></span>
                    </a>
                </td>
                <td>
                    <button class="btn btn-danger remove-from-homepage" type="button">Usuń ze strony głównej</button>
                </td>
            </tr>
            {{END}}
        </tbody>
    </table>
        
    
    <h3>Dodaj artefakt do strony głównej</h3>

    <form role="form" class="form-horizontal form-groups-bordered" id="add-artifact-to-homepage">

        <div class="form-group">

            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon">ID artefaktu:</span>
                    <input type="text" class="form-control artifact-id" placeholder="" />
                    <span class="input-group-btn">
                        <button class="btn btn-primary add-to-homepage" type="button">Dodaj</button>
                    </span>
                </div>
            </div>
        </div>

    </form>
        
</div>