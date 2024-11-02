<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('favicon.png') }}" rel="shortcut icon" type="image/x-icon">

    @vite('resources/scss/app.scss')
</head>
<body>

@yield('content')

<a href="https://www.flaticon.com/free-icons/clean-air" title="clean air icons">Clean air icons created by Culmbio - Flaticon</a>
@vite('resources/js/app.js')
</body>
</html>
