<template>
    <main class="main">
        <ol></ol>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-th"></i> TIPOS DE ATENCIÓN &nbsp;
                    <button type="button" @click="openModal('types','register')" class="btn btn-primary btn-sm">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listAttentionTypes(1,search)" class="form-control form-control-sm" placeholder="Texto a search">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>TIPO DE ATENCIÓN</th>
                            <th>ACCIONES</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="types in arrayAttentionTypes" :key="types.id">
                            <td v-text="types.description"></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('types','update',types)">
                                    <i class="icon-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteAttentionTypes(types.id)">
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
        <div class="modal fade" tabindex="-1" :class="{'show-attention-type' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Recurso:</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="description" class="form-control form-control-sm" placeholder="Ingrese tipo de recurso">
                                    <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Detalle de Recurso:</label>
                                <div class="col-md-1">
                                    <label class="switch switch-text switch-primary">
                                        <input type="checkbox" class="switch-input" v-model="resource_detail" checked="1">
                                        <span class="switch-label" data-on="SÍ" data-off="NO"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction == 1" class="btn btn-primary btn-sm" @click="registerAttentionTypes()" style="color: white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction == 2" class="btn btn-primary btn-sm" @click="updateAttentionTypes()" style="color: white">
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
                arrayAttentionTypes: [],
                description: '',
                resource_detail: false,
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
            listAttentionTypes(page,search){
                let me=this;
                var url= this.ruta + '/attentionTypes?page=' + page + '&search='+ search;
                axios.get(url).then(function (response) {
                    var res= response.data;
                    me.arrayAttentionTypes = res.types.data;
                    me.pagination= res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search){
                let me = this;
                me.pagination.current_page = page;
                me.listAttentionTypes(page,search);
            },
            registerAttentionTypes(){
                let me = this;
                axios.post(this.ruta + '/attentionTypes/register',{
                    'description': this.description,
                    'resource_detail': this.resource_detail
                }).then(() => {
                    me.closeModal();
                    me.listAttentionTypes(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            updateAttentionTypes(){
                let me = this;
                axios.put(this.ruta + '/attentionTypes/update',{
                    'description': this.description,
                    'resource_detail': this.resource_detail,
                    'id': this.id
                }).then(() => {
                    me.closeModal();
                    me.listAttentionTypes(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal(){
                this.modal=0;
                this.titleModal='';
                this.description='';
                this.resource_detail=false;
                this.errors = [];
            },
            openModal(model, action, data = []){
                switch(model){
                    case "types":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal       = 1;
                                this.titleModal  = 'Registrar Recurso';
                                this.description ='';
                                this.typeAction  = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal           = 1;
                                this.titleModal      = 'Actualizar Usuario';
                                this.typeAction      = 2;
                                this.description     = data['description'];
                                this.resource_detail = data['resource_detail'];
                                this.id              = data['id'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteAttentionTypes(id) {
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
                        axios.patch(this.ruta + '/attentionTypes/' + id).then(() => {
                            me.listAttentionTypes(1,this.search);
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
            this.listAttentionTypes(1,this.search);
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-attention-type{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>





