<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div id="ui-view">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-doc"/>SEGUIMIENTO DE TRÁMITE
                        <button
                            v-if="this.arrayTracing.length"
                            class="btn btn-sm btn-primary float-right"
                            @click="printPdf(record)"
                        >
                        <i class="fa fa-file-pdf-o"/>
                            PDF
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                v-model="record"
                                                class="form-control form-control-sm"
                                                placeholder="N° de Expediente"
                                                @keyup.enter="getTracing(record)"
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm"
                                                @click="getTracing(record)"
                                            >
                                                <i class="fa fa-record"/> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="this.details">
                                    <div>El documento con expediente <b>{{this.details.records}}</b></div>
                                    <div v-if="this.details.state === 1">Se encuentra <span  class="badge badge-pill badge-success">Pendiente</span></div>
                                    <div v-if="this.details.state === 2">Se encuentra <span  class="badge badge-pill badge-danger">Para Tramitar</span></div>
                                    <div v-if="this.details.state === 3">Se encuentra <span  class="badge badge-pill badge-warning">Derivado</span></div>
                                    <div v-if="this.details.state === 4">Se encuentra <span  class="badge badge-pill badge-primary">Archivado</span></div>
                                    <div v-if="this.details.state === 6">Se encuentra <span  class="badge badge-pill badge-dark">Finalizado</span></div>
                                    <div v-if="this.details.state === 6" class="mb-3">
                                        En la oficina de <b>{{this.details.Dependency}}</b>
                                    </div>
                                    <div v-else class="mb-3">
                                        En la oficina de <b>{{this.details.Destination}}</b>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header text-white bg-primary">REFERENCIA DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                <tr>
                                                    <th><small><b>DEPENDENCIA EMISOR</b></small></th>
                                                    <th><small><b>DEPENDENCIA RECEPTOR</b></small></th>
                                                    <th><small><b>ESTADO</b></small></th>
                                                    <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                                                    <th><small><b>ASUNTO</b></small></th>
                                                    <th><small><b>FOLIOS</b></small></th>
                                                    <th><small><b>OBSERVACIONES</b></small></th>
                                                    <th><small><b>FECHA DE ENVÍO</b></small></th>
                                                    <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                                                    <th><small><b>FECHA DE TRÁMITE</b></small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(tracing , i) in arrayTracing" :key="`${i}-${tracing.id}`">
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
                                                    <td v-if="tracing.state ===1"><span  class="badge badge-pill badge-success">Pendiente</span></td>
                                                    <td v-if="tracing.state ===2"><span  class="badge badge-pill badge-danger">Para Tramitar</span></td>
                                                    <td v-if="tracing.state ===3"><span  class="badge badge-pill badge-warning">Derivado</span></td>
                                                    <td v-if="tracing.state ===4"><span  class="badge badge-pill badge-primary">Archivado</span></td>
                                                    <td v-if="tracing.state ===6"><span  class="badge badge-pill badge-dark">Finalizado</span></td>
                                                    <td><small>{{ tracing.code }}</small></td>
                                                    <td v-text="tracing.affair"/>
                                                    <td v-text="tracing.folio"/>
                                                    <td><small>{{ tracing.observations }}</small></td>
                                                    <td v-text="tracing.shipping_date"/>
                                                    <td v-text="tracing.reception_date"/>
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
                record: '',
                id: 0,
                state: '',
                details: ''
            }
        },
        methods : {
            getTracing(record) {
                let me=this;
                var url= this.ruta + '/monitoring?record=' + record;
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayTracing = res.tracing;
                    me.arrayRequirements = res.requirements;
                    me.details = res.details;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            printPdf(id) {
                let me=this;
                window.open(`${me.ruta}/document/reportTracingExternal/${id}`, '_blank')
            }
        }
    }
</script>

