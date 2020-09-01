<template>
    <div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="switch switch switch-3d switch-primary">
                    <input type="checkbox" class="switch-input" v-model="destination">
                    <span class="switch-label"/>
                    <span class="switch-handle"/>
                </label>
                &nbsp;Enviar a usuario
            </div>
        </div>
        <div class="form-group row">
            <div class="form-group col-sm-6">
                <label>Entidad:</label>
                <v-select
                    v-model="origin_data.selectedEntityProvided"
                    :options="origin_data.entities"
                    placeholder="Elija una Entidad"
                    :clearable="false"
                    class="small"
                    @input="$emit('getDependencies', origin_data.selectedEntityProvided.id)"
                />
            </div>
            <div class="form-group col-sm-6" v-if="destination === false">
                <label>Dependencia:</label>
                <v-select
                    v-model="origin_data.selectedDependencyProvided"
                    :options="origin_data.dependencies"
                    placeholder="Elija una Dependencia"
                    class="small"
                    :clearable="false"
                />
                <div v-if="origin_data.errors && origin_data.errors.dependency_id" class="text-danger">{{ origin_data.errors.dependency_id[0] }}</div>
            </div>
            <div class="form-group col-sm-6" v-if="destination === true">
                <label>Usuario:</label>
                <vue-bootstrap-typeahead
                    v-model="query"
                    :data="origin_data.users"
                    :serializer="item => item.dni"
                    @hit="origin_data.selectedCitizen = $event"
                    placeholder="Buscar por DNI"
                    size="sm"
                    ref="typeahead"
                />
            </div>
            <div class="form-group col-sm-6">
                <label>Proveído de Atención:</label>
                <v-select
                    v-model="origin_data.selectedProvided"
                    :options="origin_data.provided"
                    placeholder="Elija un Proveído"
                    class="small"
                    :clearable="false"
                />
                <div v-if="origin_data.errors && origin_data.errors.provided_id" class="text-danger">{{ origin_data.errors.provided_id[0] }}</div>
            </div>
            <div class="col-md-2">
                <label class="col-md-12 form-control-label">&nbsp;</label>
                <div class="input-group" >
                    <button type="submit" @click="setDocumentDestination()" class="btn btn-primary btn-sm">
                        <i class="icon-plus"/> Añadir
                    </button>
                </div>
            </div>
        </div>
        <div v-if="origin_data.errors && origin_data.errors.destinations" class="text-danger">{{ origin_data.errors.destinations[0] }}</div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Dependencia</th>
                        <th>Personal</th>
                        <th>Proveído</th>
                        <th>Cod. de Documento</th>
                        <th>Accciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(destination, index) in origin_data.destinations" :key="index">
                        <td v-text="destination.description"/>
                        <td v-text="destination.fullName"/>
                        <td v-text="origin_data.actions[index].provided"/>
                        <td v-text="origin_data.shipping[index].detail"/>
                        <td>
                            <button type="submit" @click="deleteProvided(index)" class="btn btn-danger btn-sm">
                                <i class="icon-trash"/> Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import vSelect from 'vue-select'
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'

    export default {
        components: {
            vSelect,VueBootstrapTypeahead
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            }
        },
        data(){
            return {
                dependency_shipping_id: 0,
                destination: false,
                query: '',
                dni: 0,
            }
        },
        computed:{
            selectedUser(){
                return this.origin_data.selectedUser
            },
            selectedCitizen(){
                return this.origin_data.selectedCitizen
            },
            dependencyEntity()
            {
                return this.origin_data.selectedDependencyEntity
            },
            dependencies()
            {
                return this.origin_data.dependencies
            }
        },
        methods: {
            setDocumentDestination()
            {
                axios.post(`${this.origin_data.route}/documentDestination`, {
                    'dependency_id'          : this.origin_data.selectedDependencyProvided.id,
                    'dependency_shipping_id' : this.origin_data.dependency_shipping_id,
                    'provided_id'            : this.origin_data.selectedProvided.id,
                    'document_type_id'       : this.origin_data.selectedDocumentType.id,
                    'type'                   : this.origin_data.selectedDocumentType.label,
                    'detail_document_type_id': this.origin_data.detail_document_type_id,
                    'destinations'           : this.origin_data.destinations,
                    'countDocument'          : this.origin_data.countDocument,
                    'dni'                    : this.dni,
                    'number'                 : this.origin_data.number
                }).then((response) => {
                    this.getCountDocument();
                    this.origin_data.destinations               = response.data.destinations;
                    this.origin_data.actions                    = response.data.provided;
                    this.origin_data.shipping                   = response.data.shipping;
                    this.origin_data.selectedDependencyProvided = {id: 0, label: 'Elija una dependencia'};
                    this.origin_data.selectedProvided           = {id: 0, label: 'Elija un proveído'};
                    this.errors                                 = [];
                    this.origin_data.errors                     = [];
                    if (this.destination === true)
                    {
                        this.$refs.typeahead.inputValue  = '';
                        this.origin_data.selectedCitizen = [];
                    }
                }, (error) => {
                    this.errors             = error.response.data.errors;
                    this.origin_data.errors = error.response.data.errors
                });
            },
            deleteProvided(id) {
                axios.patch(`${this.origin_data.route}/documentProvidedDelete/${id}`)
                    .then((response) => {
                        this.origin_data.destinations               = response.data.destinations;
                        this.actions                                = response.data.provided;
                        this.shipping                               = response.data.shipping;
                        this.origin_data.selectedDependencyProvided = {id: 0, label: 'Elija una dependencia'};
                        this.origin_data.selectedProvided           = {id: 0, label: 'Elija un proveído'};
                        if (this.destination === true)
                        {
                            this.query                                = '';
                            this.$refs.typeahead.inputValue           = '';
                            this.origin_data.selectedCitizen          = [];
                        }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            getCountDocument()
            {
                return this.origin_data.countDocument = this.origin_data.countDocument + 1;
            }
        },
        watch: {
            dependencies() {
                this.dependency_shipping_id = this.origin_data.selectedDependency.id;
            },
            selectedUser() {
                this.dependency_shipping_id  = this.origin_data.selectedUser.id;
            },
            dependencyEntity() {
                this.dependency_shipping_id = this.origin_data.selectedDependencyEntity.id;
            },
            query(newQuery) {
                axios.get(`${this.origin_data.route}/documentPersonQuery?newQuery=${newQuery}`)
                    .then((response) => {
                        this.origin_data.users = response.data.person_charge;
                        if (this.origin_data.users.length) {
                            this.origin_data.selectedDependencyProvided.id = this.origin_data.users[0].id;
                            this.dni                                       = this.origin_data.users[0].dni;
                        }
                    })
            }
        }
    }
</script>
