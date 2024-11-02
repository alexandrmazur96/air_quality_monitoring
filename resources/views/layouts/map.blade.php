<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('favicon.png') }}" rel="shortcut icon" type="image/x-icon">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    @vite('resources/scss/app.scss')
</head>
<body>
<div id="app">
    <div class="grid grid-nogutter">
        <div class="col">
            @yield('content')
        </div>
    </div>
</div>
@vite('resources/js/app.js')
</body>
</html>
