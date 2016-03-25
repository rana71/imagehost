<div class='container contact'>
    <div class="row">
        <div class='col-md-6 col-md-offset-3'>
            <form action='' method='post' class="form-horizontal">
                <div class="form-group">
                    <label for='contact-email' class="col-sm-4 control-label">Twój adres e-mail:</label> 
                    <div class="col-sm-8">
                        <input type='email' name='contact-email' id='contact-email' class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for='contact-name' class="col-sm-4 control-label">Twoje imię:</label> 
                    <div class="col-sm-8">
                        <input type='text' name='contact-name' id='contact-name' class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label for='contact-content' class="col-sm-4 control-label">Treść zapytania:</label> 
                    <div class="col-sm-8">
                        <textarea name='contact-content' id='contact-content' class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <input type='hidden' id='contact-subject' value='{{$strSubject}}' />
                        <input type='submit' class='btn btn-primary' value='Wyślij zapytanie' />
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>