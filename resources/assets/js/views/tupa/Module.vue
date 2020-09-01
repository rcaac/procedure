<template>
    <main class="main">
        <ol></ol>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-cube"></i> MÓDULOS &nbsp;
                    <button type="button" @click="openModal('module','register')" class="btn btn-primary btn-sm">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listModule(1,search)" class="form-control form-control-sm" placeholder="Texto a search">
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>MÓDULO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="modules in arrayModules" :key="modules.id">
                                <td v-text="modules.description"></td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('module','update',modules)" >
                                        <i class="icon-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteModule(modules.id)">
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
        <div class="modal fade" tabindex="-1" :class="{'show-module' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                                <label class="col-md-1 form-control-label">Módulo:</label>
                                <div class="col-md-8">
                                    <input type="text" v-model="description" class="form-control form-control-sm" placeholder="Ingrese el módulo">
                                    <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction == 1" class="btn btn-primary btn-sm" @click="registerModule()" style="color: white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction == 2" class="btn btn-primary btn-sm" @click="updateModule()" style="color: white">
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
                arrayModules: [],
                description: '',
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
                id: 0,
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
            listModule(page,search){
                let me=this;
                var url= this.ruta + '/module?page=' + page + '&search='+ search;
                axios.get(url).then(function (response) {
                    var res= response.data;
                    me.arrayModules = res.modules.data;
                    me.pagination= res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search){
                let me = this;
                me.pagination.current_page = page;
                me.listModule(page,search);
            },
            registerModule(){
                let me = this;
                axios.post(this.ruta + '/module/register',{
                    'description': this.description,
                }).then(() => {
                    me.closeModal();
                    me.listModule(1,'','code');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            updateModule(){
                let me = this;
                axios.put(this.ruta + '/module/update',{
                    'description': this.description,
                    'id'         : this.id
                }).then(() => {
                    me.closeModal();
                    me.listModule(1,'');
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal(){
                this.modal=0;
                this.titleModal='';
                this.description='';
                this.errors = [];
            },
            openModal(model, action, data = []){
                switch(model){
                    case "module":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal = 1;
                                this.titleModal = 'Registrar Módulo';
                                this.description='';
                                this.typeAction = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal=1;
                                this.titleModal='Actualizar Módulo';
                                this.typeAction=2;
                                this.description=data['description'];
                                this.id=data['id'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteModule(id) {
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
                        axios.patch(this.ruta + '/module/' + id).then(() => {
                            me.listModule(1,this.search);
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
            this.listModule(1,this.search,this.criterion);
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-module{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>



