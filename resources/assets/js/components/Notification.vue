<template>
    <li class="nav-item d-md-down-none">
        <a class="nav-link" href="#" data-toggle="dropdown">
            <i class="icon-bell"/>
            <span class="badge badge-pill badge-danger" v-if="arrayPending.length">{{arrayPending.length}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
                <strong>DOCUMENTOS</strong>
            </div>
            <div v-if="arrayPending.length">
                <a class="dropdown-item" href="#">
                    <i class="icon-doc"/>Pendientes
                    <span class="badge badge-pill badge-success">{{ arrayPending.length}}</span>
                </a>
            </div>
            <div v-else>
                <a><span>No tiene notificaciones</span></a>
            </div>
        </div>
    </li>
</template>

<script>
export default {
	props : ['ruta'],
    data (){
        return {
            arrayNotifications:[],
            arrayPending: []
        }
    },
    /*computed:{
        listar ()  {
            //return this.notifications[0];
             this.arrayNotifications = Object.values(this.notifications[0]);
            if (this.notifications === '') {
                    return this.arrayNotifications = [];
            } else {
                //Capturo la ultima notificación agregada
                this.arrayNotifications = Object.values(this.notifications[0]);
                //Validación por indice fuera de rango
                if (this.arrayNotifications.length > 3) {
                    //Si el tamaño es > 3 Es cuando las notificaciones son obtenidas desde el mismo servidor, es decir por la consulta con AXIOS
                    return Object.values(this.arrayNotifications[4]);
                } else {
                    //Si el tamaño es < 3 Es cuando las notificaciones son obtenidas desde el canal privado, es decir mediante Laravel Echo y Pusher
                    return Object.values(this.arrayNotifications[0]);
                }
            }
        }
    }*/
    methods: {
        getCountPending()
        {
            let me=this;
            var url= `${this.ruta}/pending`;

            axios.get(url).then((response) => {
                me.arrayPending = response.data.pending;
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    },
    mounted() {
        this.getCountPending()
    }
}
</script>







