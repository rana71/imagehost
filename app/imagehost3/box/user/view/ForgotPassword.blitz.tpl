<div class="m-user">
     <h1>Odzyskiwanie dostępu do konta</h1>
     
     <div class="main-content sign-up">
        <form action='' metehod='post'>
            <div class='r'>
                <div class='col'>
                    <label for='email'>Twój adres e-mail *</label> 
                    <input id='email' type='email' name='email' placeholder='Twój adres e-mail *' />
                </div>
                <div class='col'>
                    <input type='submit' value='Odzyskaj hasło' />
                </div>
            </div>
            <div class='r'>
                <a href='{{this::url("User::login")}}' title="Logowanie" class="go-sign-in">Wróć do logowania</a>
            </div>
        </form>
     </div>
</div>