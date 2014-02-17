<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Install
/*
Route::get('install', array('as' => 'install', 'uses' => 'InstallController@getIndex'));
Route::post('install', array('uses' => 'InstallController@postInstall'));
Route::get('install/finished', array('as' => 'install/finished', 'uses' => 'InstallController@getFinished'));
Route::any('(.*)', function() {
    return Redirect::route('install');
});
return;
*/

//Home
Route::get('/', array('as' => 'home', 'uses' => 'HomeController@getIndex')); 
Route::get('help', array('as' => 'help', 'uses' => 'HomeController@getHelp')); 
Route::get('feature', array('as' => 'feature', 'uses' => 'HomeController@getFeature')); 

//Authentification
Route::get('auth/login', array('as' => 'auth/login', 'uses' => 'AuthentificationController@getLogin'));
Route::post('auth/login', array('uses' => 'AuthentificationController@postLogin'));
Route::get('auth/logout', array('as' => 'auth/logout', 'uses' => 'AuthentificationController@getLogout'));
Route::get('auth/register', array('as' => 'auth/register', 'uses' => 'AuthentificationController@getRegister'));
Route::post('auth/register', array('uses' => 'AuthentificationController@postRegister'));
Route::get('auth/activate', array('as' => 'auth/activate', 'uses' => 'AuthentificationController@getActivate'));
Route::get('auth/activate/(:any)/(:any)', array('uses' => 'AuthentificationController@getActivate'));
Route::get('auth/confirmemail', array('as' => 'auth/confirmemail', 'uses' => 'AuthentificationController@getConfirmemail'));
Route::get('auth/forgotpassword', array('as' => 'auth/forgotpassword', 'uses' => 'AuthentificationController@getForgotpassword'));
Route::post('auth/forgotpassword', array('uses' => 'AuthentificationController@postForgotpassword'));

//Language
Route::post('language/edit', array('uses' => 'LanguageController@postEdit'));

//Profile
Route::filter('pattern: profile*', 'auth');
Route::get('profile/edit', array('as' => 'profile/edit', 'uses' => 'ProfileController@getEdit'));
Route::post('profile/edit', array('uses' => 'ProfileController@postEdit'));
Route::post('profile/changepassword', array('uses' => 'ProfileController@postChangepassword'));
Route::get('profile/delete', array('as' => 'profile/delete', 'uses' => 'ProfileController@getDelete'));
Route::delete('profile/delete', array('uses' => 'ProfileController@deleteDelete'));

//Group
Route::get('group/create', array('as' => 'group/create', 'uses' => 'GroupController@getCreate'));
Route::post('group/create', array('uses' => 'GroupController@postCreate'));
Route::get('group/{num}/edit', array('as' => 'group/edit', 'uses' => 'GroupController@getEdit'));
Route::post('group/edit', array('uses' => 'GroupController@postEdit'));
Route::get('group/{num}/delete', array('as' => 'group/delete', 'uses' => 'GroupController@getDelete'));
Route::get('group/deleted', array('as' => 'group/deleted', 'uses' => 'GroupController@getDeleted'));
Route::get('groups', array('as' => 'groups', 'uses' => 'GroupController@getGroups'));
Route::get('group/{num}', array('as' => 'group', 'uses' => 'GroupController@getGroup'));

//Course
Route::get('courses', array('as' => 'courses', 'uses' => 'CourseController@getCourses'));
Route::get('courses/search', array('uses' => 'CourseController@getSearch', 'before' => 'csrf')); 
Route::get('course/create', array('as' => 'course/create', 'uses' => 'CourseController@getCreate'));
Route::post('course/create', array('uses' => 'CourseController@postCreate'));
Route::get('course/{num}', array('as' => 'course', 'uses' => 'CourseController@getCourse'));
Route::get('course/{num}/edit', array('as' => 'course/edit', 'uses' => 'CourseController@getEdit'));
Route::post('course/edit', array('uses' => 'CourseController@postEdit'));
Route::get('course/{num}/delete', array('as' => 'course/delete', 'uses' => 'CourseController@getDelete'));
Route::get('course/deleted', array('as' => 'course/deleted', 'uses' => 'CourseController@getDeleted'));

//Course - Create element
Route::get('course/{num}/catalog/create', array('as' => 'catalog/create', 'uses' => 'CatalogController@getCreate'));
Route::get('course/{num}/question/create', array('as' => 'question/create', 'uses' => 'QuestionController@getCreate'));

//Course - Import
Route::get('course/{num}/import', array('as' => 'course/import', 'uses' => 'ImportController@getCourse'));
Route::get('import/check', array('as' => 'import/check', 'uses' => 'ImportController@getCheck'));
Route::post('import/check', array('uses' => 'ImportController@postCheck'));
Route::post('import/save', array('uses' => 'ImportController@postSave'));

//Catalog
Route::get('catalog/{num}', array('as' => 'catalog', 'uses' => 'CatalogController@getCatalog'));
Route::post('catalog/create', array('uses' => 'CatalogController@postCreate'));
Route::get('catalog/{num}/edit', array('as' => 'catalog/edit', 'uses' => 'CatalogController@getEdit'));
Route::post('catalog/edit', array('uses' => 'CatalogController@postEdit'));
Route::get('catalog/{num}/delete', array('as' => 'catalog/delete', 'uses' => 'CatalogController@getDelete'));
Route::get('catalog/deleted', array('as' => 'catalog/deleted', 'uses' => 'CatalogController@getDeleted'));

//Question
Route::get('question/{num}', array('as' => 'question', 'uses' => 'QuestionController@getQuestion'));
Route::post('question/create', array('uses' => 'QuestionController@postCreate'));
Route::get('question/{num}/edit', array('as' => 'question/edit', 'uses' => 'QuestionController@getEdit'));
Route::get('question/{num}/delete', array('as' => 'question/delete', 'uses' => 'QuestionController@getDelete'));
Route::get('question/deleted', array('as' => 'question/deleted', 'uses' => 'QuestionController@getDeleted'));

//Favorites
Route::get('profile/favorites', array('as' => 'favorites', 'uses' => 'FavoriteController@getFavorites'));

//Learning
Route::get('course/{num}/learning', array('as' => 'course/learning', 'uses' => 'LearningController@getCourse'));
Route::get('catalog/{num}/learning', array('as' => 'catalog/learning', 'uses' => 'LearningController@getCatalog'));
Route::get('favorites/learning', array('as' => 'favorites/learning', 'uses' => 'LearningController@getFavorites'));

// Route group for API versioning 
Route::group(array('prefix' => 'api/v1'), function()
{
	//User
	Route::post('users/search', array('uses' => 'SearchController@postUser'));

	//Group 
	Route::post('group/user/add', array('uses' => 'GroupMemberController@postUserAdd'));
	Route::post('group/user/remove', array('uses' => 'GroupMemberController@postUserRemove'));
	Route::post('groups/search', array('uses' => 'SearchController@postGroup'));
	Route::post('group/admin/add', array('uses' => 'GroupMemberController@postAdminAdd'));
	Route::post('group/admin/remove', array('uses' => 'GroupMemberController@postAdminRemove'));
	
	//Question
	Route::post('question/edit', array('uses' => 'QuestionController@postEdit'));

	//Favorites
	Route::post('favorites/add', array('uses' => 'FavoriteController@postAdd'));
	Route::post('favorites/remove', array('uses' => 'FavoriteController@postRemove'));	

	//Learning
	Route::post('learning/next', array('uses' => 'LearningController@postNext'));


});
