<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    //Set the language
    $language = 'en';

    if(Cookie::has('language')) {
        $tmp = Cookie::get('language');
        $accepted = Config::get('app.languages_accepted');

        foreach($accepted as $key => $value) {
            if($tmp === $key) {
                $language = $tmp;
                break;
            }
        }
    }

    Config::set('app.language', $language);
});

App::after(function($request, $response)
{

});

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Sentry::check() === FALSE) {
		return Redirect::to('auth/login');
    }
});

Route::filter('auth.basic', function() {
	return Auth::basic();
});


/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    //if (Session::token() != Input::get('_token')) {
    //    return Response::view('error.500', array(), 404);
    //}
});
