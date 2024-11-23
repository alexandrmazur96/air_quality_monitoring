<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('favicon.png') }}" rel="shortcut icon" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=B612+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        .montserrat-mono-regular {
            font-family: "Montserrat", serif;
            font-weight: 400;
            font-style: normal;
        }

        .montserrat-mono-bold {
            font-family: "Montserrat", serif;
            font-weight: 700;
            font-style: normal;
        }

        .montserrat-mono-regular-italic {
            font-family: "Montserrat", serif;
            font-weight: 400;
            font-style: italic;
        }

        .montserrat-mono-bold-italic {
            font-family: "Montserrat", serif;
            font-weight: 700;
            font-style: italic;
        }

        .b612-mono-regular {
            font-family: "B612 Mono", serif;
            font-weight: 400;
            font-style: normal;
        }

        .b612-mono-bold {
            font-family: "B612 Mono", serif;
            font-weight: 700;
            font-style: normal;
        }

        .b612-mono-regular-italic {
            font-family: "B612 Mono", serif;
            font-weight: 400;
            font-style: italic;
        }

        .b612-mono-bold-italic {
            font-family: "B612 Mono", serif;
            font-weight: 700;
            font-style: italic;
        }
    </style>

    @vite('resources/scss/app.scss')
</head>
<body class="text-slate-900 montserrat-mono-regular">
<div id="app">
    <div class="grid">
        <div class="col-12 col-offset-0 lg:col-offset-2 lg:col-8">
            @yield('content')
        </div>
    </div>
</div>
@vite('resources/js/app.js')
</body>
</html>
