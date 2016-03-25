<div class='col-sm-12 box-admins-list'>
    <h3>Użytkownicy portalu</h3>

    
    
    <div id="table-3_wrapper" class="dataTables_wrapper form-inline">

        <div class="row">
            <div class="col-xs-6 col-left">
                <div class="dataTables_length" id="table-3_length"></div>
            </div>
            <div class="col-xs-6 col-right">
                <div id="table-3_filter" class="dataTables_filter">
                    <label>Szukaj: <input type="search" class="account-search form-control input-sm" placeholder="" aria-controls="table-3"></label>
                    
                    <label style='margin: 0 10px;'>
                        Załaduj ponownie <input type="text" name="list-length" class='form-control input-sm' value='40' /> kont
                    </label>
                </div>
            </div>
        </div>
        
        <table class="table table-bordered datatable users-table" id="table-3">
            <thead>
                <tr class="replace-inputs">
                    <th>Nazwa uzytkownika</th>
                    <th>Adres e-mail</th>
                    <th>Data rejestracji</th>
                    <th>Aktywny ?</th>
                    <th>Ilość artefaktów</th>
                    <th>Statystyki zaawansowane</th>
                    <th>Funkcja "anonim"</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th>Nazwa uzytkownika</th>
                    <th>Adres e-mail</th>
                    <th>Data rejestracji</th>
                    <th>Aktywny ?</th>
                    <th>Ilość artefaktów</th>
                    <th>Statystyki zaawansowane</th>
                    <th>Funkcja "anonim"</th>
                </tr>
                <tr class='row-template' style='display:none;'>
                    <td class="username">
                        <a href="#" target='_blank'>
                            <span class="text"></span>
                            <span class='glyphicon glyphicon-new-window'></span>
                        </a>
                    </td>
                    <td class="email"></td>
                    <td class="add_date"></td>
                    <td class="is-active"><strong></strong><div></div></td>
                    <td class="artifacts-count"></td>
                    <td>
                        <input type='checkbox' class='is_pro_stats' />
                    </td>
                    <td>
                        <input type='checkbox' class='is_anonymous_available' />
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    
</div>