<div class='row'>
    <div class='col-sm-3 col-xs-12 logo-menu-container'>
        <a class='logo' href="{{this::url('Homepage')}}" title='imgED'><img src="/imagehost2/img/logo.png" title="imgED" /></a>
        <div class="btn-group topmenu">
            <button type="button" class="btn btn-default dropdown-toggle glyphicon glyphicon-menu-hamburger" data-toggle="dropdown" aria-expanded="false"></button>
            <ul class="dropdown-menu" role="menu">
                <li>
                 <a href="{{this::url('TagsList::index')}}" title='Najpopularniejsze tematy'>Tematy</a>
                </li>
                <li>
                    <a href="{{this::url('QueriesList::index')}}" title='Ostatnie wyszukiwania'>Ostatnie wyszukiwania</a>
                </li>
                <li class='divider'></li>
                <li class="dropdown-header">Ostatnie uploady</li>
                <li>
                    <a href="{{this::url('Listing::latestStories')}}" title='Galerie zdjęć'>Galerie zdjęć</a>
                </li>
                <li>
                    <a href="{{this::url('Listing::latestImported')}}" title='Galerie Allegro'>Galerie Allegro</a>
                </li>
                <li class='divider'></li>
                <li>
                    <a href="{{this::url('StaticContent::about')}}" title='O nas'>O nas</a>
                </li>
                <li>
                    <a href="{{this::url('StaticContent::career')}}" title='Praca'>Kariera</a>
                </li>
                <li>
                    <a href="{{this::url('StaticContent::advertisement')}}" title='Reklama w imgED'>Reklama w imgED</a>
                </li>
                <li>
                    <a href="{{this::url('User::register')}}" title='Rejestracja'>Rejestracja</a>
                </li>
                <li>
                    <a href="{{this::url('StaticContent::agreements')}}" title='Regulamin'>Regulamin</a>
                </li>
                <li>
                    <a href="{{this::url('StaticContent::privacyPolicy')}}" title='Polityka prywatności'>Polityka prywatności</a>
                </li>
                <li>
                    <a href="{{this::url('StaticContent::contact')}}" title='Kontakt'>Kontakt</a>
                </li>
            </ul>
        </div>
    </div>
    <div class='col-sm-9 col-xs-12 text-right'>
        <form class="navbar-form top-search-form" action='' method='post' role="search">
            <div class="input-group">
                <input type="text" class="form-control" value='{{$arrCurrentSearchQuery.query}}' title="Wpisz co najmniej 3 znaki" placeholder="Szukaj" name="searchQuery" />
                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                </div>
            </div>
        </form>
        <a href='{{this::url("Upload::index")}}' title='Dodaj'>
           <button class="upload-button btn btn-primary"><span class='icon glyphicon glyphicon-cloud-upload'></span>Dodaj</button>
        </a> 
        <button class="user-account-trigger btn btn-success">
            <span class='icon glyphicon glyphicon-user'></span>
            Mój imgED
            {{IF $arrLoggedUser.username}}
                @ {{$arrLoggedUser.username}}
            {{END if-list}}
        </button>
        {{IF $arrLoggedUser}}
        {{ELSE}}
            <div>
                <small><a href="{{this::url("StaticContent::AccountGuestCompare")}}" title="Porównanie kont">Po co mi darmowe konto?</a></small>
            </div>
        {{end if-list}}
        <div class="user-account-inner">
            <div class="user-account-popover-cointent">
                {{IF $arrLoggedUser}}
                    Witaj, <strong class="uname">{{$arrLoggedUser.display_name}}</strong>
                    <ul>
                        <li>&raquo; <a href='{{this::url("User::myUploads", 'najnowsze')}}'>Moje obrazki</a></li>
                        <li>&raquo; <a href='{{this::url("User::account")}}'>Moje konto</a></li>
                        <li>&raquo; <a href='{{this::url("User::logout")}}'>Wyloguj się</a></li>
                    </ul>
                {{ELSE}}
                    <div class="social-buttons">
                        <button class="btn btn-small btn-social btn-facebook"><i class="fa fa-facebook"></i> Połącz z Facebookiem</button>
                    </div>
                    lub
                    <ul>
                        <li>&raquo; <a href='{{this::url("User::login")}}'>Zaloguj się</a></li>
                        <li>&raquo; <a href='{{this::url("User::register")}}'>Załóż darmowe konto</a></li>
                    </ul>
                    
                {{END if-list}}
            </div>
        </div>
    </div>
</div>
