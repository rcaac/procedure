<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h6><i class="icon-doc"/> ARCHIVO DE DOCUMENTOS</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="getArchive(search)"
                                    class="form-control form-control-sm"
                                    placeholder="N° de Expediente"
                                >
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"/>Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <select
                                    v-model="pending_change_id"
                                    class="form-control form-control-sm"
                                    @change="getArchive(1)"
                                >
                                    <option :value="0">Todos los documentos</option>
                                    <option :value="1">INTERNO</option>
                                    <option :value="2">ENTIDAD EXTERNA</option>
                                    <option :value="3">CIUDADANO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select
                                    v-model="document_change_id"
                                    class="form-control form-control-sm"
                                    @change="getArchive(1)"
                                >
                                    <option :value=0>Todos los tipos de documentos</option>
                                    <option
                                        v-for="types in arrayDocumentTypes"
                                        :key="types.id"
                                        :value="types.id"
                                        v-text="types.label">
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <select
                                    v-model="date"
                                    class="form-control form-control-sm"
                                    @change="getArchive(1)"
                                >
                                    <option :value=0>Todos los años</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <select
                                    v-model="procedure_state_id"
                                    class="form-control form-control-sm"
                                    @change="getArchive(1)"
                                >
                                    <option :value="4">ARCHIVADOS</option>
                                    <option :value="5">ANULADOS</option>
                                    <option :value="6">FINALIZADOS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th><small><b>EXPEDIENTE</b></small></th>
                            <th><small><b>REGISTRO</b></small></th>
                            <th><small><b>DEPENDENCIA EMISOR</b></small></th>
                            <th><small><b>DEPENDENCIA RECEPTOR</b></small></th>
                            <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                            <th><small><b>ASUNTO</b></small></th>
                            <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                            <th><small><b>FOLIOS</b></small></th>
                            <th><small><b>PRIORIDAD</b></small></th>
                            <th><small><b>OBSERVACIONES</b></small></th>
                            <th><small><b>FECHA DE ENVÍO</b></small></th>
                            <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                            <th><small><b>TRÁMITE</b></small></th>
                            <th><small><b>FECHA DE TRÁMITE</b></small></th>
                            <th><small><b>ACCIONES</b></small></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(archive, i) in arrayArchive" :key="`${i}-${archive.id}`">
                            <td v-text="archive.records"/>
                            <td>
                                {{ archive.registry }}<br>
                                <small><b>Referencia:</b> {{ archive.reference }}</small>
                            </td>
                            <td>
                                <div v-if="archive.Dependency === 'USUARIO EXTERNO'">
                                    <small><b>Usuario:</b> {{ archive.fullName }}<br></small>
                                    <small><b>Dni:</b> {{ archive.dniT }}</small>
                                </div>
                                <div v-else>
                                    <small>{{ archive.Dependency }}<br></small>
                                    <small><b>Entidad:</b> {{ archive.entityDependency }}</small>
                                </div>
                            </td>
                            <td>
                                <div v-if="archive.Destination === 'USUARIO EXTERNO'">
                                    <small><b>Usuario:</b> {{ archive.fullName }}<br></small>
                                    <small><b>Dni:</b> {{ archive.dniR }}</small>
                                </div>
                                <div v-else>
                                    <small>{{ archive.Destination }}<br></small>
                                    <small><b>Entidad:</b> {{ archive.entityDestination }}</small>
                                </div>
                            </td>
                            <td><small>{{ archive.code }}</small></td>
                            <td v-text="archive.affair"/>
                            <td><small>{{ archive.provided }}</small></td>
                            <td v-text="archive.folio"/>
                            <td><small>{{ archive.priority }}</small></td>
                            <td v-text="archive.observations"/>
                            <td v-text="archive.shipping_date"/>
                            <td v-text="archive.reception_date"/>
                            <td v-if="archive.state ===4"><span  class="badge badge-pill badge-primary">Archivado</span></td>
                            <td v-if="archive.state ===5"><span  class="badge badge-pill badge-pink">Anulado</span></td>
                            <td v-if="archive.state ===6"><span  class="badge badge-pill badge-dark">Finalizado</span></td>
                            <td v-text="archive.procedure_date"/>
                            <td>
                                <button type="button" class="btn btn-outline-info btn-sm" @click="unarchive(archive.documentary_procedure_id, archive.state)">
                                    <i class="fa fa-close"/> Deshacer
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
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data () {
            return {
                arrayArchive: [],
                offset : 3,
                search: '',
                pending_change_id: 0,
                document_change_id: 0,
                procedure_state_id:4,
                date: 0,
                arrayDocumentTypes: [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
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
            getArchive(page)
            {
                let me=this;
                if (this.search !== '')
                {
                    var url= this.ruta + '/records?page=' + page +'&search=' + this.search;
                }else {
                    var url= this.ruta + '/records?page=' + page
                        + '&pending_change_id=' + this.pending_change_id
                        + '&document_type_id=' + this.document_change_id
                        + '&date=' + this.date
                        + '&procedure_state_id=' + this.procedure_state_id;
                }
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayArchive = res.archive.data;
                    me.pagination  = res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page) {
                let me = this;
                me.pagination.current_page = page;
                me.getArchive(page);
            },
            getDocumentToSent()
            {
                var url = this.ruta + '/documentToSent';
                axios.get(url).then((response) => {
                    this.arrayDocumentTypes      = response.data.document_type;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            unarchive(id, state)
            {
                Swal.fire({
                    title: '<h4>Motivo por el cual se deshace el documento.</h4>',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Deshacer',
                    showLoaderOnConfirm: true,
                    preConfirm: (observation) => {
                        let me=this;
                        return axios.post(this.ruta + '/documentUnarchive', {
                            'id'                      : id,
                            'state'                   : state,
                            'observation'             : observation
                        }).then(() => {
                            me.getArchive(1)
                        }, function (error) {
                            me.errors = error.response.data.errors;
                            Swal.showValidationMessage(
                                `${me.errors}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        Swal.fire(
                            'Deshecho!',
                            'El documento se deshizo con éxito.',
                            'success'
                        )
                    }
                })
            }
        },
        mounted() {
            this.getArchive(1);
            this.getDocumentToSent()
        }
    }
</script>
