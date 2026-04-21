<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Sistemas de Ventas">
  <meta name="author" content="Jorge Francelli Saldaña Reyes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('public/img/icono/icoriza.ico') }}">
  <title>Induamerica - Sistema de Ventas</title>


  <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/perfect-scrollbar/css/perfect-scrollbar.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/material-design-icons/css/material-design-iconic-font.min.css') }} " />
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/font-awesome.min.css') }} " />
  <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/scroll/css/scroll.css') }} " />

  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  @yield('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/jquery-confirm.min.css') }} "/>
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/style.css?v='.$version) }} " />
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/oryza.css?v='.$version) }} " />
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/premium.css?v='.$version) }} " />

</head>

<body>


  <div class="be-wrapper be-color-header"  id="app">

    @include('success.ajax-alert')
    @include('success.bienhecho', ['bien' => Session::get('bienhecho')])
    @include('error.erroresurl', ['error' => Session::get('errorurl')])
    @include('error.erroresbd', ['error' => Session::get('errorbd')])

    @include('menu.nav-top')
    @include('menu.nav-left')


    @include('success.xml', ['xml' => Session::get('xmlmsj')])

    @yield('section')
    <input type='hidden' id='carpeta' value="{{$capeta}}" />
    <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
  </div>
 
  <script src="{{ asset('public/lib/jquery/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/main.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/scroll/js/jquery.mousewheel.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/scroll/js/jquery-scrollpanel-0.7.0.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/scroll/js/scroll.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/general/general.js?v='.$version) }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/general/jquery-confirm.min.js?v='.$version) }}" type="text/javascript"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $('.be-toggle-left-sidebar').on('click', function(e){
        e.preventDefault();
        var $wrapper = $('.be-wrapper');
        $wrapper.toggleClass('sidebar-collapsed');
        
        // Fix for charts resizing
        setTimeout(function(){
          $(window).trigger('resize');
        }, 310);

        // Change icon based on state
        var $icon = $(this).find('i, span.icon');
        if($wrapper.hasClass('sidebar-collapsed')){
          $icon.removeClass('mdi-menu').addClass('mdi-chevron-right');
        } else {
          $icon.removeClass('mdi-chevron-right').addClass('mdi-menu');
        }
      });

      // Manual initialization of tooltips to ensure they look "Premium"
      function initPremiumTooltips() {
        $('.be-left-sidebar [data-toggle="tooltip"]').tooltip({
          container: 'body',
          placement: 'right',
          trigger: 'hover',
          template: '<div class="tooltip premium-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
      }

      initPremiumTooltips();
    });
  </script>

  @yield('script')
 
    <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
<!--     <script type="module">
      import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

      createChat({
        webhookUrl: 'http://localhost:5678/webhook/da259712-a41d-4207-ab02-94acd6b3a288/chat'
      });

      // Esperar a que el chat se cargue y modificar textos
      setTimeout(() => {
        document.querySelector('.chat-heading h1').innerText = "¡Hola! 👋";
        document.querySelector('.chat-header p').innerText = "Inicia una conversación. Estamos aquí para ayudarte 24/7.";
        
        const botMessages = document.querySelectorAll('.chat-message-from-bot .chat-message-markdown p');
        if (botMessages.length > 0) {
          botMessages[0].innerText = "¡Hola! 👋";
          if (botMessages.length > 1) {
            botMessages[1].innerText = "Me llamo Chalancito. ¿En qué puedo ayudarte hoy?";
          }
        }

        document.querySelector('.chat-input textarea').setAttribute('placeholder', "Escribe tu pregunta...");
      }, 3000); // Asegura que el chat esté completamente cargado
    </script> -->


 
  <script type="text/javascript">
    // Parche global preventivo para corregir el error de dimensiones en gráficas Flot
    // Esto evita que el sistema falle si las gráficas intentan dibujarse antes de que 
    // el contenedor tenga dimensiones reales (ej. durante transiciones del menú).
    $(window).on('load', function() {
      if (typeof $.plot === 'function') {
        var _originalPlot = $.plot;
        $.plot = function(placeholder, data, options) {
          var $el = $(placeholder);
          if ($el.length && ($el.width() <= 0 || $el.height() <= 0)) {
            setTimeout(function() { $.plot(placeholder, data, options); }, 300);
            return { shutdown: function() {}, setData: function() {}, draw: function() {}, getPlaceholder: function() { return $el; } };
          }
          return _originalPlot.apply(this, arguments);
        };
      }
    });
  </script>
</body>

</html>