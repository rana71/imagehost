<div class="m-footer">

    <div class="stats-cnt">
        <div class="stats">
            <p>
                <i>{{number_format($arrStats.items_count, 0, ',', ' ')}}</i>
                <span>obrazków jest w naszej bazie</span></p>
            <p>
                <i>{{number_format($arrStats.items_added_this_month, 0, ',', ' ')}}</i>
                <span>obrazków dodanych w tym miesiącu</span>
            </p>
        </div>
    </div>
    <div class="menu-wrap">
        <div class="row">
            <div class="menu">
                <ul>
                    <li><a href="{{this::url('User::register')}}" title='Rejestracja'>Rejestracja</a></li>
                    <li><a href="{{this::url('Upload::memeGenerator')}}" title='Generator memów'>Generator memów</a></li>
                    <li><a href="{{this::url('Listing::latestStories')}}" title='Nowe galerie zdjęć'>Nowe galerie zdjęć</a></li>
                    <li><a href="{{this::url('Listing::latestImported')}}" title='Nowe galerie Allegro'>Nowe galerie Allegro</a></li>
                    <li><a href="{{this::url('TagsList::index')}}" title='Tematy'>Tematy</a></li>
                    <li><a href="{{this::url('QueriesList::index')}}" title='Ostatnie wyszukiwania'>Ostatnie wyszukiwania</a></li>
                </ul>
                <ul>
                    <li><a href="{{this::url('StaticContent::about')}}" title='O nas'>O nas</a></li>
                    <li><a href="{{this::url('StaticContent::advertisement')}}" title='Reklama w imgED'>Reklama w imgED</a></li>
                    <li><a href="{{this::url('StaticContent::career')}}" title='Kariera'>Kariera</a></li>
                    <li><a href="{{this::url('StaticContent::agreements')}}" title='Regulamin'>Regulamin</a></li>
                    <li><a href="{{this::url('StaticContent::privacyPolicy')}}" title='Polityka prywatności'>Polityka prywatności</a></li>
                    <li><a href="{{this::url('StaticContent::contact')}}" title='Kontakt'>Kontakt</a></li>
                </ul>
            </div>
            <div class='fb-stream'>
                <div class="fb-page" data-href="https://www.facebook.com/imgedPL/" data-width="100%" data-height="380" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"></div></div>
            </div>
        </div>
        <p class='copy'>&copy; {{date('%Y')}} <span>imgED</span></p>
    </div>
</div>