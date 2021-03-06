<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GrupoAtributo;
use App\Atributo;
use App\Combinacion;
use App\Producto;
use App\PlanAfilizacion;


class atributoController extends Controller
{




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $combinacion=Combinacion::with('atributo')->get();

        return view('plantilla.contenido.admin.combinacion.crear',compact('combinacion'));
    }

 

    public function guardarCombinacion(Request $request){
        $d=json_decode(($request['value']),true);
        for ($i=0; $i < count($d) ; $i++) { 
            $combinacion = new Combinacion();
            $combinacion->cantidad=$d[$i]['cantidad'];
            $combinacion->save();
            $s=$d[$i]['elemento'];
            for ($j=0; $j <count($s) ; $j++) { 
                $combinacion->atributo()->attach($s[$j]['id']);
            }

        }

        return 'dd';        
    }

    public function create()
    {
        $ga=GrupoAtributo::with('atributo')->get();
        
        return $ga;

    }

    public function buscarGrupo($id){
        
        $ga=GrupoAtributo::where('tienda_id',$id)->with('atributo')->get();
        return $ga;
    }
       
    public function grupoCombinacion($slug){
        
        $producto=Producto::where('status','si')->where('slug',$slug)->first();


        $combinacion=Combinacion::where('producto_id',$producto->id)->with('atributo')->get();
        
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

        return $grupos;


    }

    public function combinacion($slug){

        $producto=Producto::where('status','si')->where('slug',$slug)->first();


        $combinacion=Combinacion::where('producto_id',$producto->id)->where('status','A')->with('atributo')->get(); 

        return $combinacion;

    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $grupoAtributo=new GrupoAtributo();
        $grupoAtributo->nombre=$request->nombre;

        $grupoAtributo->save();

        for ($i=0; $i < count($request->atributos) ; $i++) { 
            
            $atributo=new Atributo();

           $atributo->nombre = $request->atributos[$i];
           $atributo->grupoAtributo_id= $grupoAtributo->id;
            $atributo->save();
        }

        $g=GrupoAtributo::where('id',$grupoAtributo->id)->with('atributo')->first();
        
        return $g;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $combinacion=Combinacion::with('atributo')->where('producto_id',$id)->where('status','A')->get();
        return $combinacion;
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
         $combinacionEspecifica=Combinacion::findOrfail($id);
         $producto= $combinacionEspecifica->producto;
         $combinaciones=$producto->combinacion;

         $cantidad=0;
            
         for ($i=0; $i <count($combinaciones) ; $i++) { 
             if( $combinacionEspecifica->id===$combinaciones[$i]['id']){
                
                $cantidad=$cantidad+$request->cantidad;
                
             }else{ 

                $cantidad=$cantidad+$combinaciones[$i]['cantidad'];

             }
            
         }

       
        
         $plan=PlanAfilizacion::where('id',$producto->tienda->planAfiliacion->id)->first();
        
        if($cantidad>$plan->tiempoPublicacion){
            $mensaje=9569;
            return $mensaje;
        }else{
            $combinacionEspecifica->cantidad=$request->cantidad;
            $combinacionEspecifica->save();
            return $combinacionEspecifica;
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
       
       $combinacion=Combinacion::findOrFail($id);
        $producto=$combinacion->producto;
        
        
            $combinacion->status='I';
            $combinacion->save();

            return  $combinacion;
        

       
       

    }
}
