<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list"/> DEPENDENCIAS&nbsp;&nbsp;
                    <button
                        type="button"
                        @click="openModal('dependency','register'); bringDependency()"
                        class="btn btn-primary btn-sm"
                        style="color: white"
                    >
                        <i class="icon-plus"/>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="listDependency(1,entity_dependency_change_id, dependency_type_id,search)"
                                    class="form-control form-control-sm"
                                    placeholder="Texto a buscar"
                                >
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"/> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <select
                                    v-model="entity_dependency_change_id"
                                    class="form-control form-control-sm"
                                    @change="listDependency(1, entity_dependency_change_id, dependency_type_id, '')"
                                >
                                    <option value="0">Todas las entidades</option>
                                    <option
                                        v-for="entities in arrayEntityChange"
                                        :key="entities.id"
                                        :value="entities.id"
                                        v-text="entities.description"
                                    >
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <select
                                    v-model="dependency_type_id"
                                    class="form-control form-control-sm"
                                    @change="listDependency(1, entity_dependency_change_id, dependency_type_id, '')"
                                >
                                    <option value="0">Tipos de dependencias</option>
                                    <option
                                        v-for="type in arrayDependencyType"
                                        :key="type.id"
                                        :value="type.id"
                                        v-text="type.description"
                                    >
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>CÓDIGO</th>
                                <th>UNIDAD ORGÁNICA</th>
                                <th>DEPENDENCIA</th>
                                <th>RESPONSABLE</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(dependency, i) in arrayDependency" :key="`${i}-${dependency.id}`">
                                <td v-text="dependency.code"/>
                                <td v-text="dependency.description"/>
                                <td v-text="dependency.dependency"/>
                                <td v-text="dependency.fullName"/>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        @click="openModal('dependency','update',dependency)"
                                    >
                                        <i class="icon-pencil"/>Editar
                                    </button>
                                    <button
                                        v-if="auth === 1"
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="deleteDependency(dependency.id, dependency.entity_id)"
                                    >
                                        <i class="icon-trash"/>Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1,search,criterion)">Anterior</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page,search,criterion)" v-text="page"/>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1,search,criterion)">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!--Inicio del modal-->
        <div class="modal fade" tabindex="-1" :class="{'show-dependency' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                                <label class="col-md-3 form-control-label">Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="list_entity_id" class="form-control form-control-sm" @change="bringDependency">
                                        <option value="0" disabled>Seleccione Entidad</option>
                                        <option
                                            v-for="entities in arrayListEntities"
                                            :key="entities.id"
                                            :value="entities.id"
                                            v-text="entities.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.list_entity_id" class="text-danger">{{ errors.list_entity_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="arrayDependencies.length">
                                <label class="col-md-3 form-control-label">Dependencia</label>
                                <div class="col-md-9">
                                    <select v-model="dependency_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Seleccione una Dependencia</option>
                                        <option
                                            v-for="dependency in arrayDependencies"
                                            :key="dependency.id"
                                            :value="dependency.id"
                                            v-text="dependency.description"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo</label>
                                <div class="col-md-9">
                                    <select v-model="dependency_type_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Selecciona tipo de dependencia</option>
                                        <option
                                            v-for="type in arrayDependencyType"
                                            :key="type.id"
                                            :value="type.id"
                                            v-text="type.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.dependency_type_id" class="text-danger">{{ errors.dependency_type_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="typeAction === 2">
                                <label class="col-md-3 form-control-label">Dependencias</label>
                                <div class="col-md-9">
                                    <select v-model="dependency_change_id" class="form-control form-control-sm">
                                        <option value="0">Todas las entidades</option>
                                        <option
                                            v-for="entities in arrayDependencyChange"
                                            :key="entities.id"
                                            :value="entities.id"
                                            v-text="entities.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.entity_change_id" class="text-danger">{{ errors.entity_change_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unidad Orgánica</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="description" class="form-control form-control-sm" placeholder="Ingrese la Unidad Orgánica">
                                    <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Abreviatura</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="abbreviation" class="form-control form-control-sm" placeholder="Ingrese una abreviatura">
                                    <div v-if="errors && errors.abbreviation" class="text-danger">{{ errors.abbreviation[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Inicia Procedimiento:</label>
                                <div class="col-md-1">
                                    <label class="switch switch-text switch-primary">
                                        <input type="checkbox" class="switch-input" v-model="start_procedure">
                                        <span class="switch-label" data-on="SÍ" data-off="NO"/>
                                        <span class="switch-handle"/>
                                    </label>
                                </div>
                                <div v-if="validate" class="text-danger">{{validate}}</div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"/>Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary btn-sm" @click="registerDependency()">
                            <i class="fa fa-save"/> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2" class="btn btn-primary btn-sm" @click="updateDependency()">
                            <i class="fa fa-refresh"/> Actualizar
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>

    export default {
        props : ['ruta'],
        data (){
            let dependency = document.head.querySelector('meta[name="dependencyId"]');
            return {
                id: 0,
                dependency_id: 0,
                child_id: 0,
                dependency_change_id: 0,
                entity_id: 0,
                start_procedure: false,
                entity_dependency_change_id: 0,
                list_entity_id: 0,
                dependency_type_id: 1,
                description: '',
                abbreviation: '',
                dependency: '',
                arrayDependency: [],
                arrayEntities: [],
                arrayDependencies: [],
                arrayDependencyType: [],
                arrayListEntities: [],
                arrayDependencyChange: [],
                arrayEntityChange: [],
                modal: 0,
                titleModal : '',
                typeAction : 0,
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
                errors: [],
                validate: '',
                auth: 0
            }
        },
        computed:{
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
        methods : {
            listDependency (page,entity_dependency_change_id, dependency_type_id, search){
                let me=this;
                var url= this.ruta + '/dependency?page='+page+'&entity_id='+entity_dependency_change_id+'&dependency_type_id='+dependency_type_id+'&search='+search;
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayDependency = res.list_dependency.data;
                    me.pagination= res.pagination;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            listDependencyChange() {
                let me=this;
                var url= this.ruta + '/listDependencyChange';
                axios.get(url).then((response) => {
                    var res                  = response.data;
                    me.arrayDependencyChange = res.list_dependency_change;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listEntityChange() {
                let me=this;
                var url= this.ruta + '/listEntityDependencyChange';
                axios.get(url).then((response) => {
                    var res                  = response.data;
                    me.arrayEntityChange     = res.list_entity_dependency_change;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectDependency(){
                let me=this;
                var url= this.ruta + '/selectDependency';
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayListEntities = res.entities;
                    me.arrayDependencyType = res.types;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            selectEntity()
            {
                let me = this;
                var url = this.ruta +'/selectEntityType';
                axios.get(url).then((response) => {
                    var res = response.data;
                    me.arrayEntities = res.entities;
                });
            },
            getAuthUser()
            {
                let me = this;
                var url = this.ruta +'/getAuthUser';
                axios.get(url).then((response) => {
                    var res = response.data;
                    me.entity_dependency_change_id = res.entity_dependency_change_id;
                    me.list_entity_id              = res.list_entity_id;
                    me.listDependency(1, me.entity_dependency_change_id, me.dependency_type_id, me.search);
                });
            },
            bringDependency(){
                let me=this;
                var url= this.ruta + '/bringDependency/' + this.list_entity_id;
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayDependencies = res.bring_dependency;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            changePage(page){
                let me = this;
                me.pagination.current_page = page;
                me.listDependency(page, this.entity_dependency_change_id, this.dependency_type_id, '');
            },
            registerDependency(){

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                let me = this;
                axios.post(this.ruta + '/dependency/register',{
                    'description'        : this.description,
                    'abbreviation'       : this.abbreviation,
                    'entity_id'          : this.list_entity_id,
                    'dependency_type_id' : this.dependency_type_id,
                    'dependency_id'      : this.dependency_id,
                    'list_entity_id'     : this.list_entity_id,
                    'start_procedure'    : this.start_procedure
                }).then(() => {
                    me.closeModal();
                    me.listDependency(1, me.entity_dependency_change_id, me.dependency_type_id, me.search);
                    me.listDependencyChange();
                }, function (error) {
                    me.errors = error.response.data.errors;
                    me.validate = error.response.data.validates;
                });
            },
            updateDependency(){
                let me = this;
                axios.put(this.ruta + '/dependency/update',{
                    'description'         : this.description,
                    'abbreviation'        : this.abbreviation,
                    'dependency'          : this.dependency,
                    'entity_id'           : this.list_entity_id,
                    'dependency_type_id'  : this.dependency_type_id,
                    'dependency_change_id': this.dependency_change_id,
                    'id'                  : this.id,
                    'child_id'            : this.child_id,
                    'list_entity_id'      : this.list_entity_id,
                    'start_procedure'     : this.start_procedure
                }).then(() => {
                    me.listDependency(1, me.entity_dependency_change_id, me.dependency_type_id, me.search);
                    me.listDependencyChange();
                    me.closeModal();
                }, function (error) {
                    me.errors = error.response.data.errors;
                    me.validate = error.response.data.validates;
                });
            },
            closeModal(){
                this.modal                = 0;
                this.titleModal           = '';
                this.code                 = '';
                this.description          = '';
                this.abbreviation         = '';
                this.list_entity_id       = 5;
                this.dependency_type_id   = 1;
                this.dependency_change_id = 0;
                this.id                   = 0;
                this.dependency_id        = 0;
                this.child_id             = 0;
                this.errors               = [];
                this.start_procedure      = false;
                this.validate             = ''
            },
            openModal(model, action, data = []){
                switch(model){
                    case "dependency":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal = 1;
                                this.titleModal = 'Registrar Dependencia';
                                this.code= '';
                                this.dependency='';
                                this.typeAction = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal                = 1;
                                this.titleModal           = 'Actualizar Dependencia';
                                this.typeAction           = 2;
                                this.description          = data['description'];
                                this.abbreviation         = data['abbreviation'];
                                this.dependency           = data['dependency'];
                                this.dependency_type_id   = data['dependency_type_id'];
                                this.id                   = data['id'];
                                this.list_entity_id       = data['entity_id'];
                                this.dependency_change_id = data['parent'];
                                this.child_id             = data['parent'];
                                this.start_procedure      = data['start_procedure'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteDependency(id, entity_id){
                Swal.fire({
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
                        axios.patch(this.ruta + '/dependency?id=' + id + '&entity_id='+ entity_id).then(() => {
                            me.listDependency(1, me.entity_dependency_change_id, me.dependency_type_id, me.search);
                            me.listDependencyChange();
                            Swal.fire(
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
            },
            getDocumentAuth()
            {
                let me=this;
                var url= me.ruta + '/documentAuth';
                axios.get(url).then(function (response) {
                    me.auth = response.data.auth;
                })
                .catch(function (error) {
                    console.log(error);
                })
            }
        },
        created() {
            this.getAuthUser();
            this.getDocumentAuth();
        },
        mounted() {
            this.listDependencyChange();
            this.listEntityChange();
            this.selectDependency();
            this.selectDependency();
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-dependency{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
</style>
