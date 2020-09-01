<template>
    <div>
        <br>
        <div>
            <div class="form-group row">
                <label class="col-md-2 form-control-label">Persona:</label>
                <div class="col-md-6">
                    <vue-bootstrap-typeahead
                        v-model="query"
                        :data="users"
                        :serializer="item => item.dni"
                        @hit="origin_data.selectedUser = $event"
                        placeholder="Buscar por DNI"
                        size="sm"
                        ref="typeahead"
                    />
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <button
                            class="btn btn-primary btn-sm"
                            @click="openModal('person','register')"
                            style="color: white"
                        >
                            <i class="icon-plus"/>
                            Persona
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group row" v-if="Object.keys(origin_data.selectedUser).length">
                <label class="col-md-2 form-control-label">Entidad:</label>
                <div class="col-md-6">
                    <input type="text" v-model="origin_data.selectedUser.entity" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row" v-if="Object.keys(origin_data.selectedUser).length">
                <label class="col-md-2 form-control-label">Usuario:</label>
                <div class="col-md-6">
                    <input type="text" v-model="origin_data.selectedUser.fullName" class="form-control form-control-sm">
                </div>
            </div>
        </div>
        <!--Inicio del modal-->
        <div class="modal fade" tabindex="-1" :class="{'show-document-origin-citizen' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
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
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="col-md-12 col-form-label">Tipo de Documento de Identidad</label>
                                    <div class="col-md-12">
                                        <select v-model="identification_document_id" class="form-control form-control-sm">
                                            <option value="0" disabled>Seleccione</option>
                                            <option
                                                v-for="identificationDocument in arrayIdentificationDocument"
                                                :key="identificationDocument.id"
                                                :value="identificationDocument.id"
                                                v-text="identificationDocument.document"
                                            >
                                            </option>
                                        </select>
                                        <div v-if="errors && errors.identification_document_id" class="text-danger">{{ errors.identification_document_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-12 col-form-label">Número de Documento de Identidad</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="dni" class="form-control form-control-sm" placeholder="Documento">
                                        <div v-if="errors && errors.dni" class="text-danger">{{ errors.dni[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 col-form-label">Nombres</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="firstName" class="form-control form-control-sm" placeholder="Nombre de la persona">
                                        <div v-if="errors && errors.firstName" class="text-danger">{{ errors.firstName[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 col-form-label">Apellidos</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="lastName" class="form-control form-control-sm" placeholder="Apellidos de la persona">
                                        <div v-if="errors && errors.lastName" class="text-danger">{{ errors.lastName[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 col-form-label">Teléfono</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="phone" class="form-control form-control-sm" placeholder="Teléfono">
                                        <div v-if="errors && errors.phone" class="text-danger">{{ errors.phone[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 col-form-label">Email</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="email" class="form-control form-control-sm" placeholder="Email">
                                        <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 col-form-label">Dirección</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="direction" class="form-control form-control-sm" placeholder="Dirección de domicilio u oficina">
                                        <div v-if="errors && errors.direction" class="text-danger">{{ errors.direction[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" @click="registerPerson()" style="color: white">
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
    export default {
        components: {
            VueBootstrapTypeahead
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            },
        },
        data(){
            return {
                query: '',
                users: [],
                modal : 0,
                titleModal : '',
                typeAction : 0,
                identification_document_id: 0,
                dependency_citizen_id: 0,
                arrayIdentificationDocument: [],
                errors: [],
                firstName: '',
                lastName: '',
                dni: '',
                phone: '',
                email: '',
                direction: '',
            }
        },
        watch: {
            query(newQuery) {
                var url = this.origin_data.route + '/documentPersonQuery?newQuery=' + newQuery;
                axios.get(url)
                .then((res) => {
                    this.users = res.data.person_charge;
                })
            }
        },
        methods: {
            getIdentificationDocument()
            {
                let me=this;
                var url= this.origin_data.route + '/identificationDocument';
                axios.get(url).then(function (response) {
                    me.arrayIdentificationDocument = response.data.identifications;
                    me.dependency_citizen_id       = response.data.dependency_id;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectUser(dni) {
                var url = this.origin_data.route + '/documentPersonQuery?newQuery=' + dni;
                axios.get(url)
                    .then((res) => {
                        this.origin_data.selectedUser = res.data.person_charge[0];
                    })
            },
            registerPerson(){
                let me = this;
                axios.post(this.origin_data.route + '/userExternal/register',{
                    'firstName'                 : this.firstName,
                    'lastName'                  : this.lastName,
                    'dni'                       : this.dni,
                    'phone'                     : this.phone,
                    'email'                     : this.email,
                    'direction'                 : this.direction,
                    'identification_document_id': this.identification_document_id,
                    'dependency_citizen_id'     : this.dependency_citizen_id
                }).then((response) => {
                    me.$refs.typeahead.inputValue = response.data.dni;
                    me.selectUser(response.data.dni);
                    me.closeModal();
                },function (error) {
                    me.errors               = error.response.data.errors;
                    me.activity_user.errors = error.response.data.errors
                });
            },
            openModal(model, action){
                this.getIdentificationDocument();
                switch(model){
                    case "person":
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal                      = 1;
                                this.titleModal                 = 'Registrar Usuario';
                                this.firstName                  = '';
                                this.lastName                   = '';
                                this.dni                        = '';
                                this.phone                      = '';
                                this.email                      = '';
                                this.role_id                    = 0;
                                this.typeAction                 = 1;
                                this.identification_document_id = 1;
                                this.dependency_id              = 0;
                                break;
                            }
                        }
                    }
                }
            },
            closeModal(){
                this.modal                          = 0;
                this.titleModal                     = '';
                this.firstName                      = '';
                this.lastName                       = '';
                this.dni                            = '';
                this.user                           = '';
                this.phone                          = '';
                this.email                          = '';
                this.person_id                      = 0;
                this.typeAction                     = 0;
                this.errors                         = [];
                this.direction                      = '';
            },
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-document-origin-citizen{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
</style>
