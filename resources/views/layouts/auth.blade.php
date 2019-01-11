<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Facturalo Peru</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    </head>
    <body>
        <div class="layout-login" id="app">
            @yield('content')
        </div>
        <script>
            window.i18n = {!! json_encode(__('app')) !!};
        </script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
