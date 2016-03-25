<?php
use webcitron\Subframe\Route;

Route::addReversed('/', 'Dashboard::index');
Route::addReversed('/logowanie/', 'Login::login');
Route::addReversed('/wyloguj/', 'Login::logout');
Route::addReversed('/odzyskiwanie-hasla/', 'Login::passwordRecovery');

Route::addReversed('/uzytkownicy/', 'User::overview');
Route::addReversed('/uzytkownicy/zablokowany-upload/', 'User::disallowUpload');

Route::addReversed('/administratorzy/', 'Admin::overview');
Route::addReversed('/administratorzy/dodaj/', 'Admin::add');
Route::addReversed('/administratorzy/{id}/', 'Admin::edit');

Route::addReversed('/artefakty/strona-glowna/', 'Artifact::onHomepage');
Route::addReversed('/artefakty/lista/', 'Artifact::manage');

Route::addReversed('/reklama/', 'Adverts::index');



