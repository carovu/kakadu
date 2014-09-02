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
{   //this filter is necessary for other domains post request. a optionsrequest is sent before post.
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      header('Access-Control-Allow-Origin: http://localhost:9000');
      header('Access-Control-Request-Method: POST, PUT, DELETE');
      header('Access-Control-Allow-Headers: X-Requested-With, X-CSRF-Token, Content-Type, Accept, Host, Origin');
      header('Access-Control-Allow-Credentials: true');
       
      exit;
    }
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
    //laravelclient
    if(Input::get('_token')){
        if (Session::token() != Input::get('_token')) {
          //throw new Illuminate\Session\TokenMismatchException;
          return Response::view('error.500', array(), 404);
      }
    //angularjsclient
    }else{
        if (Session::token() != Request::header('X-CSRF-Token')) {
          //throw new Illuminate\Session\TokenMismatchException;
          return Response::view('error.500', array(), 404);
      }
    }
});

/*
|--------------------------------------------------------------------------
| CORS Filter
|--------------------------------------------------------------------------
|
|Because our client runs parallel to our existing laravel-client, the sent 
|requests are going between two different domains, which is why we need cross
|origin resource sharing (CORS) between our server and client.
|
*/

Route::filter('cors', function($response){
  header('Access-Control-Allow-Origin: http://localhost:9000');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header('Access-Control-Allow-Headers: Accept, Host, Origin, Cookie');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 7200'); 
});