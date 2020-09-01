<template>
        <main class="main">
        <ol></ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-users"></i> USUARIOS&nbsp;&nbsp;
                    <button type="button" @click="openModal('person','register')" class="btn btn-primary btn-sm" style="color: white">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listPerson(1,search)" class="form-control form-control-sm" placeholder="Texto a buscar">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>NOMBRES</th>
                                <th>APELLIDOS</th>
                                <th>DNI</th>
                                <th>TELÉFONO</th>
                                <th>EMAIL</th>
                                <th>DIRECCIÓN</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="person in arrayPerson" :key="person.id">

                                <td v-text="person.firstName"></td>
                                <td v-text="person.lastName"></td>
                                <td v-text="person.dni"></td>
                                <td v-text="person.phone"></td>
                                <td v-text="person.email"></td>
                                <td v-text="person.direction"></td>
                                <td>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('person','update',person)">
                                            <i class="icon-pencil"></i> Editar
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-success btn-sm" @click="openModal('person','assign',person); listChargeAssignment(person.id);">
                                            <i class="icon-check"></i> Asignar
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteUser(person.id)">
                                            <i class="icon-trash"></i> Eliminar
                                        </button>
                                    </div>
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
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'show' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" :class="{'modal-lg-charge': typeAction===3}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                          <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="row">
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
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
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-12 col-form-label">Número de Documento de Identidad</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="dni" class="form-control form-control-sm" placeholder="Documento">
                                        <div v-if="errors && errors.dni" class="text-danger">{{ errors.dni[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-4 col-form-label">Nombres</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="firstName" class="form-control form-control-sm" placeholder="Nombre de la persona">
                                        <div v-if="errors && errors.firstName" class="text-danger">{{ errors.firstName[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-4 col-form-label">Apellidos</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="lastName" class="form-control form-control-sm" placeholder="Apellidos de la persona">
                                        <div v-if="errors && errors.lastName" class="text-danger">{{ errors.lastName[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-4 col-form-label">Teléfono</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="phone" class="form-control form-control-sm" placeholder="Teléfono">
                                        <div v-if="errors && errors.phone" class="text-danger">{{ errors.phone[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-4 col-form-label">Email</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="email" class="form-control form-control-sm" placeholder="Email">
                                        <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===1 || typeAction===2">
                                    <label class="col-md-4 col-form-label">Dirección</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="direction" class="form-control form-control-sm" placeholder="Dirección de domicilio u oficina">
                                        <div v-if="errors && errors.direction" class="text-danger">{{ errors.direction[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="typeAction===2">
                                    <label class="col-md-4 col-form-label">Contraseña</label>
                                    <div class="col-md-12">
                                        <input type="password" v-model="password" class="form-control form-control-sm" placeholder="Contraseña">
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="typeAction===3">
                                <div class="form-group col-sm-12">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" v-model="type" id="radio1" :value="1"> Dependencias Internas
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" v-model="type" id="radio2" :value="2"> Usuario
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" v-model="type" id="radio3" :value="3"> Entidades Externas
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <component v-if="typeAction===3"
                                :is="activityUser"
                                :activity_user="activity_user"
                            ></component>
                            <div v-if="typeAction===3">
                                <hr>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                        <tr>
                                            <th>ENTIDAD</th>
                                            <th>DEPENDENCIA</th>
                                            <th>CARGO</th>
                                            <th>SUSTENTO</th>
                                            <th>FECHA INICIAL</th>
                                            <th>FECHA FINAL</th>
                                            <th>ESTADO</th>
                                            <th>ROL</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <user-charge
                                                v-for="user_charge in arrayChargeAssignment"
                                                :key="user_charge.id"
                                                :charge="user_charge"
                                                @edit="updateCharge(user_charge)"
                                                @remove="deleteCharge(user_charge.charge_id)"
                                                :arrayDependency="arrayDependency"
                                                :arrayState="arrayState"
                                                :arrayRole="arrayRole"
                                                :errors="errors"
                                                :date="date"
                                            />
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary btn-sm" @click="registerPerson()" style="color: white">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2" class="btn btn-primary btn-sm" @click="updatePerson()" style="color: white">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                        <button type="button" v-if="typeAction === 3" class="btn btn-primary btn-sm" @click="registerCharge()" style="color: white">
                            <i class="icon-check"></i> Asignar
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
    import UserInternal from '../../components/users/UserInternal'
    import UserExternal from '../../components/users/UserExternal'
    import UserCharge from '../../components/users/UserCharge'
    import Citizen from '../../components/users/Citizen'
    export default {
        components: {UserInternal, UserExternal, UserCharge, Citizen},
        props : ['ruta'],
        data (){
            return {
                person_id: 0,
                identification_document_id: 0,
                charge_id: 0,
                detail_id: 0,
                firstName: '',
                lastName: '',
                dni: '',
                phone: '',
                email: '',
                direction: '',
                user: '',
                activity_user: {
                    arrayDependencyInternal: [],
                    arrayDependencyExternal: [],
                    arrayDependencyCitizen: [],
                    arrayDependencies: [],
                    arrayEntity: [],
                    arrayState: [],
                    arrayRoleInternal : [],
                    arrayRoleExternal : [],
                    arrayRoleCitizen : [],
                    role_internal_id: 3,
                    role_external_id: 4,
                    role_citizen_id: 4,
                    detail: '',
                    startDate: '',
                    finalDate: '',
                    charge: '',
                    dependency_id: 0,
                    charge_state_id: 1,
                    route: this.ruta,
                    errors: [],
                    validate: '',
                    selectedDependencyInternal: {'id': 0, 'description': 'Elije Dependencia'},
                    selectedDependencyExternal: {'id': 0, 'description': 'Elije Dependencia'},
                    selectedDependencyCitizen: {'id': 0, 'description': ''},
                    selectedEntity: {'id': 0, 'description': 'Elije Entidad'}
                },
                type: 1,
                arrayDependency: [],
                arrayRole: [],
                arrayRoleInternal : [],
                dependency_id: 0,
                user_id: 0,
                arrayPerson : [],
                arrayState : [],
                charge_state_id: 0,
                arrayIdentificationDocument: [],
                arrayPersonCharge: [],
                arrayChargeAssignment: [],
                modal : 0,
                description: '',
                titleModal : '',
                typeAction : 0,
                errorPerson : 0,
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
                editable: false,
                arrayDependencyCitizen: [],
                date: ''
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
            },
            activityUser(){
                switch(this.type){
                    case 1: {
                        return 'user-internal'
                    }
                    case 2: {
                        return 'citizen'
                    }
                    case 3: {
                        return 'user-external'
                    }
                }
            },

        },
        watch: {
            arrayDependencyCitizen() {
                    this.activity_user.selectedDependencyCitizen.id = this.arrayDependencyCitizen[0]['id'];
                    this.activity_user.selectedDependencyCitizen.description = this.arrayDependencyCitizen[0]['description']
            }
        },
        methods : {
            listPerson (page,search){
                let me=this;
                var url= this.ruta + '/user?page=' + page + '&search='+ search;
                axios.get(url).then(function (response) {
                    me.arrayPerson                           = response.data.persons.data;
                    me.arrayIdentificationDocument           = response.data.documents;
                    me.activity_user.arrayEntity             = response.data.entities;
                    me.activity_user.arrayDependencyInternal = response.data.dependency_internal;
                    me.activity_user.arrayDependencyCitizen  = response.data.dependency_citizen;
                    me.arrayDependencyCitizen                = response.data.dependency_citizen;
                    me.arrayDependency                       = response.data.dependencies;
                    me.activity_user.arrayState              = response.data.state;
                    me.arrayState                            = response.data.state;
                    me.pagination                            = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectRole(){
                let me=this;
                var url= this.ruta + '/role/selectRole';
                axios.get(url).then(function (response) {
                    me.activity_user.arrayRoleInternal = response.data.role_internal;
                    me.activity_user.arrayRoleExternal = response.data.role_external;
                    me.activity_user.arrayRoleCitizen  = response.data.role_citizen;
                    me.arrayRoleInternal               = response.data.role_internal;
                    me.arrayRole                       = response.data.role;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search,criterion){
                let me                     = this;
                me.pagination.current_page = page;
                me.listPerson(page,search,criterion);
            },
            registerPerson(){

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                let me = this;
                axios.post(this.ruta + '/user/register',{
                    'firstName'                 : this.firstName,
                    'lastName'                  : this.lastName,
                    'dni'                       : this.dni,
                    'phone'                     : this.phone,
                    'email'                     : this.email,
                    'direction'                 : this.direction,
                    'identification_document_id': this.identification_document_id,
                }).then(() => {
                    me.closeModal();
                    me.listPerson(1,'');
                },function (error) {
                    me.errors               = error.response.data.errors;
                    me.activity_user.errors = error.response.data.errors
                });
            },
            updatePerson(){
                let me = this;
                axios.put(this.ruta + '/user/update',{
                    'firstName'                 : this.firstName,
                    'lastName'                  : this.lastName,
                    'dni'                       : this.dni,
                    'phone'                     : this.phone,
                    'email'                     : this.email,
                    'role_id'                   : this.role_id,
                    'user_id'                   : this.user_id,
                    'password'                  : this.password,
                    'person_id'                 : this.person_id,
                    'identification_document_id': this.identification_document_id,
                    'direction'                 : this.direction,
                }).then(() => {
                    me.closeModal();
                    me.listPerson(1,'');
                }, function (error) {
                    var res   = error.response.data;
                    me.errors       = res.errors;
                });
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Esta seguro de eliminar este usurio?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.patch(this.ruta + '/user/' + id).then(() => {
                            me.listPerson(1,this.search);
                            Swal.fire(
                                'Eliminado!',
                                'El usuario ha sido eliminado con éxito.',
                                'success'
                            )
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                })
            },
            listChargeAssignment(id)
            {
                let me=this;
                var url= this.ruta + '/chargeAssignment/' + id;
                axios.post(url).then(function (response) {
                    var res                  = response.data;
                    me.arrayChargeAssignment = res.charge_assignment;
                    me.date = response.data.date
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registerCharge(){
                let me = this;
                axios.post(this.ruta + '/chargeUser',{
                    'person_id'                 : this.person_id,
                    'type'                      : this.type,
                    'change_entity_id'          : this.activity_user.selectedEntity.id,
                    'dependency_internal_id'    : this.activity_user.selectedDependencyInternal.id,
                    'dependency_external_id'    : this.activity_user.selectedDependencyExternal.id,
                    'dependency_citizen_id'     : this.activity_user.selectedDependencyCitizen.id,
                    'startDate'                 : this.activity_user.startDate,
                    'finalDate'                 : this.activity_user.finalDate,
                    'detail'                    : this.activity_user.detail,
                    'charge'                    : this.activity_user.charge,
                    'role_internal_id'          : this.activity_user.role_internal_id,
                    'role_external_id'          : this.activity_user.role_external_id,
                    'role_citizen_id'           : this.activity_user.role_citizen_id,
                    'charge_state_id'           : this.activity_user.charge_state_id
                }).then(() => {
                    me.listChargeAssignment(this.person_id);
                    me.activity_user.selectedDependencyInternal = {'id': 0, 'description': 'Elije Dependencia'};
                    me.activity_user.validate                   = '';
                    me.activity_user.startDate                  = '';
                    me.activity_user.finalDate                  = '';
                    me.activity_user.detail                     = '';
                    me.activity_user.charge                     = '';
                }, function (error) {
                    me.activity_user.errors = error.response.data.errors;
                    me.activity_user.validate = error.response.data.validates;
                });
            },
            updateCharge(data = [])
            {
                let me = this;
                axios.put(this.ruta + '/user/editCharge',{
                    'dependency_id'             : data['dependency_id'],
                    'startDate'                 : data['startDate'],
                    'finalDate'                 : data['finalDate'],
                    'detail'                    : data['detail'],
                    'detail_id'                 : data['detail_id'],
                    'person_id'                 : this.person_id,
                    'role_id'                   : data['role_id'],
                    'charge'                    : data['charge'],
                    'charge_id'                 : data['charge_id'],
                    'type'                      : data['type'],
                    'type_id'                   : data['type_id'],
                    'charge_state_id'           : data['charge_state_id']
                }).then(() => {
                    me.listChargeAssignment(this.person_id);
                }, function (error) {
                    me.errors = error.response.data.errors
                });
            },
            deleteCharge(id)
            {
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
                        axios.patch(this.ruta + '/charge/' + id).then(() => {
                            this.listChargeAssignment(this.person_id);
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
            closeModal(){
                this.modal                          = 0;
                this.titleModal                     = '';
                this.firstName                      = '';
                this.lastName                       = '';
                this.dni                            = '';
                this.user                           = '';
                this.phone                          = '';
                this.email                          = '';
                this.password                       = '';
                this.activity_user.role_id          = 3;
                this.person_id                      = 0;
                this.charge_id                      = 0;
                this.detail_id                      = 0;
                this.typeAction                     = 0;
                this.user_id                        = 0;
                this.activity_user.charge_state_id  = 1;
                this.identification_document_id     = 0;
                this.activity_user.selectedDependencyInternal = {'id': 0, 'description': 'Elije Dependencia'};
                this.activity_user.selectedDependencyExternal = {'id': 0, 'description': 'Elije Dependencia'};
                this.activity_user.selectedEntity             = {'id': 0, 'description': 'Elije Entidad'};
                this.activity_user.startDate        = '';
                this.activity_user.finalDate        = '';
                this.activity_user.detail           = '';
                this.activity_user.charge           = '';
                this.type                           = 1;
                this.activity_user.dependency_id    = 0;
                this.activity_user.change_entity_id = 0;
                this.errors                         = [];
                this.activity_user.errors           = [];
                this.direction                      = '';
                this.activity_user.validate         = ''
            },
            openModal(model, action, data = []){
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
                                this.change_entity_id           = 0;
                                this.startDate                  = '';
                                this.finalDate                  = '';
                                this.detail                     = '';
                                break;
                            }
                            case 'update':
                            {
                                this.modal                      = 1;
                                this.titleModal                 = 'Actualizar Usuario';
                                this.typeAction                 = 2;
                                this.person_id                  = data['id'];
                                this.firstName                  = data['firstName'];
                                this.lastName                   = data['lastName'];
                                this.dni                        = data['dni'];
                                this.phone                      = data['phone'];
                                this.email                      = data['email'];
                                this.user_id                    = data['user_id'];
                                this.password                   = data['password'];
                                this.direction                  = data['direction'];
                                this.identification_document_id = data['identification_document_id'];
                                break;
                            }
                            case 'assign':
                            {
                                this.modal                      = 1;
                                this.titleModal                 = 'Asignar Cargo';
                                this.typeAction                 = 3;
                                this.person_id                  = data['id'];
                                this.user_id                    = data['user_id'];
                                this.role_id                    = data['role_id'];
                                this.identification_document_id = data['identification_document_id'];
                                break;
                            }
                        }
                    }
                }
            },
        },
        mounted() {
            this.listPerson(1,this.search);
            this.selectRole();
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .modal-lg-charge {
        height: 90%;
        max-width: 99%;
    }
    .show{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
</style>
