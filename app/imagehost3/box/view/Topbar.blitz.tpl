<div class="m-top">
    <div class="logo">
        <a href="{{this::url("Homepage")}}" title="imgED - Strona główna"><img src="/imagehost3/img/logo.png" alt="imgED" /></a>
    </div>
    <div class="left">
        <ul>
            <li class='search'>
                <form method="post">
                    <input type='text' placeholder='Czego szukasz?' name='q' title="Wyszkaj min 3 znaki" class='q' pattern=".{3,}" />
                    <div class='icon'></div>
                    <span class='text'>Szukaj</span>
                    <input type='submit' class='s' value='' />
                </form>
            </li>
            <li class='browse'>
                <a href='{{this::url('Listing::onHomepage')}}' title='Najlepsze'><span class='text'>Najlepsze</span></a>
            </li>
            {{IF $arrRandomArtifact}}
                <li class='random'>
                    <a href='{{this::url('Details', $arrRandomArtifact.slug, $arrRandomArtifact.id)}}' title='Losowo'><span class='text'>Losowo</span></a>
                </li>
            {{END if-list}}
        </ul>
    </div>
    <div class="right">
        <div class="fb-like" data-href="https://www.facebook.com/imgedPL/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
        <a href="{{this::url("Upload::index")}}" title="Dodaj" class='upload'><span class='text'>Dodaj</span></a>
        <button class="my-imged" title="Mój imgED">
            <span class='text'>
                {{IF $arrLoggedUser.display_name}}
                    @{{$arrLoggedUser.display_name}}
                {{ELSE}}
                    Mój imgED
                {{END if-list}}
            </span>
        </button>
    </div>
    
    <div class="templates">
        <div id="user-modal" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">
                            {{IF $arrLoggedUser.display_name}}@{{$arrLoggedUser.display_name}} - {{END if-list}}Twój imgED
                        </h4>
                    </div>

                    <div class="modal-body">
                        {{IF $arrLoggedUser.display_name}}
                            <ul>
                                <li>&raquo; <a href='{{this::url("User::myUploads", 'najnowsze')}}'>Moje obrazki</a></li>
                                <li>&raquo; <a href='{{this::url("User::account")}}'>Moje konto</a></li>
                                <li>&raquo; <a href='{{this::url("User::logout")}}'>Wyloguj się</a></li>
                            </ul>
                        {{ELSE}}

                            <form action='' method='post' class='login-form'>
                                <div class='r'>
                                    <label for='login-username'>Nazwa użytkownika *</label> 
                                    <input id='login-username' type='test' name='login-username' placeholder='Nazwa użytkownika *' />
                                </div>

                                <div class='r'>
                                    <label for='login-password'>Hasło *</label> 
                                    <input id='login-password' type='password' name='login-password' placeholder='Hasło *' />
                                </div>
                                
                                <div class='r'>
                                    <a href='{{this::url("User::forgotPassword")}}' class='forget-password'>Nie pamiętasz hasła?</a>
                                </div>

                                <div class='b'>
                                    <input type="button" class="button sign-fb" value="Zaloguj przez Facebook" />
                                    <input class='sign-in' type='submit' value='Ładuję formularz...' data-value='Zaloguj' disabled='disabled' />
                                </div>
                                
                                <p class="l"><span>lub</span></p>
                                
                                <div class='b sign-up'>
                                    <a href='{{this::url("User::register")}}'>Załóż darmowe konto</a>
                                </div>

                            </form>
                        {{END if-list}}
                        
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
<div class='m-top-margin'></div>

