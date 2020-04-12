@extends('layouts.appAdmin')

@section('estilos')
    
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('adminlte/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

<link rel="stylesheet" href="{{asset('adminlte/plugins/ekko-lightbox/ekko-lightbox.css')}}">

@endsection

@section('scripts')
    
<script src="/adminlte/ckeditor/ckeditor.js"></script>

<!-- select2---->
<script src="{{asset('adminlte/plugins/select2/js/select2.full.min.js')}}"></script>

<script src="{{asset('adminlte/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>

<script>

    window.data={
  
      editar:'si',
      datos:{
        "nombre":"{{$producto->nombre}}",
        "precioAnterior":"{{$producto->precioAnterior}}",
        "precioActual":"{{$producto->precioActual}}",
        "porcentajeDescuento":"{{$producto->porcentajeDescuento}}",
        "selectedCategoria":"{{$producto->subCategoria->categoria->id}}"
          }
  
    }

  
  $(function () {
      //Initialize Select2 Elements
      $('#categoriaa_id').select2()
  
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    

 //Uso de lightbox
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        alwaysShowClose: true
      });
    });

})

  
  </script>

@endsection

@section('contenido')

<div id="producto">
<form action="{{route('producto.update',$producto)}}" method="post" enctype="multipart/form-data">
  @method('PUT')
  @csrf

<div class="content-wrapper">
  <!-- Content Header (Page header) -->

  <div class="p-2">
    @include('flash::message')
 </div>
 
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
      <h1>Producto</h1>
        </div>

        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{route('Plan.index')}}">Consultar</a></li>
            <li class="breadcrumb-item active">Modificar producto</li>
          </ol>

      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->



          <div class="col-md-12">


            <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Datos generados automáticamente</h3>
      
                 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
      
                   <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
      
                        <label>Visitas</label>
                        <input nombre=""  class="form-control" type="number" id="visitas" name="visitas"
                      readonly value="{{$producto->visitas}}"
                        >
                        
                       
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                      <div class="form-group">
      
                        <label>Ventas</label>
                        <input  class="form-control" type="number" id="ventas" name="ventas" 
                        readonly value="{{$producto->ventas}}"
                        >
                      </div>
                      <!-- /.form-group -->
          
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
      
      
      
      
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  
                </div>
              </div>
              <!-- /.card -->
      
      
      
      
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Datos del producto</h3>
      
                
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
      
                        <label>Nombre</label>
                        <input v-model="nombre" class="form-control" type="text" id="nombre" name="nombre"
                        @blur="getProducto"
                        @focus="divAparecer=true"
                        >
                        {!!$errors->first('nombre','<small>:message</small><br>')!!}
                        <label>Slug</label>
                        <input class="form-control" type="text" id="slug" name="slug" 
                        readonly
                        v-model="generarSlug"
                        >
                        <div v-if="divAparecer " v-bind:class="divClaseSlug">
                            @{{divMensajeSlug}}
                        </div>
                        <br v-if="divAparecer">
                        {!!$errors->first('slug','<small>:message</small><br>')!!}
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                      <div class="form-group">
                        
                        <div class="row">
                          
                          <div class="col-md-6">
                          
                          <label>Categoria</label>
                          
                          <select v-model="selectedCategoria"  data-old="{{old('categoria_id')}}" @change="cargarSubCategorias" name="categoria_id" id="categoria_id" class="form-control" style="width: 100%;">
  
                              <option value=""  >Seleccione una categoria</option>
                              
                              @foreach($categoria as $categorias)
                              
                               @if ($categorias->id==$producto->subCategoria->categoria->id)
                                  <option value="{{ $categorias->id }}" selected="selected">{{ $categorias->nombre }}</option>
                               @else
                                  <option value="{{ $categorias->id }}">{{ $categorias->nombre }}</option>                         
                              @endif
                              @endforeach
          
          
                            </select>
                           
                         
                        </div>
                            <div class="col-md-6">
                             
                             
                          <label>Sub categoria</label>
                          
                            <select v-model="selectedSubCategoria" name="subCategoria_id" id="subCategoria_id" data-old="{{old('subCategoria_id')}}" class="form-control select2" style="width: 100%;">
  
                              <option value="" selected="selected" >Seleccione una categoria</option>
                              
                              <option v-for="(subCategoria,index) in obtenerSubCategorias"  v-bind:value="index" >@{{subCategoria}}</option>
                            </select>
                         
                          </div>

                        </div>
                        
                        <label>Cantidad</label>
                    <input class="form-control" type="number" id="cantidad" name="cantidad" value="{{$producto->cantidad}}" >
                      </div>
                      <!-- /.form-group -->
          
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
      
      
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                 
              </div>
            </div>
      
              <!-- /.card -->
      
      
      
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Sección de Precios</h3>
      
                  
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
      
      
      
                    <div class="col-md-3">
                      <div class="form-group">
      
                        <label>Precio anterior</label>
                        
      
      
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input 
                        v-model="precioAnterior"
                        class="form-control" type="number" id="precioAnterior" name="precioAnterior" min="0" value="0" step=".01">                 
                      </div>
                       
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
      
      
      
                    <div class="col-md-3">
                      <div class="form-group">
      
                        <label>Precio actual</label>
                         <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input 
                        v-model="precioActual"
                        class="form-control" type="number" id="precioActual" name="precioActual" min="0" value="0" step=".01">                 
                      </div>
      
                      <br>
                      <span id="descuento">
                          @{{generarDescuento}}
                      </span>
                      </div>
                      <!-- /.form-group -->
          
                    </div>
                    <!-- /.col -->
      
      
      
      
                    <div class="col-md-6">
                      <div class="form-group">
      
                        <label>Porcentaje de descuento</label>
                         <div class="input-group">                  
                        <input 
                        v-model="porcentajeDescuento"
                        class="form-control" type="number" id="porcentajeDescuento" name="porcentajeDescuento" step="any" min="0" max="100" value="0" >    <div class="input-group-prepend">
                          <span class="input-group-text">%</span>
                        </div>  
      
                      </div>
      
                      <br>
                      <div class="progress">
                          <div id="barraprogreso" class="progress-bar" role="progressbar" 

                          v-bind:style="{width:porcentajeDescuento+'%'}"

                          aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">@{{porcentajeDescuento}}%</div>
                      </div>
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
      
      
                  </div>
                  <!-- /.row -->
      
      
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  
                </div>
              </div>
      
      
      
      
      
      
      
      
         <div class="row">
                <div class="col-md-6">
      
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title">Descripciones del producto</h3>
                    </div>
                    <div class="card-body">
                      <!-- Date dd/mm/yyyy -->
                      <div class="form-group">
                        <label>Descripción corta:</label>
      
                        <textarea  class="form-control ckeditor" name="descripcionCorta" id="descripcionCorta" rows="3">
                            {!!$producto->descripcionCorta!!}
                        </textarea>
                      
                      </div>
                      <!-- /.form group -->
      
                     <div class="form-group">
                        <label>Descripción larga:</label>
      
                        <textarea class="form-control ckeditor" name="descripcionLarga" id="descripcionLarga" rows="5">
                            {!!$producto->descripcionLarga!!}
                        </textarea>
                      
                      </div>                
      
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
      
             </div>
              <!-- /.col-md-6 -->
      
      
      
      
                <div class="col-md-6">
      
                  <div class="card card-info">
                    <div class="card-header">
                      <h3 class="card-title">Especificaciones y otros datos</h3>
                    </div>
                    <div class="card-body">
                      <!-- Date dd/mm/yyyy -->
                      <div class="form-group">
                        <label>Especificaciones:</label>
      
                        <textarea class="form-control ckeditor" name="especificaciones" id="especificaciones" rows="3">
                            {!!$producto->especificaciones!!}
                        </textarea>
                      
                      </div>
                      <!-- /.form group -->
      
                     <div class="form-group">
                        <label>Datos de interes:</label>
      
                        <textarea class="form-control ckeditor" name="datosInteres" id="datosInteres" rows="5">
                            {!!$producto->datosInteres!!}
                        </textarea>
                      
                      </div>                
      
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
      
             </div>
              <!-- /.col-md-6 -->
      
      
      
            </div>
            <!-- /.row -->
      
      
      
      
               <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">Imagenes</h3>
      
                 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
      
                  <div class="form-group">
                      
                     <label for="imagenes">Subir varias imagenes</label> 
                     
                     <input type="file" class="form-control-file" id="imagenes[]" name="imagenes[]"  multiple 
                     accept="image/*" >
                     
                     <div class="description">
                       Un número ilimitado de archivos pueden ser argado en este campo.
                       <br>
                       Limite de 2048MB por imagen.
                       <br>
                       Tipos permitidos: jpeg,png,jpg,gif,svg.
                       <br>
                     
                      </div>
                      {!!$errors->first('imagenes','<small>:message</small><br>')!!}
                  </div>
      
      
                </div>
      
      
                <!-- /.card-body -->
                <div class="card-footer">
                  
                </div>
              </div>
              <!-- /.card -->
      
              

              <div class="card card-primary">
                <div class="card-header">
                  <div class="card-title">
                    Galería de imagenes
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">

                    @foreach ($producto->imagen as $imagen)

                  <div class="col-sm-2" id="idimagen-{{$imagen->id}}">
                      <a href=" {{$imagen->url}}" data-toggle="lightbox" data-title="id:{{$imagen->id}}" data-gallery="gallery">
                          <img  src="{{$imagen->url}}" class="img-fluid mb-2"/>
                        </a>
                        <br>
                        <a href="{{$imagen->url}}"
                          v-on:click.prevent="eliminarImagen({{$imagen}})"
                          >
                          <i class="fas fa-trash-alt" style="color:red;  "></i>{{$imagen->id}}
                        </a>
                      </div>
                      
                    @endforeach

                  </div>
                </div>
              </div>





            <div class="card card-danger">
                <div class="card-header">
                  <h3 class="card-title">Administración</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
      
             <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
      
                        <label>Estado</label>
                        <input  class="form-control" type="text" id="estado" name="estado" value="Nuevo">
      
                       
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                          <!-- checkbox -->
                          <div class="form-group clearfix">
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input" id="status" name="status"
                              
                              @if($producto->status='si')
                                checked
                              @endif

                              >
                              <label class="custom-control-label" for="status">Activo</label>
                           </div>
      
                          </div>
      
                          <div class="form-group">
                          <div class="custom-control custom-switch">
                            <input type="checkbox"  class="custom-control-input" id="sliderPrincipal" name="sliderPrincipal">
                            <label class="custom-control-label" for="sliderPrincipal">Aparece en el Slider principal</label>
                          </div>
                        </div>
      
                        </div>
      
                      
      
             </div>
                  <!-- /.row -->
      
      
      
      
             <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
      
                         <a class="btn btn-danger" href="">Cancelar</a>
                         <input  
                         :disabled="deshabilitarBoton==1"        
                        type="submit" value="Actualizar productos " class="btn btn-primary">
                       
                      </div>
                      <!-- /.form-group -->
                      
                    </div>
                    <!-- /.col -->
      
      
                 
                      
      
             </div>
                  <!-- /.row -->
      
      
      
      
                </div>
      
      
         
                <!-- /.card-body -->
                <div class="card-footer">
                  
                </div>
              </div>
              <!-- /.card -->

            </div>


          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>





        
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</form>
</div>
@endsection