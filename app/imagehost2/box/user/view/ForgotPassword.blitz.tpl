<div class='container forgot-password-contents col-md-5 col-md-offset-3'>
    <form action='' method='post' class="form-horizontal">
        <div class="form-group">
            <label for='signin-email' class="col-sm-4 control-label">Twój adres-email:</label> 
            <div class="col-sm-8">
                <input type='email' name='email' id='signin-email' class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <input type='submit' class='btn btn-primary' value='Odzyskaj hasło' />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <a href='{{this::url("User::login")}}' class='go-signin'>Wróć do logowania</a>
            </div>
        </div>

    </form>
</div>