<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistemas de Ventas Induamerica">
    <meta name="author" content="Antigravity AI">
    <link rel="icon" href="{{ asset('public/img/icono/icoriza.ico') }}">
    
    <title>Induamerica - Inicio de Sesión</title>
    
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/material-design-icons/css/material-design-iconic-font.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/perfect-scrollbar/css/perfect-scrollbar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('public/css/premium.css?v='.$version) }}" type="text/css"/>

  </head>
  <body class="be-splash-screen premium-login">

    <div class="premium-login-card">
      <div class="logo-container">
        <img src="{{ asset('public/img/oryza.png') }}" alt="Induamerica Logo" class="logo-img">
      </div>
      
      <h2>INDUAMERICA</h2>
      <p class="subtitle">Sistema de Ventas - Gestión Inteligente</p>

      <div class="panel-body">
        <form method="POST" action="{{ url('login') }}" class="premium-form">
          {{ csrf_field() }}

          <div class="form-group">
            <i class="mdi mdi-account input-icon"></i>
            <input id="name" name='name' type="text" required="" value="{{ old('name') }}"  placeholder="Usuario" autocomplete="off" class="form-control" data-aw="1"/>

            @include('error.erroresvalidate', [ 'id' => $errors->has('name')  , 
                                                'error' => $errors->first('name', ':message') , 
                                                'data' => '1'])

            @include('error.erroresbd', [ 'id' => Session::get('errorbd')  , 
                                          'error' => Session::get('errorbd') , 
                                          'data' => '1'])
          </div>

          <div class="form-group">
            <i class="mdi mdi-key input-icon"></i>
            <input id="password" name='password' type="password" required=""   placeholder="Contraseña" class="form-control" data-aw="2"/>
            @include('error.erroresvalidate', ['id' => $errors->has('password')  , 'error' => $errors->first('password', ':message'), 'data' => '2'])
            @include('error.erroresbd', ['id' => Session::get('errorbd')  , 'error' => Session::get('errorbd'), 'data' => '2'])
          </div>

          <div class="form-group login-submit" style="margin-top: 30px;">
            <button type="submit" class="premium-btn">Entrar al Sistema</button>
          </div>

          <input type='hidden' id='carpeta' value="{{$capeta}}"/>
          <input type="text" id="token"  class="ocultar" name="_token"  value="{{ csrf_token() }}">

        </form>
      </div>
    </div>

    <script src="{{ asset('public/lib/jquery/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/main.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        App.init();
        $('form').parsley();
      });
    </script>

    <script src="{{ asset('public/js/user/user.js') }}" type="text/javascript"></script>

  </body>
</html>