<div class="row">

    <div class="col-sm-3">
        <div class="tile-block" id="remove_ban" style="background-color:#D34A00;">

            <div class="tile-header">
                <i class="entypo-cancel"></i>

                <a href="#">
                    Usuwanie i banowanie
                    <span>Podaj poniżej ID artefaktu lib ID sprzedawcy Allegro. Po kliknięciu "usuń" system wyświetli informacje na temat sprzedawcy, wtedy będziesz mógł potwierdzić usunięcie i ban.</span>
                </a>
            </div>

            <div class="tile-content">

                <input type="text" class="artifact-id form-control" id="field-ta" placeholder="Podaj ID artefaktu" />
                <div style="text-align:center; margin: 10px; ">lub</div>
                <input type="text" class="seller-id form-control" id="field-ta" placeholder="Podaj ID sprzedawcy" />

            </div>

            <div class="tile-footer">
                <div class="step1">
                    <button type="button" class="do-remove btn btn-red">Usuń</button>
                </div>
                <div class="step2" style="display: none; ">
                    ID sprzedawcy na Allegro: <strong class="allegro-seller-id"></strong><br />
                    Ilość ofert na imgED: <strong class="seller-offers-count"></strong><br />
                    <br />
                    Przykładowe oferty: <br />
                    <div class="example-offers" style="margin-bottom:10px;">
                        <ul style="padding-left:15px;"></ul>
                    </div>
                    <input type="checkbox" checked="checked" name="remove-offers" /> usuń oferty<br />
                    <input type="checkbox" checked="checked" name="ban-seller" /> zbanuj sprzedawcę<br />
                    <br />
                    <div class="col-xs-12" style="line-height:31px; margin-bottom: 15px; ">
                        <button type="button" class="confirm btn btn-red pull-left">Zatwierdź</button>
                        <span class="pull-right cancel">&laquo; anuluj</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>