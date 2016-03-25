<div class='container upload-form' data-default-item="{{$strDefaultItemType}}">
    <div class='form-contents'>
        <p class='intro'></p>
        <ol class='items-list'></ol>
        <div class='jumbotron submit-row row'>
            <div class='col-lg-6 text-left text'>
                <span class='req'>*</span> Pola wymagane<br />
                Wgrywając galerię akceptuję <a href='{{this::url("StaticContent::agreements")}}' title='Regulamin' target='_blank'>warunki korzystania z serwisu</a>
            </div>
            <div class='col-lg-6 text-right'>
                <div class="btn btn-lg btn-success save-button">
                    <span class="glyphicon glyphicon-ok"></span> Wyślij
                </div>
            </div>
        </div>
        <div class='items-left'>
            <strong class='items-left-no'>20</strong> elementów możliwych jeszcze do dodania
        </div>
        <div class='row'>
            <div class='col-lg-4'>
                <div class='add-item photo'>
                    <span class='glyphicon glyphicon-picture'></span>
                    <span class='text'>Dodaj zdjęcie</span>
                </div>
            </div>
            <div class='col-lg-4'>
                <div class='add-item ytvideo'>
                    <span class='glyphicon glyphicon-film'></span>
                    <span class='text'>Dodaj video</span>
                </div>
            </div>
            <div class='col-lg-4'>
                <div class='add-item mem'>
                    <span class='glyphicon glyphicon-eye-open'></span>
                    <span class='text'>Dodaj mema</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class='templates'>
        <ul>
            
            <li class='row item image'>
                <div class="col-xs-12">
                    <h4>Element <span class="item-no"></span></h4>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <div id="dropbox">
                            <input type='file' name='image[]' data-image-selector='true' />
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label><span class="req">*</span> Tytuł zdjęcia</label>
                        <input type="text" name="title[]" class="form-control" placeholder="Jak chcesz nazwać zdjęcie ?" />
                    </div>
                    <div class="form-group">
                        <label>Opis zdjęcia</label>
                        <textarea class="form-control" name="content[]" placeholder="Napisz coś o tym zdjęciu"></textarea>
                        <p class='tip'>
                            Teraz w opisie możesz używać BBCode ! Np: <br />
                            [b]tekst[/b] = <strong>tekst</strong><br />
                            [i]tekst[/i] = <i>tekst</i><br />
                            [u]tekst[/u] = <u>tekst</u><br />
                            [url]http://www.twojastrona.pl[/url] = <a href='http://imged.pl'>http://www.twojastrona.pl</a><br />
                        </p>
                    </div>
                    <div class="story-element-remove-row form-group text-right">
                        <button class="remove-item btn btn-link"><span class='icon glyphicon glyphicon-remove'></span> Usuń to zdjęcie</button>
                    </div>
                </div>
            </li>
            
            <li class='row item ytvideo'>
                <div class="col-xs-12">
                    <h4>Element <span class="item-no"></span></h4>
                </div>

                
                <div class="col-lg-12">
                    <div class="form-group">
                        <label><span class="req">*</span> Tytuł filmu</label>
                        <input type="text" name="title[]" class="form-control" placeholder="Jak chcesz nazwać film ?" />
                    </div>
                    <div class="form-group">
                        <label><span class="req">*</span> Adres URL filmu</label>
                        <input type="text" name="movie_url[]" class="form-control" placeholder="Podaj adres URL filmu z serwisu https://www.youtube.com" />
                    </div>
                    <div class="form-group">
                        <label>Opis filmu</label>
                        <textarea class="form-control" name="content[]" placeholder="Napisz coś o tym filmie"></textarea>
                        <p class='tip'>
                            Teraz w opisie możesz używać BBCode ! Np: <br />
                            [b]tekst[/b] = <strong>tekst</strong><br />
                            [i]tekst[/i] = <i>tekst</i><br />
                            [u]tekst[/u] = <u>tekst</u><br />
                            [url]link[/url] = <a href='http://imged.pl'>link</a><br />
                            Aby przejsć do nowej linii naciśnij po prostu ENTER
                        </p>
                    </div>
                    <div class="story-image-remove-row form-group text-right">
                        <button class="remove-item btn btn-link"><span class='icon glyphicon glyphicon-remove'></span> Usuń ten film</button>
                    </div>
                </div>
            </li>
            
            <li class='row item mem'>
                <div class="col-xs-12">
                    <h4>Element <span class="item-no"></span></h4>
                </div>
                <div class='col-sm-7'>

                    <div class="mem-container">
                        <img src='' class='mem-background' />
                        <textarea class='mem-textarea mem-title' rows="2">Tytuł mema</textarea>
                        <textarea class='mem-textarea mem-text' rows="3">Jakiś chwytliwy opis</textarea>
                    </div>

                </div>
                <div class='col-sm-5'>
                    <div class='row'>
                        <div class='col-xs-12'>
                            <input type='file' class='custom-mem-background' />
                        </div>

                    </div>
                    <div class='row' style='margin-top:20px;'>
                        <div class='col-xs-12'>

                            <div class="form-group text-center">
                                <label>Możesz też wyszukać tła w imgED:</label>
                                <input type='text' class="search-image form-control" placeholder="Jakiego tła szukasz?">
                            </div>
                        </div>
                        <div class='col-xs-12 search-results'></div>
                    </div>
                </div>
                <div class="col-sm-12 story-element-remove-row form-group text-right">
                    <button class="remove-item btn btn-link"><span class='icon glyphicon glyphicon-remove'></span> Usuń tego mema</button>
                </div>
            </li>
            
            
        </ul>
        
        <div class="modal-gallery-info modal fade" data-keyboard="false" data-backdrop="static" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Zauważyliśmy, że dodajesz wiele elementów</h4>
                    </div>
                    <div class="modal-body">
                        <p>Dodaj tytuł i opis swojej galerii</p>
                        <div class="form-group">
                            <input type="text" name="title" class="form-control" id="title" placeholder="Tytuł galerii" />
                        </div>
                        
                        <div class="form-group">
                            <textarea class="form-control" name="content" id="content" placeholder="Opis galerii"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn btn-success save-info">
                            <span class="glyphicon glyphicon-ok"></span> Zapisz 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>