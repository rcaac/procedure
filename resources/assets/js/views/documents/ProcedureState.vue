<template>
    <main class="main">
        <ol></ol>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> ESTADO DEL TRÁMITE &nbsp;
                    <button type="button" @click="openModal('procedure','register')" class="btn btn-primary btn-sm" style="color: white">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listProcessState(1,search)" class="form-control form-control-sm" placeholder="Texto a buscar">
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>ESTADO DEL TRÁMITE</th>
                            <th>ACCIONES</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="state in arrayProcedureState" :key="state.id" v-model="state.id">
                            <td v-text="state.state"></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('procedure','update',state)">
                                    <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteProcedureState(state.id)">
                                    <i class="icon-trash"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1,search,criterion)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page,search,criterion)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1,search,criterion)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'show-procedure-state' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Estado del Trámite</label>
                                <div class="col-md-10">
                                    <input type="text" v-model="state" class="form-control form-control-sm" placeholder="Ingrese el estado del trámite">
                                    <div v-if="errors && errors.state" class="text-danger">{{ errors.state[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" style="color: white" @click="closeModal()">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary btn-sm"  style="color: white" @click="registerProcedureState()">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2" class="btn btn-primary btn-sm" style="color: white" @click="updateProcedureState()">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data () {
            return {
                arrayProcedureState: [],
                state: '',
                id: 0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterion : '',
                search : '',
                modal: 0,
                titleModal: 0,
                typeAction: 0,
                errors: []
            }
        },
        computed: {
            isActived: function(){
                return this.pagination.current_page;
            },
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                var from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods: {
            listProcessState (page,search){
                let me=this;
                var url= this.ruta + '/procedureState?page=' + page + '&search='+ search;
                axios.get(url).then(function (response) {
                    var res= response.data;
                    me.arrayProcedureState = res.state.data;
                    me.pagination= res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search,){
                let me = this;
                me.pagination.current_page = page;
                me.listProcessState(page,search,);
            },
            registerProcedureState(){
                let me = this;
                axios.post(this.ruta + '/procedureState/register',{
                    'state': this.state,
                }).then(() => {
                    me.closeModal();
                    me.listProcessState(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            updateProcedureState(){
                let me = this;
                axios.put(this.ruta + '/procedureState/update',{
                    'state': this.state,
                    'id': this.id
                }).then(() => {
                    me.closeModal();
                    me.listProcessState(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal(){
                this.modal      = 0;
                this.typeAction = 0;
                this.titleModal = '';
                this.state      = '';
                this.errors     = [];
            },
            openModal(model, action, data = []){
                switch(model){
                    case "procedure":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal = 1;
                                this.titleModal = 'Registrar Estado del Trámite';
                                this.state='';
                                this.typeAction = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal=1;
                                this.titleModal='Actualizar Estado del Trámite';
                                this.typeAction=2;
                                this.state=data['state'];
                                this.id=data['id'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteProcedureState(id) {
                swal({
                    title: 'Esta seguro de eliminar este registro?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.patch(this.ruta + '/procedureState/' + id).then(() => {
                            me.listProcessState(1,this.search);
                            swal(
                                'Eliminado!',
                                'El registro ha sido eliminado con éxito.',
                                'success'
                            )
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {

                    }
                })
            }
        },
        mounted() {
            this.listProcessState(1,this.search);
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-procedure-state{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>
