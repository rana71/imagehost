<div class='m-user'>
    <h1>Twoje konto</h1>
    <div class='main-content'>
        <div role="tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#haslo" id="haslo-tab" role="tab" data-toggle="tab" aria-controls="haslo" aria-expanded="true">Zmiana hasła</a>
                </li>
                <li role="presentation" class="">
                    <a href="#email" role="tab" id="email-tab" data-toggle="tab" aria-controls="email" aria-expanded="false">Zmiana adresu e-mail</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" id="haslo" aria-labelledby="haslo-tab">
                    <form action='' method='post' class="form-horizontal user-account-change-password">
                        <div class="form-group">
                            <label for='current_password' class="col-sm-4 control-label">Aktualne hasło:</label> 
                            <div class="col-sm-8">
                                <input type='password' name='current_password' id='current_password' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for='new_password' class="col-sm-4 control-label">Nowe hasło:</label> 
                            <div class="col-sm-8">
                                <input type='password' name='new_password' id='new_password' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for='new_password2' class="col-sm-4 control-label">Potwierdź nowe hasło:</label> 
                            <div class="col-sm-8">
                                <input type='password' name='new_password2' id='new_password2' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <input type='submit' class='btn btn-primary' value='Zapisz nowe hasło' />
                            </div>
                        </div>

                    </form>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="email" aria-labelledby="email-tab">
                    <div class="alert alert-warning" role="alert">
                        <strong>Uwaga!</strong> <br />
                        Ze względów bezpieczeństwa po zmianie adresu e-mail <strong>zostaniesz wylogowany</strong> ze swojego konta. Na nowy adres e-mail wyślemy maila z linkiem aktywacyjnym. Dopiero po jego kliknięciu będziesz mógł ponownie zalogować się na swoje konto.
                    </div>
                    <form action='' method='post' class="form-horizontal user-account-change-email">
                        <div class="form-group">
                            <label class="col-sm-5 control-label">Aktualne adres e-mail:</label> 
                            <div class="col-sm-7">
                                {{$arrLoggedUser.email}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for='change-email-password' class="col-sm-5 control-label">Aktualne hasło:</label> 
                            <div class="col-sm-7">
                                <input type='password' name='password' id='change-email-password' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for='change-email-new' class="col-sm-5 control-label">Nowy adres e-mail:</label> 
                            <div class="col-sm-7">
                                <input type='text' name='new_email' id='change-email-new' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for='change-email-renew' class="col-sm-5 control-label">Powtórz nowy adres e-mail:</label> 
                            <div class="col-sm-7">
                                <input type='text' name='new_email2' id='change-email-renew' class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-5 col-sm-7">
                                <input type='submit' class='btn btn-primary' value='Zapisz nowy adres e-mail (nastąpi wylogowanie)' />
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    
</div>
