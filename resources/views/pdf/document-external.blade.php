<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Reporte Documento Externo</title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    #logo1 {
        float: left;
        margin-left: 2%;
        margin-right: 2%;
    }

    #logo2 {
        float: right;
        margin-left: 2%;
        margin-right: 2%;
    }

    #imagen {
        width: 60px;
    }

    #encabezado {
        text-align: center;
        font-size: 16px;
    }

    #date {
        text-align: right;
        margin-top: -2%;
        margin-right: 16%;
    }

    #affair {
        margin-left: 45%;
        top: -1%;
        position: relative;
        margin-bottom: -4%;
    }

    #content_affair {
        margin-left: 56%;
        top: -1%;
        position: relative;
        margin-bottom: -4%;
    }

    #identification {
        margin-left: 6%;
        margin-right: 6%;
        text-align: justify;
        margin-top: 4%;
    }

    #section_1 {
        text-align: left;
        top: 3%;
        margin-left: 6%;
        margin-right: 2%;
        font-size: 14px;
        position: relative;
    }

    #section_2 {
        text-align: justify;
        margin-left: 6%;
        margin-right: 6%;
        margin-top: 3%;
    }

    #section_3 {
        margin-top: 5%;
        text-align: center;
    }

    #section_4 {
        width: 100px;
        height: 30px;
        border: 1px solid #555;
        font-size: 11px;
        top: 6.5%;
        margin-top: 5%;
        margin-left: 6%;
        position: relative;
    }

    #section_5 {
        margin-left: 20%;
    }

    @page {
        margin: 120px 50px 60px;
    }

    #header {
        position: fixed;
        left: 0px;
        top: -100px;
        right: 0px;
        height: 67px;
        text-align: center;
    }

    #footer {
        position: fixed;
        left: 0px;
        bottom: -100px;
        right: 0px;
        height: 100px;
        text-align: center;
        border-top:solid 2px black;
        font-size: 12px;
    }
</style>
<body>
<div id="header">
    <div id="logo1">
        <img src="{{ asset('img/logo2.jpg') }}" alt="munipillco" id="imagen">
    </div>
    <div id="logo2">
        <img src="{{ asset('img/logo3.jpg') }}" alt="munipillco" id="imagen">
    </div>

    <p id="encabezado">
        <b>{{ $documents->entityDependency }}</b><br>
        "Año de la universalización de la salud"
    </p>
</div>

<div id="footer">
    Av. Juan Velazco Alvarado Nro. 1650 - Municipalidad Distrital de Pillcomarca - Cel: 964118454
</div>

<div id="affair">
    <b>SOLICITA:</b>
</div>

<div id="content_affair">
    {{mb_strtoupper($documents->affair)}}
</div>


<div id="section_1">
    <P><b>SEÑOR ALCALDE DE LA {{ $documents->entityDependency }}</b></P>
</div>

<div id="identification">
    Yo {{$documents->fullNameT}}, identificado con DNI. N° {{$documents->dni}}, con domicilio en: {{$documents->direction}}, N° de Celular: {{$documents->phone}}
    <br>
    <p>Ante usted. Con el debido respeto me presento y expongo lo siguiente:</p>
</div>

<div id="section_2">
    <b>FUNDAMENTACIÓN:</b>
    <p>{!! $documents->description !!}</p>
</div>

<div id="section_5">
    <p><b>POR LO EXPUESTO:</b></p>
    Ante usted Señor Alcalde, pido acceder a mi petición por ser de justicia.
</div>
<br>
<p id="date">Cayhuayna, {{$date}}</p>
<br><br>

<div id="section_3">
    <p>{{$documents->fullNameT}}<br>
        DNI: {{$documents->dni}}
    </p>
</div>

<div id="section_4">
    Expediente:{{$documents->records}}<br>Registro: {{$documents->registry}}
</div>

</body>
</html>

