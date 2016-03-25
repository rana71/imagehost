<div class='col-sm-12 box-admins-list'>

    <h3>Zarządzanie artefaktami</h3>
    <br />
    
    <div id="table-3_wrapper" class="dataTables_wrapper form-inline">

        <div class="row">
            <div class="col-xs-12">
                <div id="table-3_filter" class="dataTables_filter">
                    <div style="float:right">
                        Sortuj wg: <select name="sort"  class='form-control'>
                            <option value="add_date" selected>czasu dodania</option>
                            <option value="popularity">popularności</option>
                            <option value="by_search">dopasowania</option>
                            
                        </select>
                    </div>
                    <label>Szukaj: <input type="search" class="artifact-search form-control input-sm" placeholder="" aria-controls="table-3"></label>
                    
                    <label style='margin: 0 10px;'>
                        Załaduj ponownie <input type="text" name="list-length" class='form-control input-sm' value='40' /> artefaktów
                    </label>
                    <label style='margin: 0 10px;'>
                        Wyszukuj także zaimportowane <input type="checkbox" name="is_imported" class='form-control' style="width:20px;" /> 
                    </label>
                   
                </div>
            </div>
        </div>
        <table class="table table-bordered datatable artifacts-table" id="table-3">
            <thead>
                <tr class="replace-inputs">
                    <th>ID</th>
                    <th>Dodany</th>
                    <th>Miniatura</th>
                    <th>Tytuł</th>
                    <th>Ilość elementów</th>
                    <th>Popularność</th>
                    <th>Strona główna</th>
                    <th>18+</th>
                    <th>Ofertowy</th>
                    <th>Opcje</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Dodany</th>
                    <th>Miniatura</th>
                    <th>Tytuł</th>
                    <th>Ilość elementów</th>
                    <th>Strona główna</th>
                    <th>18+</th>
                    <th>Ofertowy</th>
                    <th>Opcje</th>
                </tr>
                <tr class='row-template' style='display:none;'>
                    <td class='id'></td>
                    <td class='add-date'></td>
                    <td>
                        <img src='' class='image' style='max-width:150px;' />
                    </td>
                    <td class='title'>
                        <a href="" alt="" target="_blank">
                            <span class="text"></span>
                            <span class='glyphicon glyphicon-new-window'></span>
                        </a>
                    </td>
                    <td class='elements-count'></td>
                    <td class='shows-count-real'></td>
                    <td>
                        <input type='checkbox' class='on-homepage' />
                    </td>
                    <td>
                        <input type='checkbox' class='adults-only' />
                    </td>
                    <td>
                        <input type='checkbox' class='offer' />
                    </td>
                    <td>
                        <a href="#" class="artifact-uploader-ip btn btn-info btn-sm btn-icon icon-left"><i class="fa fa-crosshairs"></i> IP autora</a>
                        <a href="#" class="restore-artifact btn btn-success btn-sm btn-icon icon-left"><i class="entypo-check"></i> Przywróć</a>
                        <a href="#" class="delete-artifact btn btn-danger btn-sm btn-icon icon-left"><i class="entypo-cancel"></i> Usuń</a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>