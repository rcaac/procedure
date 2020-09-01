<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Reporte Documento Interno</title>
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
        margin-right: 2%;
    }

    #affair {
        margin-left: 19%;
        top: -3.5%;
        position: relative;
        margin-bottom: -4%;
    }

    #reference {
        margin-left: 19%;
        top: -4%;
        position: relative;
    }

    #section_1 {
        text-align: left;
        top: -2%;
        margin-left: 6%;
        margin-right: 2%;
        font-size: 14px;
        position: relative;
    }

    #section_2 {
        text-align: justify;
        margin-left: 6%;
        margin-right: 6%;
        margin-top: -4%;
    }

    #section_3 {
        margin-left: 24%;
    }

    #section_4 {
        margin-left: 83%;
        width: 100px;
        height: 30px;
        border: 1px solid #555;
        font-size: 11px;
        top: 6.5%;
        position: relative;
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

    td {
        border-color: #959594;
        border-style: solid;
        border-width: 1px;
    }

    table {
        border-collapse: collapse;
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
            <b>MUNICIPALIDAD DISTRITAL DE PILLCOMARCA</b><br>
            "Año de la universalización de la salud"
        </p>
    </div>

    <div id="footer">
        Av. Juan Velazco Alvarado Nro. 1650 - Municipalidad Distrital de Pillcomarca - Cel: 964118454
    </div>

    <p id="date">Cayhuayna, {{$date}}</p>

    <div id="section_4">
        Expediente:{{$documents->records}}<br>Registro: {{$documents->registry}}
    </div>

    <div id="section_1">
        <p><b>{{$documents->code}}</b></p>

        <p>
            <b>SEÑOR(A)</b>
            @foreach($destinations as $key => $destination)
                @if($key == 0)
                    {{str_repeat ("&nbsp;", 12)}}:{{strtoupper($destination->fullName)}}<br>
                    {{str_repeat ("&nbsp;", 30)}} <small style="font-size: 10px;">{{$destination->dependency}}</small><br>
                @else
                    {{str_repeat ("&nbsp;", 30)}} {{strtoupper($destination->fullName)}}<br>
                    {{str_repeat ("&nbsp;", 30)}} <small style="font-size: 10px;">{{$destination->dependency}}</small><br>
                @endif()
            @endforeach
        </p>

        <p><b>ASUNTO</b></p>

        <div id="affair">
            :{{mb_strtoupper($documents->affair)}}
        </div>

        @if(count($codes) > 0)
            <p><b>REFERENCIA</b></p>
            <div id="reference">
                @foreach($codes as $key => $code)
                    @if($key == 0)
                        :<small style="font-size: 10px;">{{ $code->code }}</small><br>
                    @else
                        <small style="font-size: 10px;">{{ $code->code }}</small><br>
                    @endif()
                @endforeach
            </div>
        @endif
    </div>

    <div id="section_2">
        <p>{!! $documents->description !!}</p>
    </div>
    <br><br>

    <div id="section_3">
        <p>Atentamente,</p>
    </div>
    <br><br>

    <div id="section_3">
        <p>{{$documents->fullNameT}}<br>
            <small style="font-size: 9px;">{{$documents->Dependency}}</small><br>
            <small style="font-size: 9px;">{{$documents->entityDependency}}</small>
        </p>
    </div>

</body>
</html>
