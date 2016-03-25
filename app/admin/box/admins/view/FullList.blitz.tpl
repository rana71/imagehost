<div class='col-sm-12 box-admins-list'>
    <h3>Lista administratorów</h3>
    <table class="table table-bordered table-striped datatable dataTable no-footer" id="table-2" role="grid" aria-describedby="table-2_info">
        <thead>
            <tr role="row">
                <th>Nazwa użytkownika</th>
                <th>Imię i nazwisko</th>
                <th>Operacje</th>
            </tr>
        </thead>

        <tbody>
            {{BEGIN arrAdmins}}
            <tr role="row" class="even" data-admin-id="{{$id}}">

                <td>{{$username}}</td>
                <td class="name-surname">{{$name}} {{$surname}}</td>
                <td>
                    <a href="{{this::url("Admin::edit", $id)}}" class="btn btn-default btn-sm btn-icon icon-left">
                        <i class="entypo-pencil"></i>
                        Edytuj
                    </a>

                    <a href="#" class="remove btn btn-danger btn-sm btn-icon icon-left" data-admin-id="{{$id}}">
                        <i class="entypo-cancel"></i>
                        Usuń
                    </a>

                </td>
            </tr>
            {{END}}
        </tbody>
    </table>
</div>