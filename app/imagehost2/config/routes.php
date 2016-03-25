<?php
use webcitron\Subframe\Route;


Route::addReversed('/u/logowanie.html', 'User::login');
Route::addReversed('/u/rejestracja.html', 'User::register');
Route::addReversed('/u/wyloguj.html', 'User::logout');
Route::addReversed('/u/zapomniane-haslo.html', 'User::forgotPassword');
Route::addReversed('/u/aktywacja-{activation_hash}.html', 'User::accountActivation');
Route::addReversed('/u/wrzutki/{sort}/({pagination_page}/)?', 'User::myUploads');
Route::addReversed('/u/konto.html', 'User::account');
Route::addReversed('/u/{username}/', 'Listing::userUploads');

Route::addReversed('/s/o-nas.html', 'StaticContent::about');
Route::addReversed('/s/kariera.html', 'StaticContent::career');
Route::addReversed('/s/reklama.html', 'StaticContent::advertisement');
Route::addReversed('/s/polityka-prywatnosci.html', 'StaticContent::privacyPolicy');
Route::addReversed('/s/regulamin.html', 'StaticContent::agreements');
Route::addReversed('/s/kontakt.html', 'StaticContent::contact');
Route::addReversed('/s/kontakt/kariera.html', 'StaticContent::contactCareer');
Route::addReversed('/s/porownanie-kont.html', 'StaticContent::AccountGuestCompare');

Route::addReversed('/upload.html', 'Upload::index');
Route::addReversed('/memgenerator.html', 'Upload::memeGenerator');

Route::addReversed('/tagi/{tag}/', 'Listing::tag301');
Route::addReversed('/tagi/', 'TagsList::r301');
Route::addReversed('/tematy/{tag}/', 'Listing::tag');
Route::addReversed('/tematy/', 'TagsList::index');
Route::addReversed('/wyszukiwania/', 'QueriesList::index');
Route::addReversed('/ostatnie/galerie/', 'Listing::latestStories');
Route::addReversed('/ostatnie/allegro/', 'Listing::latestImported');

Route::addReversed('/{directory}/{slug}-{id}.{ext}', 'ImageDirect');
Route::addReversed('/{slug}-{id}.html', 'Details');
Route::addReversed('/{query}/', 'Listing::query');



Route::addReversed('/', 'Homepage');



