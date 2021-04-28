<template>
    <main class="main">
        <ol></ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-institution"></i> ENTIDADES&nbsp;&nbsp;
                    <button type="button" @click="openModal('entity','register')" class="btn btn-primary btn-sm" style="color: white">
                        <i class="icon-plus"></i> Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listEntity(1,0,search)" class="form-control form-control-sm" placeholder="Texto a buscar">
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <select v-model="entity_change_id" class="form-control form-control-sm" @change="listEntity(1, entity_change_id, '')">
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
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>CÓDIGO</th>
                                <th>ENTIDAD</th>
                                <th>DIRECCIÓN</th>
                                <th>RUC</th>
                                <th>DEPENDENCIA</th>
                                <th>ACCIONES</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="entity in arrayEntity" :key="entity.id">
                                <td v-text="entity.code"></td>
                                <td v-text="entity.description"></td>
                                <td v-text="entity.direction"></td>
                                <td v-text="entity.ruc"></td>
                                <td v-text="entity.dependency"></td>
                                <td v-text="entity.child_id" style="display:none;"></td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        @click="openModal('entity','update',entity)"
                                    >
                                        <i class="icon-pencil"></i> Editar
                                    </button> &nbsp;
                                    <button
                                        v-if="auth === 1"
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="deleteEntity(entity.id)"
                                    >
                                        <i class="icon-trash"></i> Eliminar
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
                                <a class="page-link" href="#" @click.prevent="changePage(page,search,criterion)" v-text="page"></a>
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
        <div class="modal fade" tabindex="-1" :class="{'show-entity' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                                <label class="col-md-3 form-control-label">Tipo de Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="entity_type_id" class="form-control form-control-sm" @change="selectEntity">
                                        <option value="0" disabled>Seleccione Tipo de Entidad</option>
                                        <option
                                            v-for="entityType in arrayEntityType"
                                            :key="entityType.id"
                                            :value="entityType.id"
                                            v-text="entityType.type"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.entity_type_id" class="text-danger">{{ errors.entity_type_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="(typeAction === 1) && (arrayEntities.length)">
                                <label class="col-md-3 form-control-label">Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="entity_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Seleccione Entidad</option>
                                        <option
                                            v-for="entities in arrayEntities"
                                            :key="entities.id"
                                            :value="entities.id"
                                            v-text="entities.description"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" v-if="typeAction === 2">
                                <label class="col-md-3 form-control-label">Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="entity_change_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Seleccione Entidad</option>
                                        <option
                                            v-for="entities in arrayEntityChange "
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
                                <label class="col-md-3 form-control-label">Entidad</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="description" class="form-control form-control-sm" placeholder="Ingrese la Entidad">
                                    <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Dirección</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="direction" class="form-control form-control-sm" placeholder="Ingrese la Dirección">
                                    <div v-if="errors && errors.direction" class="text-danger">{{ errors.direction[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">RUC</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="ruc" class="form-control form-control-sm" placeholder="Ingrese el RUC">
                                    <div v-if="errors && errors.ruc" class="text-danger">{{ errors.ruc[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Abreviatura</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="abbreviation" class="form-control form-control-sm" placeholder="Ingrese una abreviatura">
                                    <div v-if="errors && errors.abbreviation" class="text-danger">{{ errors.abbreviation[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary btn-sm" @click="registerEntity()" style="color: white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2" class="btn btn-primary btn-sm" @click="updateEntity()" style="color: white">
                            <i class="fa fa-refresh"></i> Actualizar
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
        data () {
            return {
                id: 0,
                entity_id: 0,
                entity_type_id: 0,
                entity_change_id: 0,
                child_id: 0,
                code: '',
                description: '',
                direction: '',
                ruc: '',
                abbreviation: '',
                dependency: '',
                modal: 0,
                titleModal : '',
                typeAction : 0,
                arrayEntity: [],
                arrayEntityType: [],
                arrayEntities: [],
                arrayEntityChange: [],
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
                search : String(),
                errors: [],
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
                let from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                let pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods : {
            listEntity(page, id, search) {
                let me=this;
                let url= this.ruta + '/entity?page=' + page + '&id=' + id + '&search=' + search;
                axios.get(url).then((response) => {
                    let res= response.data;
                    me.arrayEntity = res.list_dependency_entity.data;
                    me.pagination  = res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectEntityType() {
                let me=this;
                var url= this.ruta + '/selectEntityType';
                axios.get(url).then((response) => {
                    var res            = response.data;
                    me.arrayEntityType = res.type;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listEntityChange() {
                let me=this;
                var url= this.ruta + '/listEntityChange';
                axios.get(url).then((response) => {
                    var res              = response.data;
                    me.arrayEntityChange = res.list_entity_change;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectEntity() {
                let me=this;
                var url= this.ruta + '/selectListEntity/' + this.entity_type_id;
                axios.get(url).then((response) => {
                    var res          = response.data;
                    me.arrayEntities = res.list;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page) {
                let me = this;
                me.pagination.current_page = page;
                me.listEntity(page, this.entity_change_id, this.search);
            },
            registerEntity() {

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                let me = this;
                axios.post(this.ruta + '/entity/register',{
                    'description':    this.description,
                    'direction':      this.direction,
                    'ruc':            this.ruc,
                    'abbreviation':   this.abbreviation,
                    'entity_type_id': this.entity_type_id,
                    'entity_id':      this.entity_id
                }).then(() => {
                    me.closeModal();
                    this.listEntity(1,this.entity_change_id, this.search);
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            updateEntity() {
                let me = this;
                axios.put(this.ruta + '/entity/update',{
                    'description':      this.description,
                    'direction':        this.direction,
                    'ruc':              this.ruc,
                    'abbreviation':     this.abbreviation,
                    'dependency':       this.dependency,
                    'entity_type_id':   this.entity_type_id,
                    'entity_change_id': this.entity_change_id,
                    'id':               this.id,
                    'child_id':         this.child_id
                }).then(() => {
                    me.closeModal();
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal() {
                this.modal           = 0;
                this.titleModal      = '';
                this.code            = '';
                this.description     = '';
                this.direction       = '';
                this.ruc             = '';
                this.abbreviation    = '';
                this.dependency      = '';
                this.entity_type_id  = 0;
                this.entity_id       = 0;
                this.id              = 0;
                this.arrayEntities   = [];
                this.child_id        = 0;
                this.entity_change_id = 0;
                this.errors = {}
            },
            openModal(model, action, data = []) {
                switch (model) {
                    case "entity":
                    {
                        switch (action) {
                            case 'register':
                            {
                                this.entity_id    = 0;
                                this.modal        = 1;
                                this.titleModal   = 'Registrar Entidad';
                                this.description  = '';
                                this.direction    = '';
                                this.ruc          = '';
                                this.abbreviation = '';
                                this.typeAction   = 1;
                                break;
                            }
                            case 'update':
                            {
                                this.modal            = 1;
                                this.titleModal       = 'Actualizar Entidad';
                                this.typeAction       = 2;
                                this.description      = data['description'];
                                this.direction        = data['direction'];
                                this.ruc              = data['ruc'];
                                this.abbreviation     = data['abbreviation'];
                                this.dependency       = data['dependency'];
                                this.entity_type_id   = data['entity_type_id'];
                                this.id               = data['id'];
                                this.entity_change_id = data['parent'];
                                this.child_id         = data['parent'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteEntity(id) {
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
                        axios.patch(this.ruta + '/entity/trash/' + id).then(() => {
                            me.listEntity(1,0, this.search);
                            me.listEntityChange();
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
        created(){
            this.getDocumentAuth();
        },
        mounted() {
            this.listEntity(1,this.entity_change_id, this.search);
            this.listEntityChange();
            this.selectEntityType();
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-entity{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
</style>


