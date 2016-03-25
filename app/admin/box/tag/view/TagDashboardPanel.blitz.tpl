<div class="row">

    <div class="col-sm-4">

        <div class="panel panel-primary tags-dashboard">
            <div class="panel-heading">
                <div class="panel-title">Ostatnio usunięte tagi</div>
            </div>

            <div class="panel-body with-table"><table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Slug</th>
                            <th>Data</th>
                            <th>Cofnij</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        {{IF $arrLastRemoved}}
                            {{BEGIN arrLastRemoved}}
                                <tr>
                                    <td>{{$slug}}</td>
                                    <td>{{$removed_since}}</td>
                                    <td>
                                        <button type='button' class='btn btn-default undo-remove' data-tag-id='{{$id}}' data-tag-slug="{{$slug}}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            {{END}}
                        {{ELSE}}
                            <tr>
                                <td colspan="3">
                                    <div class="alert alert-default"><strong>Brak</strong> usuniętych tagów</div>
                                </td>
                            </tr>
                        {{END if-list}}
                        <tr>
                            <td colspan="3">
                                <form action='' method='post' class='remove-tag'>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input type="text" name="tag-to-delete" class="form-control" placeholder="Slug taga do usunięcia" />
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-default go-delete"> 
                                                    <i class="glyphicon glyphicon-remove-circle"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>