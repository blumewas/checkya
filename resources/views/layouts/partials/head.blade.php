<head>
  <!--
    ======================================================================================
    == Lovingly brought to you by mindtwo GmbH (https://www.mindtwo.de/) =================

     _____ ______    ___   ________    ________   _________   ___       __    ________
    |\   _ \  _   \ |\  \ |\   ___  \ |\   ___ \ |\___   ___\|\  \     |\  \ |\   __  \
    \ \  \\\__\ \  \\ \  \\ \  \\ \  \\ \  \_|\ \\|___ \  \_|\ \  \    \ \  \\ \  \|\  \
     \ \  \\|__| \  \\ \  \\ \  \\ \  \\ \  \ \\ \    \ \  \  \ \  \  __\ \  \\ \  \\\  \
      \ \  \    \ \  \\ \  \\ \  \\ \  \\ \  \_\\ \    \ \  \  \ \  \|\__\_\  \\ \  \\\  \
       \ \__\    \ \__\\ \__\\ \__\\ \__\\ \_______\    \ \__\  \ \____________\\ \_______\
        \|__|     \|__| \|__| \|__| \|__| \|_______|     \|__|   \|____________| \|_______|

    =======================================================================================
    == Hi awesome developer! ==============================================================
    == You want to join our nerd-cave and deploy state of the art web applications? =======
    == Then take a look at our career page at https://www.mindtwo.de/karriere/ ============
    =======================================================================================
    -->

  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="keywords" content="@yield('html.meta.keywords', '')" />
  <meta name="description" content="@yield('html.meta.description', '')" />
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}" />
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon-32x32.png') }}" />
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon-16x16.png') }}" />
  <link rel="manifest" href="/manifest.json" crossorigin="use-credentials" />
  <link rel="mask-icon" href="{{ asset('/safari-pinned-tab.svg') }}" color="#DE0639" />
  <meta name="msapplication-TileColor" content="#DE0639" />
  <meta name="theme-color" content="#DE0639" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url()->full() }}" />
  <meta property="og:title" content="@yield('html.title', config('app.name'))" />
  <meta property="og:image" content="@yield('og.image', asset('/favicon-96x96.png'))" />
  <meta property="og:description" content="@yield('html.meta.description', '')" />
  <meta property="og:site_name" content="@yield('og.name')" />
  <meta property="og:locale" content="de_DE" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @yield('html.meta.custom')

  <title>@yield('html.title', config('app.name'))</title>

  @stack('head-scripts')
  @stack('css')
</head>
