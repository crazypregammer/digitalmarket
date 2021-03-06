<?php

namespace App\Http\Controllers;
use App\User;
use App\Tienda;
use App\PlanAfilizacion;
use App\TiendaCuentaBancaria;
use App\Imagen;
use Carbon\Carbon;
use App\Pedido;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Validator;

class administradorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::findOrFail($id);
        
        if($user->rol_id=='3'){ 
            return \view('plantilla.contenido.perfil.perfilEmpleado',compact('user'));
        }
        
        if($user->rol_id=='2'){ 
            $tienda=Tienda::with('imagen')->with('tiendaCuentaBancaria')->findOrFail($user->tienda->id);
           
            return \view('plantilla.contenido.tienda.perfil.perfilTienda',compact('tienda'),compact('user'));
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $user=User::findOrFail($id);
        if($user->rol_id=='3'){ 
            return \view('plantilla.contenido.perfil.actualizarPerfil',compact('user'));
        }
        if($user->rol_id=='2'){
            $tienda=$user->tienda;
            $planAfiliacion=PlanAfilizacion::where('estatus','A')->get();
            $t=\Auth::user()->tienda->tiendaCuentaBancaria;
            $tiendaCuentaBancaria=TiendaCuentaBancaria::findOrFail($t->id);
          
            return \view('plantilla.contenido.perfil.actualizarPerfilTienda',compact('tienda','planAfiliacion','tiendaCuentaBancaria'));
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
       
        if(\Auth::user()->rol_id==3){


            $user=User::findOrFail($id);
        

            $v=Validator::make($request->all(),[
        
                'correo'=>'email|required|unique:users,email',
                
            ]);
    
            if ($v->fails()) {
                return \redirect()->back()->withInput()->withErrors($v->errors());
            }

            $user->email=$request->correo;
            $user->save();

            flash('Perfil actualizado con exito ha sido cambiada con exito!')->succes()->important();


            return \redirect()->route('administrador.show',$user);
      

        }

        if(\Auth::user()->rol_id=='2'){
           
     
            $v=Validator::make($request->all(),[
        
                'correo'=>'email|required|unique:tiendas,correo,'.$id
                
            ]);
    
            if ($v->fails()) {
                return \redirect()->back()->withInput()->withErrors($v->errors());
            }
         

            
            
            
            
            
            $user=User::findOrFail(\Auth::user()->id);
            $user->email=$request->correo;
            $user->nombre=$request->nombre;
            $user->apellido=$request->apellido;
            $user->save();
            
            $tienda=Tienda::findOrFail($id);
            $tienda->nombreTienda=$request->nombreTienda;
            $tienda->nombre=$request->nombre;
            $tienda->apellido=$request->apellido;
            $tienda->correo=$request->correo;

            $pedido=Pedido::whereHas('producto',function($q) use($tienda){
                $q->where('tienda_id',\Auth::user()->tienda->id);
                })->where('status','!=','culminado')->count();
            
            
            if($tienda->planAfilizacion_id!=$request->planAfiliacion){

                if($pedido>0){
                    flash('Lo sentimos. No puede afiliarse a un plan si posee pedidos en proceso de compra.')->warning()->important();
                    return \redirect()->route('administrador.show',$user);
                }else{

                    $tienda->planAfilizacion_id=$request->planAfiliacion;
                    $tienda->fechaPlanAfiliacion=Carbon::now()->format('Y-m-d H:i:s');
                }

            }




            $tienda->telefono=$request->telefono;
            
            $tienda->save();

            if($request->imagen){

                if($tienda->imagen){
                    $imagenn=$tienda->imagen;
                    $archivo=substr($imagenn->url,1);
                    \File::delete($archivo);
                    $imagenn->delete();
                }

                $imagen=$request->file('imagen');
               
                $nombre=time().'_'.$imagen->getClientOriginalName();
                $ruta=public_path().'/imagenes/tienda';
                $imagen->move($ruta , $nombre);
    
                $urlImagen['url']='/imagenes/tienda/'.$nombre;
                $tienda->imagen()->create($urlImagen);
            }

            flash('Perfil modificado  con exito!')->success()->important();


            return \redirect()->route('administrador.show',$user);

        }
        
    }

    public function showPassword($id){

        $user=User::findOrFail($id);
        return view('plantilla.contenido.perfil.actualizarContraseña',compact('user'));

    }

    public function updatePassword(request $request ,$id){
        
        $v=Validator::make($request->all(),[
        
            'password'=>'required|string|min:8|confirmed'
            
        ]);

        if ($v->fails()) {
            return \redirect()->back()->withInput()->withErrors($v->errors());
        }


        $user=User::findOrFail($id);
        if(Hash::check($request->vieja,$user->password)){
  
            $user->password =  Hash::make($request->password);
            $user->save();
            flash('la contraseña ha sido actualizada con exito!')->success()->important();
            return redirect()->route('administrador.show',$user);   
         }
         else{
            flash('la contraseña ingresada no coincide con la registrada!')->warning()->important();

            return redirect()->route('Empleado.password',$user);
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
        //
    }
}
