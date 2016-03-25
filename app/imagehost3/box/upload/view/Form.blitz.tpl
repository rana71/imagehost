<div class='m-upload' data-default-item="{{$strDefaultItemType}}">
    <h1>{{$strBoxHeader}}</h1>
    <p class='intro'></p>
    <ol class='items-list'></ol>
    <p class='items-left'><strong>20</strong> elementów możliwych jeszcze do dodania</p>
    <div class='add-item-type-select'>
        <div class='type'><div class='add photo'>Dodaj zdjęcie</div></div>
        <div class='type'><div class='add ytvideo'>Dodaj video</div></div>
        <div class='type'><div class='add mem'>Dodaj mema</div></div>
    </div>
    <div class='submit-row'>
        <div class='container'>
            <p class='info'>
                * Pola wymagane<br />
                Wgrywając galerię akceptuję <a href='{{this::url("StaticContent::agreements")}}' title='Regulamin' target='_blank'>warunki korzystania z serwisu</a>
            </p>
            <div class='btns'>
                <label class='advanced-mode'>
                    <input type='checkbox' name='advanced' /> Dodaj autora zdjęć
                </label>
                <div class='save'>Zapisz</div>
            </div>
        </div>
    </div>
    
    <div class='templates'>
        <ul>
            
            <li class='item image'>
                <div class='item-no'></div>
                <div class='preview'><input type='file' name='image[]' data-image-selector='true' /></div>
                <form action='' method='post' class='info'>
                    <div class='r title req'>
                        <label>Tytuł zdjęcia</label> 
                        <input class='req' type="text" name="title[]" placeholder="Jak chcesz nazwać zdjęcie ? *" />
                    </div>
                    <div class='r advanced-mode-only'>
                        <label>Autor zdjęcia</label> 
                        <input type="text" name="author[]" placeholder="Kto jest autorem zdjęcia ?" />
                    </div>
                    <div class='r content'>
                        <label>Opis zdjęcia</label> 
                        <textarea name="content[]" placeholder="Napisz coś o tym zdjęciu" rows='5'></textarea>
                    </div>
                    <div class='options'>
                        <span class="bbcode">Wspieramy BBcode</span>
                        <span class="remove-item">Usuń to zdjęcie</span>
                    </div>
                </form>
            </li>
            
            <li class='item ytvideo'>
                <div class='item-no'></div>
                <div class='preview'><div class='video-selector'></div></div>
                <form action='' method='post' class='info'>
                    <div class='r title req'>
                        <label>Adres URL</label> 
                        <input class='req' type="text" name="movie_url[]" placeholder="Adres URL filmu z serwisu https://www.youtube.com *" />
                    </div>
                    <div class='r title req'>
                        <label>Tytuł filmu</label> 
                        <input class='req' type="text" name="title[]" placeholder="Jak chcesz nazwać film ? *" />
                    </div>
                    <div class='r content'>
                        <label>Opis filmu</label> 
                        <textarea name="content[]" placeholder="Napisz coś o tym filmie" rows='3'></textarea>
                    </div>
                    <div class='options'>
                        <span class="bbcode">Wspieramy BBcode</span>
                        <span class="remove-item">Usuń to video</span>
                    </div>
                </form>
            </li>
            
            <li class='item mem'>
                <div class='item-no'></div>
                <div class='preview'>
                    <div class='meme-generator'>
                        <img src='' />
                        <textarea class='mem-title' rows="2">Tytuł mema</textarea>
                        <textarea class='mem-text' rows="3">Jakiś chwytliwy opis</textarea>
                    </div>
                </div>
                <div class="info">
                    <input type='file' name='image[]' class='custom-mem-background' data-image-selector='true' />
                    <form class='bgsearch'>
                        Możesz też wyszukać tła w imgED: 
                        <input type='text' class="search-image" placeholder="Jakiego tła szukasz?" />
                        <div class='search-results'></div>
                    </form>
                    <div class='options'>
                        <span class="remove-item">Usuń tego mema</span>
                    </div>
                </div>
            </li>
            
        </ul>
        
        
        <div id="modal-gallery-info" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Zauważyliśmy, że dodajesz wiele elementów. Dodaj tytuł i opis do swojej galerii.</h4>
                    </div>
                    
                    <form action='' method='post'>
                        <div class="modal-body">
                            <div class='r'>
                                <label for='title'>Tytuł *</label> 
                                <input class='req' id='title' type='text' name='title' placeholder='Tytuł *' />
                            </div>
                            <div class='r'>
                                <label for='content'>Opis</label> 
                                <textarea id='content' name='content' rows='5' placeholder='Opis'></textarea>
                            </div>
                            <div class='b'>
                                <input type='submit' value='Zapisz'/>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
            <input type='hidden' readonly='readonly' id='artifact-id' value='{{$arrArtifact.id}}' />
            <input type='hidden' readonly='readonly' id='vote-environment' value='{{$numEnvironment}}' />
        </div>
    </div>
    <input type="hidden" name='client_ip' value='{{$strClientIp}}' readonly='readonly' />
</div>
