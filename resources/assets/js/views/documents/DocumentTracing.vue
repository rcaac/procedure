<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div id="ui-view">
                <div class="card">
                    <div class="card-header">
                        <h6><i class="icon-doc"/> SEGUIMIENTO DE TRÁMITE</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                v-model="search"
                                                class="form-control form-control-sm"
                                                placeholder="N° de Expediente"
                                                @keyup.enter="getTracing(search)"
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm"
                                                @click="getTracing(search)"
                                            >
                                                <i class="fa fa-search"/> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header text-white bg-primary">REFERENCIA DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                            <tr>
                                                <th><small><b>EXPEDIENTE</b></small></th>
                                                <th><small><b>REGISTRO</b></small></th>
                                                <th><small><b>DEPENDENCIA QUE EMITE</b></small></th>
                                                <th><small><b>DEPENDENCIA QUE RECEPCIONA</b></small></th>
                                                <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                                                <th><small><b>ASUNTO</b></small></th>
                                                <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                                                <th><small><b>FOLIOS</b></small></th>
                                                <th><small><b>OBSERVACIONES</b></small></th>
                                                <th><small><b>FECHA DE ENVÍO</b></small></th>
                                                <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                                                <th><small><b>TRÁMITE</b></small></th>
                                                <th><small><b>FECHA DE TRÁMITE</b></small></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(tracing , i) in arrayTracing" :key="`${i}-${tracing.id}`">
                                                <td v-text="tracing.records"/>
                                                <td>
                                                    {{ tracing.registry }}<br>
                                                    <small><b>Referencia:</b> {{ tracing.reference }}</small>
                                                </td>
                                                <td>
                                                    <div v-if="tracing.Dependency === 'USUARIO EXTERNO'">
                                                        <small><b>Usuario:</b> {{ tracing.fullNameT }}<br></small>
                                                        <small><b>Dni:</b> {{ tracing.dniT }}</small>
                                                    </div>
                                                    <div v-else>
                                                        <small>{{ tracing.Dependency }}<br></small>
                                                        <small><b>Entidad:</b> {{ tracing.entityDependency }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div v-if="tracing.Destination === 'USUARIO EXTERNO'">
                                                        <small><b>Usuario:</b> {{ tracing.fullNameR }}<br></small>
                                                        <small><b>Dni:</b> {{ tracing.dniR }}</small>
                                                    </div>
                                                    <div v-else>
                                                        <small>{{ tracing.Destination }}<br></small>
                                                        <small><b>Entidad:</b> {{ tracing.entityDestination }}</small>
                                                    </div>
                                                </td>
                                                <td><small>{{ tracing.code }}</small></td>
                                                <td v-text="tracing.affair"/>
                                                <td><small>{{ tracing.provided }}</small></td>
                                                <td v-text="tracing.folio"/>
                                                <td><small>{{ tracing.observations }}</small></td>
                                                <td v-text="tracing.shipping_date"/>
                                                <td v-text="tracing.reception_date"/>
                                                <td v-if="tracing.state ===1"><span  class="badge badge-pill badge-success">Pendiente</span></td>
                                                <td v-if="tracing.state ===2"><span  class="badge badge-pill badge-danger">Para Tramitar</span></td>
                                                <td v-if="tracing.state ===3"><span  class="badge badge-pill badge-warning">Derivado</span></td>
                                                <td v-if="tracing.state ===4"><span  class="badge badge-pill badge-primary">Archivado</span></td>
                                                <td v-if="tracing.state ===6"><span  class="badge badge-pill badge-dark">Finalizado</span></td>
                                                <td v-text="tracing.procedure_date"/>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">REQUISITOS</div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                <tr>
                                                    <th><small><b>REQUISITOS</b></small></th>
                                                    <th><small><b>COSTO(S/.)</b></small></th>
                                                    <th><small><b>OBSERVACIONES</b></small></th>
                                                    <th><small><b>ESTADO</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="requirement in arrayRequirements" :key="requirement.id">
                                                    <td v-text="requirement.description"/>
                                                    <td v-text="requirement.cost"/>
                                                    <td v-text="requirement.observation"/>
                                                    <td v-if="requirement.state === 1">
                                                        <input type="checkbox" checked disabled >
                                                    </td>
                                                    <td v-if="requirement.state === 2">
                                                        <input type="checkbox" disabled >
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data () {
            return {
                arrayTracing: [],
                arrayRequirements: [],
                offset : 3,
                search: '',
                id: 0,
                state: '',
            }
        },
        methods : {
            getTracing(search) {
                axios.get(`${this.ruta}/tracing?search=${search}`)
                    .then((response) => {
                        this.arrayTracing = response.data.tracing;
                        this.arrayRequirements = response.data.requirements;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        }
    }
</script>
