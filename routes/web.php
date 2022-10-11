<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/get-all', 'TeacherController@getTeachers')->name('teachers');
    Route::post('/teacher/get-all', 'TeacherController@getTeachers')->name('teachers-filter');
    Route::get('/teacher/add-form', 'TeacherController@addTeacherForm')->name('teacher-form');
    Route::post('/teacher/add', 'TeacherController@addOrUpdateTeacher')->name('teacher-add');
    Route::get('/teacher/delete', 'TeacherController@getTeachers')->name('teacher-delete');
    Route::get('/teacher/update', 'TeacherController@addTeacherForm')->name('teacher-update');

    Route::get('/group/get-all', 'GroupController@getGroups')->name('groups');
    Route::post('/group/get-all', 'GroupController@getGroups')->name('groups-filter');
    Route::get('/group/add-form', 'GroupController@addGroupForm')->name('group-form');
    Route::post('/group/add', 'GroupController@addOrUpdateGroup')->name('group-add');
    Route::get('/group/delete', 'GroupController@getGroups')->name('group-delete');
    Route::get('/group/update', 'GroupController@addGroupForm')->name('group-update');
});



Auth::routes();


