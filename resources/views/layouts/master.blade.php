<!DOCTYPE html>
<html lang="de" class="@yield('html-class')">
  @include('layouts.partials.head')

  <body class="@yield('body-class')">
    @include('layouts.partials.javascript')

    @yield('body')

    @stack('scripts')
  </body>
</html>
