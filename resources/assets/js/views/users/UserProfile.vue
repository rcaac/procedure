<template>
    <main class="main">
        <ol></ol>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <i class="icon-user"></i> MI PERFIL
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Tipo de Documento</label>
                            <select v-model="user.identification_document_id" class="form-control form-control-sm">
                                <option value="0" disabled>Seleccione</option>
                                <option
                                    v-for="identificationDocument in arrayIdentificationDocument"
                                    :key="identificationDocument.id"
                                    :value="identificationDocument.id"
                                    v-text="identificationDocument.document"
                                >
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Número de Documento</label>
                            <input type="text" v-model="user.dni" class="form-control form-control-sm" disabled>
                            <div v-if="errors && errors.dni" class="text-danger">{{ errors.dni[0] }}</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nombres</label>
                            <input type="text" v-model="user.firstName" class="form-control form-control-sm" placeholder="Nombre de la persona">
                            <div v-if="errors && errors.firstName" class="text-danger">{{ errors.firstName[0] }}</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Apellidos</label>
                            <input type="text" v-model="user.lastName" class="form-control form-control-sm" placeholder="Apellidos de la persona">
                            <div v-if="errors && errors.lastName" class="text-danger">{{ errors.lastName[0] }}</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Teléfono</label>
                            <input type="email" v-model="user.phone" class="form-control form-control-sm" placeholder="Teléfono">
                            <div v-if="errors && errors.phone" class="text-danger">{{ errors.phone[0] }}</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" v-model="user.email" class="form-control form-control-sm" placeholder="Email">
                            <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Dirección o Domicilio</label>&nbsp;&nbsp;
                            <input type="text" v-model="user.direction" class="form-control form-control-sm" placeholder="Escriba su dirección">
                            <div v-if="errors && errors.direction" class="text-danger">{{ errors.direction[0] }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-form-label">Cambiar Contraseña</label>&nbsp;&nbsp;
                            <input type="checkbox" v-model="changePassword" :true-value="1" :false-value="0">
                        </div>
                    </div>
                    <div class="row" v-if="changePassword === 1">
                        <div class="form-group col-md-6">
                            <label>Nueva Contraseña</label>
                            <input type="password" v-model="user.password" class="form-control form-control-sm" placeholder="Contraseña">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <button type="submit" @click="editUser()" class="btn btn-primary">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data() {
            return {
                changePassword: 0,
                user: [],
                arrayIdentificationDocument: [],
                errors: []
            }
        },
        methods: {
            getAuthUser()
            {
                let me = this;
                var url = this.ruta + '/authUser';
                axios.get(url).then(function (response) {
                    me.user = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getIdentificationDocument()
            {
                let me=this;
                var url= this.ruta + '/identificationDocument';
                axios.get(url).then(function (response) {
                    var res                          = response.data;
                    me.arrayIdentificationDocument   = res.identifications;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            editUser()
            {
                var url= this.ruta + '/userAuthUpdate';
                let me=this;
                axios.put(url, {
                    'firstName'                 : this.user.firstName,
                    'lastName'                  : this.user.lastName,
                    'dni'                       : this.user.dni,
                    'phone'                     : this.user.phone,
                    'email'                     : this.user.email,
                    'id'                        : this.user.id,
                    'direction'                 : this.user.direction,
                    'identification_document_id': this.user.identification_document_id,
                    'password'                  : this.user.password,
                })
                .then(response => {
                    Swal.fire(
                        'Actualizado!',
                        'El usuario ha sido actualizado con éxito.',
                        'success'
                    )
                }, function (error) {
                    var res   = error.response.data;
                    me.errors = res.errors;
                });
            }
        },
        mounted() {
            this.getIdentificationDocument();
            this.getAuthUser();
        }
    }
</script>
