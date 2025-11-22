@extends('layouts.master')

@section('html.title')
  @yield('title')
  - {{ config('app.name') }}
@endsection

@section('html.meta.custom')
  <link rel="canonical" href="{{ trim(url()->current(), '/') }}" />
  @viteReactRefresh
@endsection

@push('css')
  @vite(['resources/css/main.css'])
@endpush

@push('scripts')
  @vite(['resources/js/apps/sample-app/sample-app.tsx'])
@endpush

@section('body')
  @include('layouts.partials.navigation')
  <main class="m2-main pt-17 lg:pt-0 w-full">
    <div class="flex flex-col min-h-screen">
      <div>@yield('main')</div>
      <div class="mt-auto">
        @include('layouts.partials.footer')
      </div>
    </div>
  </main>
@endsection
