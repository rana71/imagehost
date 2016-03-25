<div class='container sign-up-contents col-md-5 col-md-offset-3'>
    <form action='' method='post' class="form-horizontal">
        <div class="form-group">
            <label for='signup-email' class="col-sm-4 control-label">Adres e-mail:</label> 
            <div class="col-sm-8">
                <input type='email' name='email' id='signup-email' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for='signup-username' class="col-sm-4 control-label">Nazwa użytkownika:</label> 
            <div class="col-sm-8">
                <input type='text' name='username' id='signup-username' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for='signup-password' class="col-sm-4 control-label">Hasło:</label> 
            <div class="col-sm-8">
                <input type='password' name='password' id='signup-password' class="form-control" aria-describedby="password-tip" />
                <span id="password-tip" class="help-block">Minimum 5 znaków</span>
            </div>
        </div>
        <div class="form-group">
            <label for='signup-repassword' class="col-sm-4 control-label">Powtórz hasło:</label> 
            <div class="col-sm-8">
                <input type='password' name='repassword' id='signup-repassword' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <input type='submit' class='btn btn-primary' value='Załóż konto' />
                <button type='button' class="pull-right btn btn-small btn-social btn-facebook"><i class="fa fa-facebook"></i> Połącz z Facebookiem</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <a href='{{this::url("StaticContent::AccountGuestCompare")}}'>Co zyskasz po założeniu konta ?</a>
            </div>
        </div>
        
    </form>
</div>