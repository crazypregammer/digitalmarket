@extends('layouts.frondTienda.design')
@section('contenido')
<div id="login">
   

    <section id="form" style="margin-top: 20px;"><!--form-->
        
      <div class="container">
              <div class="p-2">
                  @include('flash::message')
              </div>
        <div class="row">
          <div class="col-sm-4 col-sm-offset-1">
            <div class="login-form"><!--login form-->
              <h2>Iniciar sesión</h2>
              
              <form action="{{ url('/iniciar-sesion') }}" method="post">
                  @csrf
                
                  <div class=" mb-3">
                    <input id="email" type="text" class=" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required   autofocus placeholder="Correo Electrónico">
                    
                  </div>
                  @error('email')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                  
                  <div class="mb-3">
                    <input id="password" type="password" class=" @error('password') is-invalid @enderror" name="password" required  placeholder="Contraseña">
                    
                  </div>
                  @error('password')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                  <!--
                  <div class="row">
                    
                    -->
                    <div class="row">
                      <div class="col-sm-12">
                        
                        @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('¿Ha olvidado su contraseña?') }}
                        </a>
                        @endif
                      
                      </div>
                      <div class="col-sm-12">
                        <button type="submit" class="btn-block">Ingresar</button>
                      </div>
                    </div>
                    <!-- /.col -->
              </form>
            </div>
                      
          </div><!--/login form-->
        </div>
        
      </div>
	  </section><!--/form-->

</div>
@endsection