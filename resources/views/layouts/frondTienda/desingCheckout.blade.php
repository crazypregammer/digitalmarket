<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">

   
    
    <title>Home | E-Shopper</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    
   
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


    <link href="{{asset('shop/css/prettyPhoto.css')}}" rel="stylesheet">
    <link href="{{asset('shop/css/price-range.css')}}" rel="stylesheet">
    <link href="{{asset('shop/css/animate.css')}}" rel="stylesheet">
	<link href="{{asset('shop/css/main.css')}}" rel="stylesheet">
	<link href="{{asset('shop/css/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('shop/easy-zoom/css/easyzoom.css')}}" rel="stylesheet">
    
    

    
   <!-- <link rel="stylesheet" href="https://unpkg.com/vue-form-wizard/dist/vue-form-wizard.min.css"> -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->       
    <link rel="shortcut icon" href="{{asset('shop/images/ico/favicon.ico')}}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('shop/images/ico/apple-touch-icon-144-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{asset('shop/images/ico/apple-touch-icon-114-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{asset('shop/images/ico/apple-touch-icon-72-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" href="{{asset('shop/images/ico/apple-touch-icon-57-precomposed.png')}}">

    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">

   

</head><!--/head-->


<body>
    @include('layouts.frondTienda.headerMin')
	@yield('contenido')
	@include('layouts.frondTienda.footer')

   <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="{{asset('shop/js/jquery.scrollUp.min.js')}}"></script>
	<script src="{{asset('shop/js/price-range.js')}}"></script>
    <script src="{{asset('shop/js/jquery.prettyPhoto.js')}}"></script>
    <script src="{{asset('shop/js/main.js')}}"></script>
    <script src="{{asset('shop/easy-zoom/js/easyzoom.js')}}"></script>
   <!-- <script src="https://unpkg.com/vue-form-wizard/dist/vue-form-wizard.js"></script>-->

    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    @yield('script')

</body>
</html>