<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Validator;
use App\Comprador;
use App\User;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    public function registrar(Request $request){

        if($request->isMethod('post')){
            $v=Validator::make($request->all(),[
                'nombre' => ['required', 'string', 'max:255'],
                'apellido' => [ 'required','string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
    
            if ($v->fails()) {
                return \redirect()->back()->withInput()->withErrors($v->errors());
            }

            if($request['rol_id']==1){

                $user=new User();
                 $user->nombre= $request['nombre'];
                $user->apellido = $request['apellido'];
                $user->email = $request['email'];
                $user->password = Hash::make($request['password']);
                $user->rol_id = $request['rol_id'];
                $user->save();

                $comprador=new Comprador();
                $comprador->nombre=$request['nombre'];
                $comprador->apellido=$request['apellido'];
                $comprador->correo=$request['email'];
                $comprador->user_id=$user->id;
                $comprador->tipoComprador_id=1;
                $comprador->save();

                if (\Auth::attempt(['email' => $request['email'], 'password' => $request['password']]))
                {
                    \Session::put('frontSession',$request['email']);
                    return redirect('/');
                }
               
            }
        }

        return view('auth.registrar');
        
    }

    public function iniciarSesion(Request $request){


        if($request->isMethod('post')){

            $v=Validator::make($request->all(),[
                'email' => ['required'],
                'password' => ['required', 'string', 'min:8'],
            ]);
    
            if ($v->fails()) {
                return \redirect()->back()->withInput()->withErrors($v->errors());
            }

            if (\Auth::attempt(['email' => $request['email'], 'password' => $request['password']]))
                {   
                    if(\Auth::user()->rol_id==3||\Auth::user()->rol_id==2){
                        return redirect('/home');
                    }
                    if(\Auth::user()->rol_id==1){
                        
                        \Session::put('frontSession',$request['email']);
                        return redirect('/');
                    }
                }else{
                    \flash('Los datos ingresados no coinciden con ningun registro')->important()->warning();
                    return redirect('/iniciar-sesion');
                }

       
         }
         return view('auth.iniciarSesion');
    }
    public function cerrarSesion(){
        \Auth::logout();
        \Session::forget('fronrSession');
        return redirect('/');
    }

    public function cuenta(Request $request){
        if($request->isMethod('post')){
            
        }
        return view('plantilla.tiendacontenido.cuenta');
    }

   


}