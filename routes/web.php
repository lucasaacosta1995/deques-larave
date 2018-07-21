<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::get('Proyecto/verEngines','ProyectoController@verEngines');
Route::get('Proyecto/index/{q}','ProyectoController@index');
Route::get('Proyecto/index','ProyectoController@index');

Route::get('Proyecto/verDocuments','ProyectoController@verDocuments');
Route::get('Proyecto/getUltimoProyecto','ProyectoController@getUltimoProyecto');
Route::get('Proyecto/actualizarProyectosSwiftype','ProyectoController@actualizarProyectosSwiftype');
Route::get('Proyecto/actualizarProyectosSwiftype/{id}','ProyectoController@actualizarProyectosSwiftype');
Route::get('Proyecto/crearDocument','ProyectoController@crearDocument');

Route::get('/home', 'HomeController@index')->name('home');
