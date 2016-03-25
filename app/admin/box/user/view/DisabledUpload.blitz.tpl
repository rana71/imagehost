<div class='col-sm-12 box-disabled-uplaod-list'>
    <h3>Zablokowany dostęp do funkcji Upload</h3>

    
    
    <div id="table-3_wrapper" class="dataTables_wrapper form-inline">

        <div class="row">
            <div class="col-xs-6 col-left">
                <div class="dataTables_length" id="table-3_length">
                    <form action='' method='post' class='add-block-form'>
                        Dodaj blokadę: <input type="text" class="ip-to-block form-control input-sm" placeholder="Adres IP do zablokowania" aria-controls="table-3" /> 
                    </form>
                </div>
            </div>
            <div class="col-xs-6 col-right">
                <div id="table-3_filter" class="dataTables_filter">
                    <label>Szukaj: <input type="search" class="block-search form-control input-sm" placeholder="" aria-controls="table-3"></label>
                    
                    <label style='margin: 0 10px;'>
                        Załaduj ponownie <input type="text" name="list-length" class='form-control input-sm' value='40' /> kont
                    </label>
                </div>
            </div>
        </div>
        
        <table class="table table-bordered datatable users-table" id="table-3">
            <thead>
                <tr class="replace-inputs">
                    <th>Data założenia blokady</th>
                    <th>IP</th>
                    <th>Opcje</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th>Data założenia blokady</th>
                    <th>IP</th>
                    <th>Opcje</th>
                </tr>
                <tr class='row-template' style='display:none;' data-block-id="">
                    <td class="block_date"></td>
                    <td class="ip"></td>
                    <td>
                        <a href="#" class="remove-block btn btn-danger btn-sm btn-icon icon-left"><i class="entypo-cancel"></i> Zdejmij blokadę</a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    
</div>