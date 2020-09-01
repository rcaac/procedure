<template>
    <main class="main">
        <ol/>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"/> ORIGEN DE DOCUMENTO &nbsp;
                    <button type="button" @click="openModal('origin','register')" class="btn btn-primary btn-sm" style="color: white">
                        <i class="icon-plus"/>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listDocumentOrigin(1,search)" class="form-control form-control-sm" placeholder="Texto a buscar">
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"/> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>ORÍGEN DE DOCUMENTO</th>
                            <th>ACCIONES</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="origin in arrayDocumentOrigin" :key="origin.id" v-model="origin.id">
                            <td v-text="origin.origin"/>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('origin','update',origin)">
                                    <i class="icon-pencil"/>
                                </button> &nbsp;
                                <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteDocumentOrigin(origin.id)">
                                    <i class="icon-trash"/>
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
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page,search,criterion)" v-text="page"/>
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
        <div class="modal fade" tabindex="-1" :class="{'show-document-origin' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"/>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Origen de Documento</label>
                                <div class="col-md-10">
                                    <input type="text" v-model="origin" class="form-control form-control-sm" placeholder="Ingrese origen de documento">
                                    <div v-if="errors && errors.origin" class="text-danger">{{ errors.origin[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" style="color: white" @click="closeModal()">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary btn-sm"  style="color: white" @click="registerDocumentOrigin()">
                            <i class="fa fa-save"/> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2" class="btn btn-primary btn-sm" style="color: white" @click="updateDocumentOrigin()">
                            <i class="fa fa-refresh"/> Actualizar
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
                arrayDocumentOrigin: [],
                origin: '',
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
            listDocumentOrigin (page,search){
                let me=this;
                var url= this.ruta + '/documentOrigin?page=' + page + '&search='+ search;
                axios.get(url).then(function (response) {
                    var res= response.data;
                    me.arrayDocumentOrigin = res.origin.data;
                    me.pagination= res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search,){
                let me = this;
                me.pagination.current_page = page;
                me.listDocumentOrigin(page,search,);
            },
            registerDocumentOrigin(){
                let me = this;
                axios.post(this.ruta + '/documentOrigin/register',{
                    'origin': this.origin,
                }).then(() => {
                    me.closeModal();
                    me.listDocumentOrigin(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            updateDocumentOrigin(){
                let me = this;
                axios.put(this.ruta + '/documentOrigin/update',{
                    'origin': this.origin,
                    'id': this.id
                }).then(() => {
                    me.closeModal();
                    me.listDocumentOrigin(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal(){
                this.modal      = 0;
                this.typeAction = 0;
                this.titleModal = '';
                this.origin     = '';
                this.errors     = [];
            },
            openModal(model, action, data = []){
                switch(model){
                    case "origin":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal = 1;
                                this.titleModal = 'Registrar Origen de Documento';
                                this.origin='';
                                this.typeAction = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal=1;
                                this.titleModal='Actualizar Origen de Documento';
                                this.typeAction=2;
                                this.origin=data['origin'];
                                this.id=data['id'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteDocumentOrigin(id) {
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
                        axios.patch(this.ruta + '/documentOrigin/' + id).then(() => {
                            me.listDocumentOrigin(1,this.search);
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
            this.listDocumentOrigin(1,this.search);
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-document-origin{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>
