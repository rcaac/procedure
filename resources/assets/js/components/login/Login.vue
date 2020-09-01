<template>
    <div class="container">
        <router-view :ruta="ruta"/>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h1>Iniciar Sesión</h1>
                        <p class="text-muted">Sistema de Trámite Documentario</p>
                        <div class="input-group mb-3">
                            <select v-model="entity_id"  class="form-control">
                                <option value="0" disabled>Elija Entidad</option>
                                <option
                                        v-for="entities in arrayEntities"
                                        :key="entities.id"
                                        :value="entities.id"
                                        v-text="entities.description"
                                >
                                </option>
                            </select>
                            <div v-if="error && error.entity_id" class="text-danger-group">{{ error.entity_id[0] }}</div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <i class="icon-user"/>
                            </span>
                            </div>
                            <input type="text" v-model="user" class="form-control" placeholder="Usuario" id="internal">
                            <div v-if="error && error.user" class="text-danger-group">{{ error.user[0] }}</div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                            <i class="icon-lock"/>
                            </span>
                            </div>
                            <input type="password" v-model="password" class="form-control" placeholder="Contraseña" id="password_internal">
                            <div v-if="error && error.password" class="text-danger-group">{{ error.password[0] }}</div>
                            <div v-if="validate" class="text-danger-group">{{ validate }}</div>
                        </div>
                        <div class="input-group mb-4">
                            <select v-model="dependency_id" class="form-control">
                                <option value="0" disabled>Elija Dependencia</option>
                                <option
                                        v-for="dependencies in arrayDependencies"
                                        :key="dependencies.id"
                                        :value="dependencies.id"
                                        v-text="dependencies.description"
                                >
                                </option>
                            </select>
                            <div v-if="error && error.dependency_id" class="text-danger-group">{{ error.dependency_id[0] }}</div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block" @click="login()">Acceder</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-block btn-link" @click="process()">Tramitar</button>
                            </div>
                            <div class="col-md-8 col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-block btn-link" @click="tracing()">Seguimiento al Documento</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                entity_id: 5,
                dependency_id: 0,
                arrayEntities: [],
                arrayDependencies: [],
                user: '',
                password: '',
                error: '',
                validate: ''
            }
        },
        props: ['ruta'],
        methods: {
            selectEntity() {
                let me=this;
                var url=`${this.ruta}/selectedEntity`;
                axios.get(url).then((response) => {
                    var res          = response.data;
                    me.arrayEntities = res.entities;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            login() {
                let me = this;

                let user = {
                    'user':          this.user,
                    'password':      this.password,
                    'entity_id':     this.entity_id,
                    'dependency_id': this.dependency_id
                };
                var url=`${this.ruta}/login`;
                axios.post(url,user).then((response) => {
                    if (response.data.dependency_id == 46)
                    {
                        location.href = '/externo/enviados';
                    }
                    else 
                    {
                        location.href = '/pendientes';
                    }                                       
                },function (error) {
                    me.error    = error.response.data.errors;
                    me.validate = error.response.data.validates
                });

            },
            tracing() {
                location.href = '/tramite/seguimiento';
            },
            process() {
                location.href = '/tramite';
            }
        },
        watch: {
            user(search) {
                var url = `${this.ruta}/dependencySearch?search=${search}&id=${this.entity_id}`;
                axios.get(url).then((response) => {
                        this.arrayDependencies = response.data.dependencies;
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        },
        mounted() {
            this.selectEntity()
        }
    }
</script>
