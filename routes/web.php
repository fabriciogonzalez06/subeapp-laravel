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

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

//RUTAS DE PRUEBA
Route::get('/test-orm','DemoController@testOrm');
Route::get('/usuario/pruebas','UsuarioController@pruebas')->middleware(ApiAuthMiddleware::class);
Route::get('/roles/pruebas','RolController@pruebas');
Route::get('/producto/pruebas','ProductoController@pruebas');
Route::get('/categoria/pruebas','CategoriaController@pruebas');

//CONROLADOR USUARIO
Route::post('/api/usuario/registrar','UsuarioController@registro');
Route::post('/api/usuario/inicioSesion','UsuarioController@inicioSesion');
Route::post('/api/usuario/actualizar','UsuarioController@actualizar');


//Rutas formulario contacto
Route::resource('/api/contactanos','MensajeFrmContactoController');

//Rutas categorias
Route::get('/api/categoria','CategoriaController@listar');
Route::post('/api/categoria','CategoriaController@guardar');
Route::put('/api/categoria/{id}','CategoriaController@actualizar');
Route::delete('/api/categoria/{id}','CategoriaController@eliminar');


//Rutas para productos 
Route::post('/api/producto/listar','ProductoController@listar');
Route::post('/api/producto','ProductoController@guardar');
Route::put('/api/producto/{id}','ProductoController@actualizar');
Route::post('/api/producto/imagen/{id}','ProductoController@actualizarFoto');
Route::delete('/api/producto/{id}','ProductoController@eliminar');