<div class="col-md-12 box-admins-create-account">
    {{BEGIN arrAdmin}}
    <h3>Edycja konta - {{$username}}</h3>

    <form role="form" class="form-horizontal form-groups-bordered admin-add" data-admin-id="{{$id}}">

        <div class="form-group">
            <label for="field-1" class="col-sm-3 control-label">Nazwa użytkownika</label>

            <div class="col-sm-5">
                <input type="text" class="form-control" name='username' id="field-1" value="{{$username}}">
            </div>
        </div>

        <div class="form-group">
            <label for="field-2" class="col-sm-3 control-label">Hasło</label>

            <div class="col-sm-5">
                <input type="password" class="form-control" name='password' id="field-2" value="">
            </div>
        </div>
        
        <div class="form-group">
            <label for="field-2" class="col-sm-3 control-label">Potwierdź hasło</label>

            <div class="col-sm-5">
                <input type="password" class="form-control" name='repassword' id="field-2" value="">
            </div>
        </div>

        <div class="form-group">
            <label for="field-3" class="col-sm-3 control-label">Imię</label>

            <div class="col-sm-5">
                <input type="text" class="form-control" name='name' id="field-3" value="{{$name}}">
            </div>
        </div>
        
        <div class="form-group">
            <label for="field-3" class="col-sm-3 control-label">Nazwisko</label>

            <div class="col-sm-5">
                <input type="text" class="form-control" name='surname' id="field-3" value="{{$surname}}">
            </div>
        </div>
        
        <div class="form-group">
            <label for="field-3" class="col-sm-3 control-label">Adres e-mail</label>

            <div class="col-sm-5">
                <input type="text" class="form-control" name='email' id="field-3" value="{{$email}}">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5">
                <button type="submit" class="btn btn-default">Zapisz zmiany</button>
            </div>
        </div>
    </form>
    {{END}}
</div>