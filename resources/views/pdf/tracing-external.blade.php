<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Reporte Seguimiento Externo</title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 8px;
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

    #detail {
        font-size: 12px;
    }

    #date {
        text-align: right;
        margin-top: -2%;
        margin-right: 0%;
        font-size: 12px;
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

    thead {
        background-color: #2980B9;
        color: #fff;

    }

    tbody > tr:last-child > td {
        border-bottom-width: 0;
    }

    td {
        background-color: #f2f2f2;
    }

    tr:nth-child(2n-1) td {
        background-color: #fff;
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
            <b>{{ $documents->entityDependency }}</b><br>
            "Año de la universalización de la salud"
        </p>
    </div>

    <div id="footer">
        Av. Juan Velazco Alvarado Nro. 1650 - Municipalidad Distrital de Pillcomarca - Cel: 964118454
    </div>

    <p id="date">Cayhuayna, {{$date}}</p>

    <div id="detail">
        <div>El documento con expediente <b>{{$details->records}}</b></div>
        @if($details->state == 1)
        <div>Se encuentra <b>Pendiente</b></div>
        @elseif($details->state == 2)
        <div>Se encuentra en estado <b>Para Tramitar</b></div>
        @elseif($details->state == 3)
        <div>Se encuentra en estado <b>Derivado</b></div>
        @elseif($details->state == 4)
        <div>Se encuentra en estado <b>Archivado</b></div>
        @elseif($details->state == 6)
        <div>Se encuentra en estado <b>Finalizado</b></div>
        @endif

        @if($details->state == 6)
            <div>En la oficina de <b>{{$details->Dependency}}</b></div>
        @else
            <div>En la oficina de <b>{{$details->Destination}}</b></div>
        @endif
    </div>
    <br><br>
    <table>
        <thead>
        <tr>
            <th><small><b>EMISOR DEL DOCUMENTO</b></small></th>
            <th><small><b>RECEPTOR DEL DOCUMENTO</b></small></th>
            <th><small><b>ESTADO</b></small></th>
            <th><small><b>TIPO DE DOCUMENTO</b></small></th>
            <th><small><b>ASUNTO</b></small></th>
            <th><small><b>FOLIOS</b></small></th>
            <th><small><b>OBSERVACIONES</b></small></th>
            <th><small><b>FECHA DE ENVÍO</b></small></th>
            <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
            <th><small><b>FECHA DE TRÁMITE</b></small></th>
        </tr>
        </thead>
        <tbody>
        @foreach($tracings as $tracing)
        <tr>
            <td>
                @if($tracing->Dependency == 'USUARIO EXTERNO')
                    <div>
                        <small><b>Usuario:</b>{{ $tracing->fullNameT }}<br></small>
                        <small><b>Dni:</b>{{ $tracing->dniT }}</small>
                    </div>
                @else
                    <div>
                        <small>{{ $tracing->Dependency }}<br></small>
                        <small><b>Entidad:</b> {{ $tracing->entityDependency }}</small>
                    </div>
                @endif
            </td>
            <td>
                @if($tracing->Dependency == 'USUARIO EXTERNO')
                    <div>
                        <small><b>Usuario:</b> {{ $tracing->fullNameR }}<br></small>
                        <small><b>Dni:</b> {{ $tracing->dniR }}</small>
                    </div>
                @else
                    <div>
                        <small>{{ $tracing->Destination }}<br></small>
                        <small><b>Entidad:</b> {{ $tracing->entityDestination }}</small>
                    </div>
                @endif
            </td>
            @if($tracing->state ==1)
                <td><span  class="badge badge-pill badge-success">Pendiente</span></td>
            @elseif($tracing->state ==2)
                <td><span  class="badge badge-pill badge-danger">Para Tramitar</span></td>
            @elseif($tracing->state ==3)
            <td><span  class="badge badge-pill badge-warning">Derivado</span></td>
            @elseif($tracing->state ==4)
            <td><span  class="badge badge-pill badge-primary">Archivado</span></td>
            @elseif($tracing->state ==6)
            <td><span  class="badge badge-pill badge-darck">Finalizado</span></td>
            @endif
            <td><small>{{ $tracing->code }}</small></td>
            <td>{{ $tracing->affair }}</td>
            <td>{{ $tracing->folio }}</td>
            <td><small>{{ $tracing->observations }}</small></td>
            <td>{{ $tracing->shipping_date }}</td>
            <td>{{ $tracing->reception_date }}</td>
            <td>{{ $tracing->procedure_date }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <br><br>
    <table>
        <thead>
            <tr>
                <th><small><b>REQUISITOS</b></small></th>
                <th><small><b>COSTO(S/.)</b></small></th>
                <th><small><b>OBSERVACIONES</b></small></th>
                <th><small><b>ESTADO</b></small></th>
            </tr>
        </thead>
        <tbody>
            @foreach($requirements as $requirement)
            <tr>
                <td>{{ $requirement->description }}</td>
                <td>{{ $requirement->cost }}</td>
                <td>{{ $requirement->observation }}</td>
                @if($requirement->state == 1)
                <td>
                    <input type="checkbox" checked disabled >
                </td>
                @endif
                @if($requirement->state == 2)
                <td>
                    <input type="checkbox" disabled >
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

