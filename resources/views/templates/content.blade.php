    @extends('home')

    @section('content')          
        <router-view :ruta="ruta"></router-view>     
    @endsection
