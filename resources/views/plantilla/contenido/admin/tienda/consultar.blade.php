@extends('layouts.appAdmin')
@section('contenido')
    <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
   
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h5>Cantidad de tiendas: {{count($tienda)}}</h5>
          </div>

          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
              <li class="breadcrumb-item active">Consultar</li>
            </ol>
        </div>

        </div>
      </div><!-- /.container-fluid -->
    </section>
      <!-- Main content -->
      <section class="content">


        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="p-2">
                  @include('flash::message')
               </div>

                <div class="card-header">
                  <h3 class="card-title">Consultar Tiendas</h3>
  
                  
                </div>
                
                <!-- /.card-header -->
                <div class="card-body ">
                  <div class="table-responsive p-0">
                    <table id="table_id" class="display">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Código</th>
                          <th>Nombre</th>
                          <th>Correo electrónico</th>
                          <th>Plan afiliación</th>
                          <th>Fecha de registro</th>
                          <th>Estado</th>
                          <th>Acción</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach ($tienda as $item)
                      <tr >
                              <td class="mailbox-star">{{$item->id}}</td>
                              <td class="mailbox-star">{{$item->codigo}}</td>
                              <td class="mailbox-star">{{$item->nombreTienda}}</td>
                              <td class="mailbox-star">{{$item->correo}}</td>
                      <td class="mailbox-star">{{$item->planAfiliacion->nombre}}</td> 
                          <td class="mailbox-star">{{$item->created_at}}</td>
                      <td class="mailbox-star">
                        @if($item->estatus=='A')
                        <span class="badge badge-success">Activo</span>
                        @else
                        <span class="badge badge-danger">Inactivo</span>
                        @endif  
                      </td>
                              <td class="mailbox-star">
                                  <div class="btn-group">
                                   <a class="btn btn-default btn-sm" href="{{route('tienda.show',$item)}}"><span class="fas fa-eye"></span></a>
                                  <a href="{{route('tienda.edit',$item)}}" class="btn btn-default btn-sm mx-1">  <span class="fas fa-edit" aria-hidden ="true" ></span></a>
  
                                      
                                      
                                      <form action="{{route('tienda.destroy',$item)}}" method="POST"  >
                                          @method('DELETE')
                                          @csrf
                                          <button class="btn btn-default btn-sm d-inline float-left" onclick="return confirm('¿Esta seguro que desea eliminar este tienda?')"><span class="fas fa-trash-alt" aria-hidden ="true" ></span></button>
                                        </form>
                                      
                                      
                                    
                                  </div>
                              </td>
                          </tr>
                              
                          @endforeach
                      
                       
                      </tbody>
                    </table>
                  </div>
                  
                  <div class="box-footer p-3 float-right">
                  <a href="{{route('tienda.create')}}"  class="btn  btn-info ">Registrar tienda</a>
            
                  </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
          </div>
          <!-- /.row -->


      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection