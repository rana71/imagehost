<div class="login-container">

    <div class="login-header login-caret">

        <div class="login-content">

            <p class="description">Zaloguj się aby uzyskać dostęp do panelu administracyjnego</p>

            <!-- progress bar indicator -->
            <div class="login-progressbar-indicator">
                <h3>0%</h3>
                <span>trwa logowanie...</span>
            </div>
        </div>

    </div>

    <div class="login-progressbar">
        <div></div>
    </div>

    <div class="login-form">

        <div class="login-content">

            <div class="form-login-error">
                <h3>Niepoprawne logowanie</h3>
                <p>Podane dane logowania są nieprawidłowe</p>
            </div>

            <form method="post" role="form" id="form_login">

                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-user"></i>
                        </div>

                        <input type="text" class="form-control" name="username" id="username" placeholder="Nazwa użytkownika" autocomplete="off" />
                    </div>

                </div>

                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-key"></i>
                        </div>

                        <input type="password" class="form-control" name="password" id="password" placeholder="Hasło" autocomplete="off" />
                    </div>

                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-login">
                        <i class="entypo-login"></i>
                        Zaloguj
                    </button>
                </div>
            </form>


            <div class="login-bottom-links">

                <a href="{{this::url('Login::passwordRecovery')}}" class="link">Zapomniałeś hasła?</a>

            </div>

        </div>

    </div>

</div>
                
                <script src="/admin/assets/js/neon-login.js"></script>