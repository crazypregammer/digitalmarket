<?php
use App\User;
use App\Imagen;
use App\Categoria;

//Rutas para imagenes:
//1)
Route::get('/prueba', function () {

   $producto=App\Producto::with('imagen','subCategoria')->orderBy('id','DESC')->get();
   return $producto;
});

//2)
//Mostrar resultados 
Route::get('/resultados', function () {
    
    $imagen=App\Imagen::orderBy('id','Desc')->get();
    return $imagen; 
});

Route::get('/', function () {
    return view('tienda.index');
/*
    $user=new User();
    $user->nombre= 'jhon';
    $user->apellido = 'contreras';
    $user->email = 'jhjacoz96@gmail.com';
    $user->password = Hash::make('12345678');
    $user->rol_id =3;
    $user->save();
*/

});


Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');


Route::resource('Comprador', 'compradorController');

//Route::get('Perfil/{usuario}','controladorAdministrador@show');

Route::resource('administrador', 'administradorController');

Route::get('ActualizarContraseña/{user}','administradorController@showPassword')->name('Empleado.password');

Route::post('ActualizarContraseña/{user}','administradorController@updatePassword')->name('Empleado.updatePassword');

//Administrador cambia contraseña de cliente
Route::get('Contraseña/{comprador}','compradorController@showPassword')->name('Comprador.password');

Route::post('ActualizarContraseña/{comprador}','compradorController@updatePassword')->name('comprador.updatePassword');

Route::resource('Plan', 'planController');

Route::resource('categoria','categoriaController');
Route::resource('SubCategoria','subCategoriaController');

Route::get('traerCategoria/{categoria}',
'subCategoriaController@traer')->name('traerCategoria.traer');

Route::get('obtenerCategoria/{categoria_id}','productoController@getSubCategoria');
Route::resource('producto','productoController');

Route::delete('/eliminarImagen/{id}','productoController@eliminarImagen')->name('delete.imagen');

//autocomplete
route::get('/autoComplete','HomeController@autoComplete')->name('autocomplete');

//crear filtro direccion direcciones 
route::resource('filtroDireccion','filtroDireccionController');

