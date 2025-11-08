<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Bootstrap Css -->
        <link href="{{ asset('/assets/') }}/auth-assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">
        <!-- Icons Css -->
        <link href="{{ asset('/assets/') }}/auth-assets/css/icons.min.css" rel="stylesheet" type="text/css">
        <!-- App Css-->
        <link href="{{ asset('/assets/') }}/auth-assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">

    </head>
    <body>
        {{ $slot }}

        <script src="{{ asset('/assets/') }}/auth-assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('/assets/') }}/auth-assets/libs/metismenujs/metismenujs.min.js"></script>
        <script src="{{ asset('/assets/') }}/auth-assets/libs/simplebar/simplebar.min.js"></script>
        <script src="{{ asset('/assets/') }}/auth-assets/libs/eva-icons/eva.min.js"></script>

        <script src="{{ asset('/assets/') }}/auth-assets/js/pages/pass-addon.init.js"></script>
    </body>
</html>
