<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use App\Categoria;
use App\SubCategoria;
use App\Combinacion;
use App\Atributo;
use App\Tienda;
use App\Direccion;
use App\GrupoAtributo;
use App\Imagen;
use App\Carrito;
use App\TipoComprador;


use Illuminate\Support\Facades\File;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Validator;


class productoController extends Controller
{

   /* public function __construct()
    {
        $this->middleware('auth');
    }*/
    

    public function index(Request $request)
    {
        
        
        if(\Auth::user()->rol_id==3){
            
            $nombre=$request->get('nombre');
              
              $producto=Producto::with('imagen','subCategoria')->where('nombre','like',"%$nombre%")->paginate(2);
              
              return view('plantilla.contenido.admin.producto.consultar',compact('producto'));

        }else{

            $nombre=$request->get('nombre');
              
              $producto=Producto::with('imagen','subCategoria')->where('nombre','like',"%$nombre%")->where('tienda_id',\Auth::user()->tienda->id)->paginate(2);
              
              return view('plantilla.contenido.tienda.producto.consultar',compact('producto'));

        }
    
    }

    
    

    public function Categoria(){
        $categoria=Categoria::orderBy('nombre')->get();
        return $categoria;
    }

    public function create()
    {
        $categoria=Categoria::orderBy('nombre')->get();
        if(\Auth::user()->rol_id==3){
            $rol=\Auth::user()->rol_id;
        return \view('plantilla.contenido.admin.producto.crear',compact('categoria','rol'));
        }else{
            $planAfiliacion=\Auth::user()->tienda->planAfiliacion;

            if($planAfiliacion->cantidadPublicacion!=''){
                $tienda=\Auth::user()->tienda;
                $cantidad=Producto::where('tienda_id',$tienda->id)->where('status','si')->count();
                if($cantidad+1>$planAfiliacion->cantidadPublicacion){
                    $producto=Producto::where('tienda_id',$tienda->id)->get();
                    \flash('Ha superado la cantidad de productos activos. Si desea seguir publicando productos, debe afiliace a uno de nuestros planes con mayor cantidad de publicaciones.')->warning()->important();
                    return redirect()->route('tiendas.producto.index',compact('producto'));
                }

            }
            $rol=\Auth::user()->rol_id;
            $tienda_id=\Auth::user()->tienda->id;
            return \view('plantilla.contenido.tienda.producto.crear',compact('categoria','tienda_id','rol'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        
        $v=Validator::make($request->all(),[
            'nombre'=>'min:2|required|unique:productos,nombre',
            'slug'=>'min:2|required|unique:productos,slug',
            'imagenes.*'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048'
            
        ]);

        if ($v->fails()) {
            return \redirect()->back()->withInput()->withErrors($v->errors());
        }
        

        
        $urlImagenes=[];
        if ($request->hasFile('imagenes')) {
            
            $imagenes=$request->file('imagenes');

            foreach ($imagenes as $imagen) {

                $nombre=time().'_'.$imagen->getClientOriginalName();
                $ruta=public_path().'/imagenes';
                $imagen->move($ruta , $nombre);

                $urlImagenes[]['url']='/imagenes/'.$nombre;

            }
            //return $urlImagenes;
        }
        
        
        if(\Auth::user()->rol_id==3){
            $tienda=Tienda::where('codigo',$request->tienda)->first();
            $planAfiliacion=$tienda->planAfiliacion;
    
                if($planAfiliacion->cantidadPublicacion!=''){
                  
                    $cantidad=Producto::where('tienda_id',$tienda->id)->where('status','si')->count();
                    if($cantidad+1>$planAfiliacion->cantidadPublicacion){
                        $producto=Producto::All();
                        \flash('Ha superado la cantidad de productos activos. Si desea seguir publicando productos, debe afiliace a uno de nuestros planes con mayor cantidad de publicaciones.')->warning()->important();
                        return redirect()->route('producto.index',compact('producto'));
                    }
                }

              
        }


        
        if($request->tipoProd=='combinacion'){
            $d=json_decode(($request['value']),true);
            if($d!=null){
                $cantidad=0;
                for ($i=0; $i < count($d) ; $i++) { 
                    $cantidad=$cantidad+$d[$i]['cantidad'];
                }
                $request->cantidad=$cantidad;
              
                    if(\Auth::user()->rol_id==3){
                        $tienda=Tienda::where('codigo',$request->tienda)->first();
                        $planAfiliacion=$tienda->planAfiliacion;
                    
                        if($cantidad>$planAfiliacion->tiempoPublicacion){
                            
                            $producto=Producto::All();
                            \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                            return redirect()->route('tiendas.producto.index',compact('producto'));
                            
                        }
                    }else{
                        $tienda=\Auth::user()->tienda;
                        $planAfiliacion=$tienda->planAfiliacion;
                        $f=$planAfiliacion->tiempoPublicacion;
                        if($cantidad>$planAfiliacion->tiempoPublicacion){
                            
                            \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                            
                                $producto=Producto::where('tienda_id',$tienda->id)->get();
                                return redirect()->route('tiendas.producto.index',compact('producto'));
                            
                       
                        }
                    }
                    
              
            }
        }else{
            if(\Auth::user()->rol_id==3){
                $tienda=Tienda::where('codigo',$request->tienda)->first();
                $planAfiliacion=$tienda->planAfiliacion;
            
                if($request->cantidad>$planAfiliacion->tiempoPublicacion){
                    
                    $producto=Producto::All();
                    \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                    return redirect()->route('tiendas.producto.index',compact('producto'));
                }
            }else{
                $tienda=\Auth::user()->tienda;
                $planAfiliacion=$tienda->planAfiliacion;
                $f=$planAfiliacion->tiempoPublicacion;
                if($request->cantidad>$planAfiliacion->tiempoPublicacion){
                    
                    \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                    
                        $producto=Producto::where('tienda_id',$tienda->id)->get();
                        return redirect()->route('tiendas.producto.index',compact('producto'));

                }
            }
        }

      


        $producto= new Producto();

        $producto->nombre=$request->nombre;
        $producto->slug=$request->slug;
        
            $producto->cantidad=$request->cantidad;

        
        $producto->subCategoria_id=$request->subCategoria_id;
        $producto->precioAnterior=$request->precioAnterior;
        $producto->precioActual=$request->precioActual;
        $producto->porcentajeDescuento=$request->porcentajeDescuento;
        $producto->descripcionCorta=$request->descripcionCorta;
        $producto->descripcionLarga=$request->descripcionLarga;
        $producto->especificaciones=$request->especificaciones;
        $producto->datosInteres=$request->datosInteres;
    
        
        if(\Auth::user()->rol_id==2){
            $producto->tienda_id=\Auth::user()->tienda->id;

            
        if($request->sliderPrincipal){
            $producto->sliderPrincipal='si';
        }else{  
            $producto->sliderPrincipal='no';
        }

        if($request->status){
            $producto->status='si';
        }else{
            $producto->status='no';
        }

        if($request->tipoProd=='comun'){
            $producto->tipoCliente='comun';
        }else{

            if($request->tipoProd=='combinacion'){
    
                $producto->tipoCliente='combinacion';
            }
        }
        
    
  

        $producto->save();

        $producto->imagen()->createMany($urlImagenes);

        if($producto->tipoCliente=='combinacion'){
            $d=json_decode(($request['value']),true);
            if($d!=null){
                for ($i=0; $i < count($d) ; $i++) { 
                    $combinacion = new Combinacion();
                    $combinacion->cantidad=$d[$i]['cantidad'];
                    $combinacion->producto_id=$producto->id;
                    $combinacion->save();
                    $s=$d[$i]['elemento'];
                    for ($j=0; $j <count($s) ; $j++) { 
                        $combinacion->atributo()->attach($s[$j]['id']);
                    }
                }
            }
        }
       \flash('Producto creado con exito')->success()->important();

        return \redirect()->route('tiendas.producto.index');

        }else{
            $tienda=Tienda::where('codigo',$request->tienda)->first();

            $producto->tienda_id=$tienda->id;

            
        if($request->sliderPrincipal){
            $producto->sliderPrincipal='si';
        }else{  
            $producto->sliderPrincipal='no';
        }

        if($request->status){
            $producto->status='si';
        }else{
            $producto->status='no';
        }

        if($request->tipoProd=='comun'){
            $producto->tipoCliente='comun';
        }else{

            if($request->tipoProd=='combinacion'){
    
                $producto->tipoCliente='combinacion';
            }
        }
        
  

        $producto->save();

        $producto->imagen()->createMany($urlImagenes);

        if($producto->tipoCliente=='combinacion'){    
            $d=json_decode(($request['value']),true);
            if($d!=null){
                for ($i=0; $i < count($d) ; $i++) { 
                    $combinacion = new Combinacion();
                    $combinacion->cantidad=$d[$i]['cantidad'];
                    $combinacion->producto_id=$producto->id;
                    $combinacion->save();
                    $s=$d[$i]['elemento'];
                    for ($j=0; $j <count($s) ; $j++) { 
                        $combinacion->atributo()->attach($s[$j]['id']);
                    }
                }
            }
        }
       \flash('Producto creado con exito')->success()->important();

        return \redirect()->route('producto.index');

        }

        //$Producto->tipoProducto=$request->tipoProd;



    }

    public function getSubCategoria($id){
        
    $subCategorias=SubCategoria::where('categoria_id',$id)->get();
           return $subCategorias;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {

        if(Producto::where('slug',$slug)->first()){
            return 'slug existe';
        }
        else{
            return 'Slug disponible';
        }
    }

    public function eliminarImagen($id)
    {

        //return 'elimado'.$id;
        $imagen=Imagen::findOrFail($id);
        $archivo=substr($imagen->url,1);
        $eliminar=File::delete($archivo);
        $imagen->delete();

        return "eliminado id:".$id.''.$eliminar; 

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        
        $producto=Producto::with('imagen','subCategoria','combinacion')->where('slug',$slug)->firstOrFail();
        
        $categoria=Categoria::orderBy('nombre')->get();
        
        if(\Auth::user()->rol_id==3){
            $rol=\Auth::user()->rol_id;
        return view('plantilla.contenido.admin.producto.modificar',compact('producto','categoria','rol'));
        }else{
            $rol=\Auth::user()->rol_id;
            $tienda_id=\Auth::user()->tienda->id;
            return view('plantilla.contenido.tienda.producto.modificar',compact('producto','categoria','rol','tienda_id'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        $v=Validator::make($request->all(),[
            'nombre'=>'min:2|required|unique:productos,nombre,'.$id,
            'slug'=>'min:2|required|unique:productos,slug,'.$id,
            'imagenes.*'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            
        ]);

        if ($v->fails()) {
            return \redirect()->back()->withInput()->withErrors($v->errors());
        }

        $urlImagenes    =[];

        if ($request->hasFile('imagenes')) {
            
            $imagenes=$request->file('imagenes');

            foreach ($imagenes as $imagen) {

                $nombre=time().'_'.$imagen->getClientOriginalName();
                $ruta=public_path().'/imagenes';
                $imagen->move($ruta , $nombre);

                $urlImagenes[]['url']='/imagenes/'.$nombre;

            }
            //return $urlImagenes;
        }

        
            if(\Auth::user()->rol_id==3){
                $tienda=Tienda::where('codigo',$request->tienda)->first();
                $planAfiliacion=$tienda->planAfiliacion;
            
                if($request->cantidad>$planAfiliacion->tiempoPublicacion){
                    
                    $producto=Producto::All();
                    \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                    return redirect()->route('tiendas.producto.index',compact('producto'));
                }
            }else{
                $tienda=\Auth::user()->tienda;
                $planAfiliacion=$tienda->planAfiliacion;
                $f=$planAfiliacion->tiempoPublicacion;
                if($request->cantidad>$planAfiliacion->tiempoPublicacion){
                    
                    \flash('Ha superado el stock maximo del plan de afiliación al que pertenece. Si desea tener un limite mayor puede cambiar su plan de afiliación .')->warning()->important();
                    
                        $producto=Producto::where('tienda_id',$tienda->id)->get();
                        return redirect()->route('tiendas.producto.index',compact('producto'));

                }
            }
        




        $producto=Producto::findOrFail($id);

        $producto->nombre=$request->nombre;
        $producto->slug=$request->slug;
        $producto->cantidad=$request->cantidad;
        $producto->subCategoria_id=$request->subCategoria_id;
        $producto->precioAnterior=$request->precioAnterior;
        $producto->precioActual=$request->precioActual;
        $producto->porcentajeDescuento=$request->porcentajeDescuento;
        $producto->descripcionCorta=$request->descripcionCorta;
        $producto->descripcionLarga=$request->descripcionLarga;
        $producto->especificaciones=$request->especificaciones;
        $producto->datosInteres=$request->datosInteres;
        $producto->status=$request->status;
        
        if($request->sliderPrincipal){
            $producto->sliderPrincipal='si';
        }else{  
            $producto->sliderPrincipal='no';
        }

        if($request->status){

            if(\Auth::user()->rol_id==3){
                $tienda=Tienda::where('codigo',$request->tienda)->first();
                $planAfiliacion=$tienda->planAfiliacion;
        
                    if($planAfiliacion->cantidadPublicacion!=''){
                      
                        $cantidad=Producto::where('tienda_id',$tienda->id)->where('status','si')->count();
                        if($cantidad+1>$planAfiliacion->cantidadPublicacion){
                            $producto=Producto::All();
                            \flash('Ha superado la cantidad de productos activos. Si desea seguir publicando productos, debe afiliace a uno de nuestros planes con mayor cantidad de publicaciones.')->warning()->important();
                            return redirect()->route('producto.index',compact('producto'));
                        }
                    }
    
                  
            }else{

                $planAfiliacion=\Auth::user()->tienda->planAfiliacion;

                if($planAfiliacion->cantidadPublicacion!=''){
                    $tienda=\Auth::user()->tienda;
                    $cantidad=Producto::where('tienda_id',$tienda->id)->where('status','si')->count();
                    if($cantidad+1>$planAfiliacion->cantidadPublicacion){
                        $producto=Producto::where('tienda_id',$tienda->id)->get();
                        \flash('Ha superado la cantidad de productos activos. Si desea seguir publicando productos, debe afiliace a uno de nuestros planes con mayor cantidad de publicaciones.')->warning()->important();
                        return redirect()->route('tiendas.producto.index',compact('producto'));
                    }

                }



            }

                $producto->status='si';
            

        }else{
            $producto->status='no';
        }

        if($request->tipoProd=='comun'){
            $producto->tipoCliente='comun';
        }else{

            if($request->tipoProd=='combinacion'){
    
                $producto->tipoCliente='combinacion';
            }
        }
        
        if(\Auth::user()->rol_id==3){
            $producto->tienda->id=$request->tienda_id;
        }else{
            $producto->tienda_id=\Auth::user()->tienda->id;
        }

        $producto->save();

        $producto->imagen()->createMany($urlImagenes);
        if($producto->tipoCliente=='combinacion'){
            $d=json_decode(($request['value']),true);
            if($d!=null){
                for ($i=0; $i < count($d) ; $i++) { 
                    $combinacion = new Combinacion();
                    $combinacion->cantidad=$d[$i]['cantidad'];
                    $combinacion->producto_id=$producto->id;
                    $combinacion->save();
                    $s=$d[$i]['elemento'];
                    for ($j=0; $j <count($s) ; $j++) { 
                        $combinacion->atributo()->attach($s[$j]['id']);
                    }
                }
            }
        }

       \flash('Producto actualizado con exito')->success()->important();
        if(\Auth::user()->rol_id==3){

            return \redirect()->route('producto.edit',$producto->slug);
        }else{
            return \redirect()->route('tiendas.producto.edit',$producto->slug);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $producto=Producto::with('imagen')->findOrFail($id);
        
        foreach ($producto->imagen as $imagen) {
           
            $archivo=substr($imagen->url,1);
            File::delete($archivo);
            $imagen->delete();
        }

        $producto->delete();

        flash('producto eliminado con exito')->important()->success();

        return  redirect()->route('producto.index');
    }

    public function obtenerTienda($codigo){
        
        if(Tienda::where('codigo',$codigo)->first()){
            $tienda=Tienda::where('codigo',$codigo)->first();
            return $tienda;
        }else{
            return 'No hay registros de esta tienda';
                }
      
    }


   //CONROLADOR DE LA TIENDA

//filtar productos por categoria

    public function productoCategoria($slug){
        
        
        if(subCategoria::where('slug',$slug)->count()==0){
            abort(404);
        }else{

            $categoria=Categoria::where('estatus','A')->get();
        
        $subCategoria=subCategoria::where('slug',$slug)->with('producto')->first();
        
        
        
            return view('plantilla.tiendaContenido.filtroCategoria.subCategoria',compact('subCategoria'),compact('categoria'));
        }

    }

    public function mainProductoCategoria($slug){
        
        if(Categoria::where('estatus','A')->where('slug',$slug)->count()==0){
            abort(404);
        }else{
            $categoria=Categoria::where('estatus','A')->get();
    
        $mainCategoria=Categoria::where('estatus','A')->where('slug',$slug)->with('subCategoria')->first();
        
        
    
            return view('plantilla.tiendaContenido.filtroCategoria.categoria',compact('mainCategoria'),compact('categoria'),compact('subCategoria'));
        }
    }
    //fin de filtros por categoria

    //DETALLES DE PRODUCTO

    public function detalleProducto($slug){
        $categoria=Categoria::where('estatus','A')->get();

        $producto=Producto::where('status','si')->where('slug',$slug)->first();
       
        $relacionProducto=Producto::where('slug','!=',$slug)->where('subCategoria_id',$producto->subCategoria_id)->get();
        
    

        /*$combinacion=Combinacion::where('producto_id',$producto->id)->with('atributo')->get();
    
        $grupo=$producto->combinacion[0]->atributo;
        
        $grupos=[];
        for ($i=0; $i < count($grupo) ; $i++) { 
        $f= $grupo[$i]->grupoAtributo;
            $atributo=[];

        for ($j=0; $j <count($combinacion) ; $j++) { 
                $item=$combinacion[$j]['atributo'];

                for ($k=0; $k < count($item) ; $k++) { 

                    if($item[$k]['grupoAtributo_id']==$f['id']){
                    $f=\Arr::add($f,'atributo',$item[$k]);
                    }

                }

        }

        \array_push($grupos,$f);
        }

        $atributo=Atributo::where('id',3)->with('combinacion')->first();
        */
        
        

        return view('plantilla.tiendaContenido.producto.detalle',compact('producto','categoria','relacionProducto'));
    }

    //FIN DE DETALLES DE PRODUCTOS

    public function agregarCarrito(Request $request){

        \Session::forget('montoCupon');
        \Session::forget('codigoCupon');
        
        $producto=Producto::where('id',$request->producto_id)->first();
       
        if(empty($request['comprador_id'])){
            $request['comprador_id']='';
        }
        
        $session_id=\Session::get('session_id');
        
        if(empty($session_id)){

            $session_id=\Str::random(40);
            \Session::put('session_id',$session_id);

        }



        if($producto->tipoCliente=='comun'){
        
        $cantidadProducto=\DB::table('carritos')->where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id])->count();
       
            if( $cantidadProducto>0){

                $carrito=Carrito::where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id])->first();

                if($request['cantidad']+$carrito->cantidad>$producto->cantidad){

                    \flash('No esta disponible esta cantidad')->warning()->important();
                    return redirect('/detalleProducto/'.$producto->slug);

                }else{

                    \DB::table('carritos')->where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id])->increment('cantidad',$request['cantidad']);

                }

            }else{
                
                
                    \DB::table('carritos')->insert(['producto_id'=>$request['producto_id'],'precio'=>$request['precio'],'cantidad'=>$request['cantidad'],'comprador_id'=>1,'session_id'=>$session_id]);

            
            }
        }else{


            $cantidadProducto=\DB::table('carritos')->where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id,'combinacion_id'=>$request['combinacion_id']])->count();
            
            if( $cantidadProducto>0){

                
                $carrito=Carrito::where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id,'combinacion_id'=>$request['combinacion_id']])->first();
                
                $combinacion=Combinacion::where('id',$request['combinacion_id'])->first();
                             
                
                if($request['cantidad']+$carrito->cantidad>$combinacion->cantidad){

                    \flash('No esta disponible esta cantidad')->warning()->important();
                    return redirect('/detalleProducto/'.$producto->slug);

                }else{


                    \DB::table('carritos')->where(['producto_id'=>$request['producto_id'],'session_id'=>$session_id,'combinacion_id'=>$request['combinacion_id']])->increment('cantidad',$request['cantidad']);
                }
            }else{
                
                \DB::table('carritos')->insert(['producto_id'=>$request['producto_id'],'precio'=>$request['precio'],'cantidad'=>$request['cantidad'],'comprador_id'=>1,'session_id'=>$session_id,'combinacion_id'=>$request['combinacion_id']]);
            
                
            }


        }

        
        return redirect()->route('carrito');


    } 

    public function carrito(){

       $session_id=\Session::get('session_id');
       $userCarrito=\DB::table('carritos')->where(['session_id'=>$session_id])->get();


       $descuentoTipoComprador=\Auth::user()->comprador->tipoComprador->porcentajeDescuento;
        $totalCantidad=0;
        foreach($userCarrito as $item){
            $totalCantidad=$totalCantidad+($item->precio*$item->cantidad);
        }
        $montoDescuentoTipoComrador=$totalCantidad*($descuentoTipoComprador/100);

        \Session::put('$montoDescuentoTipoComrador',$montoDescuentoTipoComrador);

        return view('plantilla.tiendaContenido.producto.carrito',compact('userCarrito'));
    }

    public function eliminarProductoCarrito($id){
        \Session::forget('montoCupon');
        \Session::forget('codigoCupon');

        \DB::table('carritos')->where('id',$id)->delete();

        return redirect()->route('carrito');
    }

    public function actualizarProductoCarrito($id=null,$cantidad=null){

        \Session::forget('montoCupon');
        \Session::forget('codigoCupon');

        $obtenerCarrito=\DB::table('carritos')->where('id',$id)->first();
        $obtenerProducto=Producto::where('id',$obtenerCarrito->producto_id)->first();

        $actualizarCantidad=$obtenerCarrito->cantidad+$cantidad;

        if($obtenerProducto->tipoCliente=='comun'){

            if($obtenerProducto->cantidad>=$actualizarCantidad){
    
                \DB::table('carritos')->where('id',$id)->increment('cantidad',$cantidad);
                return redirect()->route('carrito');
    
            }else{
                
                flash('Esta cantidad exede la existente')->warning()->important();
                return redirect()->route('carrito');
            }

        }else{

            $combinacion=Combinacion::where('id',$obtenerCarrito->combinacion_id)->first();

            if($combinacion->cantidad>=$actualizarCantidad){
    
                \DB::table('carritos')->where('id',$id)->increment('cantidad',$cantidad);
                return redirect()->route('carrito');
    
            }else{
                flash('Esta cantidad exede la existente')->warning()->important();
                return redirect()->route('carrito');
            }

        }

    }



    public function checkout(){
        $direcciones=Direccion::where('comprador_id',\Auth::user()->comprador->id)->get();
        
        return view('plantilla.tiendaContenido.checkout',compact('direcciones'));
    }
    
    public function revisarPedido(){

    }
}