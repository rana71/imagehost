<div class='m-static contact'>
    
    <h1>Skontakuj się z nami</h1>
    
    <form action='' method='post'>
        <div class='r'>
            <label for='contact-email'>Twój adres e-mail</label> 
            <input id='contact-email' type='email' name='contact-email' placeholder='Adres e-mail' />
        </div>
        
        <div class='r'>
            <label for='contact-name'>Twoje imię</label> 
            <input id='contact-name' type='text' name='contact-name' placeholder='Twoje imię' />
        </div>
        <div class='r'>
            <label for='contact-content'>Treść zapytania *</label> 
            <textarea class='req' id='contact-content' name='contact-content' rows='5' placeholder='Treść zapytania *'></textarea>
        </div>
        <div class='b'>
            <input type='hidden' id='contact-subject' name='contact-subject' value='{{$strSubject}}' />
            <input type='submit' value='Ładuję formularz...' data-value='Wyślij zapytanie' disabled='disabled' />
        </div>
    </form>


</div>