<template>
    <div>
        <br>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Entidad:</label>
            <div class="col-md-6">
                <v-select
                    v-model="entity.selectedEntity"
                    :options="origin_data.entity_external"
                    placeholder="Elije una entidad"
                    @input="getDependencyEntity()"
                    class="small"
                    :clearable="false"
                />
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <button
                        type="submit"
                        @click="openModalEntity('entity', 'register')"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="icon-plus"/>
                        Entidad
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group row" v-show="origin_data.dependencyEntity.length || show === true">
            <label class="col-md-2 form-control-label">Dependencia:</label>
            <div class="col-md-6">
                <v-select
                    v-model="origin_data.selectedDependency"
                    :options="origin_data.dependencyEntity"
                    placeholder="Elije una Dependencia"
                    @input="getDependencyCharge()"
                    class="small"
                    :clearable="false"
                />
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <button
                        type="submit"
                        @click="openModalDependency('dependency', 'register')"
                        class="btn btn-primary btn-sm"
                    >
                        <i class="icon-plus"/>
                        Dependencia
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group row" v-if="Object.keys(origin_data.dependency_charge).length">
            <label class="col-md-2 form-control-label">Responsable:</label>
            <div class="col-md-6">
                <input type="text" v-model="origin_data.dependency_charge.fullName" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row" v-if="Object.keys(origin_data.dependency_charge).length">
            <label class="col-md-2 form-control-label">Cargo:</label>
            <div class="col-md-6">
                <input type="text" v-model="origin_data.dependency_charge.charge" class="form-control form-control-sm">
            </div>
        </div>
        <!--Inicio del modal-->
        <div class="modal fade" tabindex="-1" :class="{'show-entity' : modal1}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="entity.titleModal"/>
                        <button type="button" class="close" @click="closeModalEntity()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo de Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="entity.entity_type_id" class="form-control form-control-sm" @change="selectEntity">
                                        <option value="0" disabled>Seleccione Tipo de Entidad</option>
                                        <option
                                            v-for="entityType in entity.arrayEntityType"
                                            :key="entityType.id"
                                            :value="entityType.id"
                                            v-text="entityType.type"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="entity.errors && entity.errors.entity_type_id" class="text-danger">{{ entity.errors.entity_type_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="entity.arrayEntities.length">
                                <label class="col-md-3 form-control-label">Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="entity.entity_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Seleccione Entidad</option>
                                        <option
                                            v-for="entities in entity.arrayEntities"
                                            :key="entities.id"
                                            :value="entities.id"
                                            v-text="entities.description"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Entidad</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="entity.description" class="form-control form-control-sm" placeholder="Ingrese la Entidad">
                                    <div v-if="entity.errors && entity.errors.description" class="text-danger">{{ entity.errors.description[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Dirección</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="entity.direction" class="form-control form-control-sm" placeholder="Ingrese la Dirección">
                                    <div v-if="entity.errors && entity.errors.direction" class="text-danger">{{ entity.errors.direction[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">RUC</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="entity.ruc" class="form-control form-control-sm" placeholder="Ingrese el RUC">
                                    <div v-if="entity.errors && entity.errors.ruc" class="text-danger">{{ entity.errors.ruc[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Abreviatura</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="entity.abbreviation" class="form-control form-control-sm" placeholder="Ingrese una abreviatura">
                                    <div v-if="entity.errors && entity.errors.abbreviation" class="text-danger">{{ entity.errors.abbreviation[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModalEntity()" style="color: white">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" @click="registerEntity()" style="color: white">
                            <i class="fa fa-save"/> Guardar
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
        <!--Inicio del modal-->
        <div class="modal fade" tabindex="-1" :class="{'show-dependency' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="dependency.titleModal"/>
                        <button type="button" class="close" @click="closeModalDependency()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Entidades</label>
                                <div class="col-md-9">
                                    <select v-model="dependency.list_entity_id" class="form-control form-control-sm" @change="bringDependency">
                                        <option value="0" disabled>Elije una Entidad</option>
                                        <option
                                            v-for="entities in dependency.arrayListEntities"
                                            :key="entities.id"
                                            :value="entities.id"
                                            v-text="entities.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.list_entity_id" class="text-danger">{{ errors.list_entity_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row" v-if="dependency.arrayDependencies.length">
                                <label class="col-md-3 form-control-label">Dependencia</label>
                                <div class="col-md-9">
                                    <select v-model="dependency.dependency_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Seleccione una Dependencia</option>
                                        <option
                                            v-for="dependencies in dependency.arrayDependencies"
                                            :key="dependencies.id"
                                            :value="dependencies.id"
                                            v-text="dependencies.description"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Tipo</label>
                                <div class="col-md-9">
                                    <select v-model="dependency.dependency_type_id" class="form-control form-control-sm">
                                        <option value="0" disabled>Selecciona tipo de dependencia</option>
                                        <option
                                            v-for="type in dependency.arrayDependencyType"
                                            :key="type.id"
                                            :value="type.id"
                                            v-text="type.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.dependency_type_id" class="text-danger">{{ errors.dependency_type_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Unidad Orgánica</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="dependency.description" class="form-control form-control-sm" placeholder="Ingrese la Unidad Orgánica">
                                    <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Abreviatura</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="dependency.abbreviation" class="form-control form-control-sm" placeholder="Ingrese una abreviatura">
                                    <div v-if="errors && errors.abbreviation" class="text-danger">{{ errors.abbreviation[0] }}</div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label class="switch switch-sm switch-3d switch-primary">
                                        <input type="checkbox" class="switch-input" v-model="destination" @change="changeDstination()">
                                        <span class="switch-label"/>
                                        <span class="switch-handle"/>
                                    </label>
                                    &nbsp;Buscar a usuario
                                </div>
                                <div class="form-group col-sm-3" v-if="destination === true">
                                    <vue-bootstrap-typeahead
                                        v-model="query"
                                        :data="users"
                                        :serializer="item => item.dni"
                                        @hit="origin_data.selectedCitizen = $event"
                                        placeholder="Buscar por DNI"
                                        size="sm"
                                        ref="typeahead"
                                    />
                                </div>
                                <div class="col-sm-9" v-else>
                                    <span>Asignar Responsable</span>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <select v-model="person.identification_document_id" class="form-control form-control-sm">
                                                        <option
                                                            v-for="identificationDocument in person.arrayIdentificationDocument"
                                                            :key="identificationDocument.id"
                                                            :value="identificationDocument.id"
                                                            v-text="identificationDocument.document"
                                                        >
                                                        </option>
                                                    </select>
                                                    <div v-if="errors && errors.identification_document_id" class="text-danger">{{ errors.identification_document_id[0] }}</div>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <input type="email" v-model="person.dni" class="form-control form-control-sm" placeholder="Número de dni">
                                                    <div v-if="errors && errors.dni" class="text-danger">{{ errors.dni[0] }}</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" v-model="person.firstName" class="form-control form-control-sm" placeholder="Nombre de la persona">
                                                    <div v-if="errors && errors.firstName" class="text-danger">{{ errors.firstName[0] }}</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" v-model="person.lastName" class="form-control form-control-sm" placeholder="Apellidos de la persona">
                                                    <div v-if="errors && errors.lastName" class="text-danger">{{ errors.lastName[0] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="bg-light mt-2" v-if="users.length && destination === true">{{this.users[0].fullName}}</p>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModalDependency()" style="color: white">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" @click="registerPersonDependency()">
                            <i class="fa fa-save"/> Guardar
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </div>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
    export default {
        components: {
            VueBootstrapTypeahead,
            vSelect
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            }
        },
        watch: {
            query(newQuery) {
                if (this.query.length > 0) {
                    var url = this.origin_data.route + '/documentPersonQuery?newQuery=' + newQuery;
                    axios.get(url)
                    .then((res) => {
                        this.users = res.data.person_charge;
                        if (this.users.length){
                            this.person =  {
                                identification_document_id: 1,
                                dni: this.users[0].dni,
                                firstName: this.users[0].firstName,
                                lastName: this.users[0].lastName,
                                phone: '',
                                email: '',
                                direction: '',
                                dependency_citizen_id: this.users[0].id,
                                person_id: this.users[0].person_id,
                            }
                        }
                    })
                }
            }
        },
        data(){
            return {
                modal1 : 0,
                modal2 : 0,
                show: false,
                query: '',
                destination: false,
                users: [],
                errors: [],
                entity: {
                    arrayEntityType: [],
                    errors: [],
                    arrayEntities: [],
                    arrayEntityChange: [],
                    entity_id: 0,
                    entity_type_id: 0,
                    entity_change_id: 0,
                    description: '',
                    direction: '',
                    ruc: '',
                    abbreviation: '',
                    titleModal: '',
                    selectedEntity: {}
                },
                dependency: {
                    list_entity_id: 0,
                    dependency_id: 0,
                    arrayListEntities: [],
                    arrayDependencies: [],
                    arrayDependencyType: [],
                    dependency_type_id: 1,
                    description: '',
                    abbreviation: '',
                    start_procedure: false,
                    titleModal: ''
                },
                person: {
                    identification_document_id: 1,
                    arrayIdentificationDocument: [],
                    dni: '',
                    firstName: '',
                    lastName: '',
                    phone: '',
                    email: '',
                    direction: '',
                    dependency_citizen_id: 0,
                    person_id: 0
                }
            }
        },
        methods: {
            listEntityChange() {
                let me=this;
                var url= this.origin_data.route + '/entityExternal';
                axios.get(url).then((response) => {
                    var res                        = response.data;
                    me.origin_data.entity_external = res.entity_external;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDependencyEntity()
            {
                this.origin_data.dependency_charge = {};
                this.origin_data.dependencyEntity = [];
                this.origin_data.selectedDependency = '';
                var url = this.origin_data.route + '/documentDependency/' + this.entity.selectedEntity.id;
                axios.get(url)
                .then((res) => {
                    this.origin_data.dependencyEntity = res.data.dependencies;
                    if (this.origin_data.dependencyEntity.length === 0){
                        this.origin_data.selectedDependency = {id: 0, label: 'No hay opciones que mostrar'};
                        this.show = true;
                    }
                })
            },
            getDependencyCharge()
            {
                var url = this.origin_data.route + '/documentDependencyCharge/' + this.origin_data.selectedDependency.id;
                axios.get(url)
                .then((res) => {
                    this.origin_data.dependency_charge = res.data.dependency_charge;
                })
            },
            changeDstination()
            {
                this.users  = [];
                this.person = {}
            },
            selectEntity() {
                let me=this;
                var url= this.origin_data.route + '/selectListEntity/' + this.entity.entity_type_id;
                axios.get(url).then((response) => {
                    var res                 = response.data;
                    me.entity.arrayEntities = res.list;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectEntityType() {
                let me=this;
                var url= this.origin_data.route + '/selectEntityType';
                axios.get(url).then((response) => {
                    var res                   = response.data;
                    me.entity.arrayEntityType = res.type;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registerEntity() {

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                let me = this;
                axios.post(this.origin_data.route + '/entity/register',{
                    'description':    this.entity.description,
                    'direction':      this.entity.direction,
                    'ruc':            this.entity.ruc,
                    'abbreviation':   this.entity.abbreviation,
                    'entity_type_id': this.entity.entity_type_id,
                    'entity_id':      this.entity.entity_id
                }).then((response) => {
                    me.closeModalEntity();
                    me.entity.selectedEntity = {
                        id: response.data.id,
                        label: response.data.description
                    };
                    me.listEntityChange();
                    me.getDependencyEntity()
                },function (error) {
                    me.entity.errors = error.response.data.errors
                });
            },
            openModalEntity(model, action) {
                this.selectEntityType();
                switch (model) {
                    case "entity":
                    {
                        switch (action) {
                            case 'register':
                            {
                                this.entity_id    = 0;
                                this.modal1       = 1;
                                this.entity.titleModal   = 'Registrar Entidad';
                                this.entity.description  = '';
                                this.entity.direction    = '';
                                this.entity.ruc          = '';
                                this.entity.abbreviation = '';
                                break;
                            }
                        }
                    }
                }
            },
            closeModalEntity() {
                this.modal1                 = 0;
                this.entity.titleModal      = '';
                this.entity.description     = '';
                this.entity.direction       = '';
                this.entity.ruc             = '';
                this.entity.abbreviation    = '';
                this.entity.dependency      = '';
                this.entity.entity_type_id  = 0;
                this.entity.entity_id       = 0;
                this.entity.id              = 0;
                this.entity.arrayEntities   = [];
                this.entity.arrayEntityType = [];
                this.entity.child_id        = 0;
                this.entity.errors = {}
            },
            bringDependency(){
                let me=this;
                var url= this.origin_data.route + '/bringDependency/' + this.dependency.list_entity_id;
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.dependency.arrayDependencies = res.bring_dependency;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            selectDependency(){
                let me=this;
                var url= this.origin_data.route + '/selectDependency';
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.dependency.arrayListEntities = res.entities;
                    me.dependency.arrayDependencyType = res.types;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            registerPersonDependency(){

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 5000);

                let me = this;
                axios.post(this.origin_data.route + '/personDependency',{
                    'description'               : this.dependency.description,
                    'abbreviation'              : this.dependency.abbreviation,
                    'entity_id'                 : this.dependency.list_entity_id,
                    'dependency_type_id'        : this.dependency.dependency_type_id,
                    'dependency_id'             : this.dependency.dependency_id,
                    'list_entity_id'            : this.dependency.list_entity_id,
                    'start_procedure'           : this.dependency.start_procedure,
                    'firstName'                 : this.person.firstName,
                    'lastName'                  : this.person.lastName,
                    'dni'                       : this.person.dni,
                    'phone'                     : this.person.phone,
                    'email'                     : this.person.email,
                    'direction'                 : this.person.direction,
                    'identification_document_id': this.person.identification_document_id,
                    'person_id'                 : this.person.person_id
                }).then((response) => {
                    me.origin_data.selectedDependency = {
                        id: response.data.id,
                        label: response.data.description
                    };
                    me.closeModalDependency();
                    me.getDependencyCharge();
                }, function (error) {
                    me.errors = error.response.data.errors;
                });
            },
            openModalDependency(model, action){
                this.dependency.list_entity_id = this.entity.selectedEntity.id;
                this.selectDependency();
                this.bringDependency();
                this.getIdentificationDocument();
                switch(model){
                    case "dependency":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal2                  = 1;
                                this.dependency.titleModal   = 'Registrar Dependencia';
                                this.dependency.description  = '';
                                this.dependency.abbreviation = '';
                                break;
                            }
                        }
                    }
                }
            },
            closeModalDependency(){
                this.modal2                            = 0;
                this.dependency.titleModal             = '';
                this.dependency.description            = '';
                this.dependency.abbreviation           = '';
                this.dependency.list_entity_id         = 5;
                this.dependency.dependency_type_id     = 1;
                this.dependency.dependency_id          = 0;
                this.dependency.arrayDependencies      = [];
                this.dependency.errors                 = [];
                this.person.firstName                  = '';
                this.person.lastName                   = '';
                this.person.dni                        = '';
                this.person.phone                      = '';
                this.person.email                      = '';
                this.person.direction                  = '';
                this.person.identification_document_id = 1;
                this.person.dependency_citizen_id      = 0;
                this.person.person_id                  = 0;
                this.query                             = '';
                this.destination                       = false;
                this.errors                            = [];
                this.users                             = []
            },
            getIdentificationDocument()
            {
                let me=this;
                var url= this.origin_data.route + '/identificationDocument';
                axios.get(url).then(function (response) {
                    me.person.arrayIdentificationDocument = response.data.identifications;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        },
        mounted() {
            this.entities = [];
            this.entity.selectedEntity          = {id: 0, label: 'Elije una entidad'};
            this.origin_data.selectedDependency = {id: 0, label: 'No hay opciones que mostrar'};
            this.origin_data.dependencyEntity   = [];
            this.origin_data.dependency_charge  = {};
            this.origin_data.selectedUser       = {}
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
    .show-dependency{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
</style>
