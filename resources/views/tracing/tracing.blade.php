<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de Trámite Documentario">
    <meta name="author" content="Systems.com">
    <meta name="keyword" content="Sistema de trámite documentario">
    <title>Sistema de Trámite Documentario</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('css/templates.css')}}" rel="stylesheet">
</head>
<body>
<div id="app">
    <view-tracing :ruta="ruta"></view-tracing>
</div>
<script src="{{asset('js/app.js')}}"></script>
<script src="{{asset('js/templates.js')}}"></script>
</body>
</html>
