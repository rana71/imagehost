<div class='container sign-in-contents col-md-5 col-md-offset-3'>
    <form action='' method='post' class="form-horizontal">
        <div class="form-group">
            <label for='signin-username' class="col-sm-4 control-label">Nazwa użytkownika:</label> 
            <div class="col-sm-8">
                <input type='text' name='username' id='signin-username' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label for='signin-password' class="col-sm-4 control-label">Hasło:</label> 
            <div class="col-sm-8">
                <input type='password' name='password' id='signin-password' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <input type='submit' class='btn btn-primary' value='Zaloguj' />
                <button type='button' class="pull-right btn btn-small btn-social btn-facebook"><i class="fa fa-facebook"></i> Połącz z Facebookiem</button>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <a href='{{this::url("User::forgotPassword")}}' class='forget-password'>Nie pamiętasz hasła?</a>
            </div>
        </div>
    </form>
</div>

<div class="modal fade modal-activation-nok" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aktywacja konta nieudana</h4>
            </div>
            <div class="modal-body">
                Twoje konto nie mogło być aktywowane.<br />Prawdopodobnie link aktywacyjny wygasł. Pamiętaj, masz tylko 72 godziny na potwierdzenie swojego adresu e-mail. Prosimy o ponowną rejestrację konta.
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-activation-ok" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aktywacja konta</h4>
            </div>
            <div class="modal-body">
                Twoje konto zostało aktywowane, możesz się na nie zalogować
            </div>
        </div>
    </div>
</div>