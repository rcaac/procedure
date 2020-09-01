<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h6><i class="icon-doc"/> DOCUMENTOS ENVIADOS</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row mt-sm-0">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="getSent(search)"
                                    class="form-control form-control-sm"
                                    placeholder="N° de Expediente"
                                >
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"/> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select
                                v-model="pending_change_id"
                                class="form-control form-control-sm"
                                @change="getSent(1)"
                            >
                                <option :value=0>Todos los documentos</option>
                                <option :value=1>INTERNO</option>
                                <option :value=2>EXTERNO</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select
                                v-model="document_change_id"
                                class="form-control form-control-sm"
                                @change="getSent(1)"
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
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th><small><b>EXPEDIENTE</b></small></th>
                            <th><small><b>REGISTRO</b></small></th>
                            <th><small><b>DEPENDENCIA RECEPTOR</b></small></th>
                            <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                            <th><small><b>ASUNTO</b></small></th>
                            <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                            <th><small><b>FOLIOS</b></small></th>
                            <th><small><b>PRIORIDAD</b></small></th>
                            <th><small><b>FECHA DE ENVÍO</b></small></th>
                            <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                            <th><small><b>ACCIONES</b></small></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(sent, i) in arraySent" :key="`${i}-${sent.id}`">
                            <td v-text="sent.records"/>
                            <td>
                                {{ sent.registry }}<br>
                                <small><b>Referencia:</b> {{ sent.reference }}</small>
                            </td>
                            <td>
                                <div v-if="sent.Destination === 'USUARIO EXTERNO'">
                                    <small><b>Usuario:</b> {{ sent.fullName }}<br></small>
                                    <small><b>Dni:</b> {{ sent.dni }}</small>
                                </div>
                                <div v-else>
                                    <small>{{ sent.Destination }}<br></small>
                                    <small><b>Entidad:</b> {{ sent.entity }}</small>
                                </div>

                            </td>
                            <td><small>{{ sent.code }}</small></td>
                            <td v-text="sent.affair"/>
                            <td><small>{{ sent.provided }}</small></td>
                            <td v-text="sent.folio"/>
                            <td><small>{{ sent.priority }}</small></td>
                            <td v-text="sent.shipping_date"/>
                            <td v-text="sent.reception_date"/>
                            <td v-if="!sent.reception_date">
                                <button type="button" class="btn btn-outline-info btn-sm" @click="showModalDocument(sent.id)">
                                    <i class="icon-pencil"/> Editar
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="printDocument(sent.id)">
                                    <i class="icon-printer"/> Imprimir
                                </button>
                                <!--<button type="button" class="btn btn-outline-pink btn-sm" @click="documentInvalid(sent.documentary_procedure_id, sent.id)">
                                    <i class="icon-close"/> Anular
                                </button>-->
                                <div v-if="sent.Destination === 'USUARIO EXTERNO'">
                                    <button type="button" class="btn btn-outline-dark btn-sm" @click="documentFinalize(sent.documentary_procedure_id, sent.id)">
                                        <i class="icon-check"/> Finalizar
                                    </button>
                                </div>
                            </td>
                            <td v-else>
                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="printDocument(sent.id)">
                                    <i class="icon-printer"/> Imprimir
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Anterior</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"/>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <component
            :is="modalType"
            :ruta="this.ruta"
            :id="this.document_id"
            :internalSent="this.showInternalSent"
            @getSent="getSent($event)"
            @closeModalDocument="closeModalDocument"
        />
    </main>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
    import ModalDocumentInternal from '../../components/modals/ModalDocumentInternal';

    import {
        Doc,
        Text,
        Paragraph,
        Heading,
        TextColor,
        Bold,
        Underline,
        Italic,
        Strike,
        TextAlign,
        TrailingNode,
        Table,
        TableHeader,
        TableCell,
        TableRow,
        History,
    } from 'element-tiptap'

    export default {
        components: {
            VueBootstrapTypeahead,
            vSelect,
            ModalDocumentInternal
        },
        props : ['ruta'],
        watch: {
            query(newQuery) {
                if (this.query.length > 0) {
                    axios.get(`${this.ruta}/documentProcedureSearchExternal?newQuery=${newQuery}&records=${this.records}`)
                        .then((response) => {
                            this.procedures              = response.data.procedures;
                            this.procedure_id            = response.data.procedure_id;
                            this.procedure_like_id       = response.data.procedure_like_id;
                            if (this.procedure_id === this.procedure_like_id ) {
                                this.selectedProcedures      = response.data.procedures[0];
                                this.requirement_document_id = response.data.requirement_document_id;
                            }
                        })
                }
            },
            selectedProcedures()
            {
                this.requirements = [];
                this.states       = [];
                let url = '';

                if (this.procedure_id === this.procedure_like_id) {
                    url = `${this.ruta}/documentProcedureQueryState/${this.requirement_document_id}`;
                }else {
                    url = `${this.ruta}/documentProcedureQuery/${this.selectedProcedures.id}`;
                }
                axios.get(url)
                    .then((response) => {
                        this.requirements = response.data.requirements;
                        if (this.procedure_id === this.procedure_like_id) {
                            this.requirements = response.data.requirement_states;
                            $.each(response.data.requirement_states, function (key, value) {
                                if (value.process_state_id === 1) {
                                    me.states.push(value.id)
                                }
                            })
                        }
                    });
            },
            selectedDependency() {
                if (this.selectedDependencyProvided.id !== 0)  {
                    axios.get(`${this.ruta}/changeDependencySent/${this.selectedDependencyProvided.id}`)
                        .then((response) => {
                        this.dependency_charge = response.data.dependency_charge;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
            }
        },
        data () {
            return {
                procedures: [],
                requirements: [],
                states: [],
                records: '',
                query: '',
                files: '',
                archive: '',
                validateTupa: '',
                arraySent: [],
                offset : 3,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                errors: '',
                modal: 0,
                modal2: 0,
                titleModal : '',
                typeAction : 0,
                typeAction2: 0,
                document_id: 0,
                type_reception_id: 0,
                arrayDocumentPriorities: [],
                document_type_id: 0,
                document_priority_id: 0,
                dependency_shipping_id: 0,
                requirement_document_id: 0,
                arrayDocumentTypes: [],
                dependencies: [],
                selectedDependencyProvided: {
                    id: 0,
                    label: ''
                },
                selectedProvided: {
                    id: 0,
                    label: ''
                },
                selectedDocumentType: {
                    id: 0,
                    label: ''
                },
                affair: '',
                detail: '',
                annexes: '',
                provided: [],
                folio: '',
                shipping_date: '',
                tupa: 0,
                tupa_original: 0,
                auth: 0,
                selectedProcedures: '',
                document_sent: 1,
                procedure_id: 0,
                procedure_like_id: 0,
                pending_change_id: 0,
                document_change_id: 0,
                search: '',
                responseRecords: 0,
                responseRegistry: 0,
                responseCode: 0,
                responseAffair: '',
                responseDescription: '',
                responseFullNameT: '',
                responseFullNameR: '',
                responseDependency: '',
                responseDestination: '',
                responseEntityDependency: '',
                responseEntityDestination: '',
                responseReference: '',
                created_at: '',
                created_by: '',
                person_reception: '',
                dependency_charge: '',
                extensions: [
                    new Doc(),
                    new Text(),
                    new Paragraph(),
                    new Heading({ level: 3 }),
                    new TextColor(),
                    new Bold(),
                    new Underline(),
                    new Italic(),
                    new Strike(),
                    new TextAlign(),
                    new TrailingNode(),
                    new Table({
                        resizable: true,
                    }),
                    new TableHeader(),
                    new TableCell(),
                    new TableRow(),
                    new History(),
                ],
                showModal: 0,
                showInternalSent: 1,
            }
        },
        computed: {
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
            selectedDependency() {
                return this.selectedDependencyProvided.id
            },
            modalType(){
                switch(this.showModal){
                    case 1: {
                        return 'modal-document-internal'
                    }
                }
            },
        },
        methods: {
            getSent(page)
            {
                let url = '';

                if (this.search !== '') {
                    url = `${this.ruta}/sent?page=${page}&search=${this.search}`;
                }else {
                    url = `${this.ruta}/sent?page=${page}&pending_change_id=${this.pending_change_id}&document_type_id=${this.document_change_id}`;
                }

                axios.get(url).then((response) => {
                    this.arraySent  = response.data.sent.data;
                    this.pagination = response.data.pagination;
                    this.auth       = response.data.auth;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDocumentToSent() {
                axios.get(`${this.ruta}/documentToSent`).then((response) => {
                    this.arrayDocumentTypes      = response.data.document_type;
                    this.dependencies            = response.data.sent_document_dependencies;
                    this.provided                = response.data.provided;
                    this.arrayDocumentPriorities = response.data.priorities;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            selectedQuery() {
                if (this.query !== '') {
                    this.affair = this.query;
                    this.closeModalProcedure()
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            updateDocumentSent() {
                axios.put(`${this.ruta}/sentDocument/update`,{
                    'affair'                   : this.affair,
                    'description'              : this.detail,
                    'annexes'                  : this.annexes,
                    'date'                     : this.created_at,
                    'document_origin_id'       : this.document_origin_id,
                    'document_priority_id'     : this.document_priority_id,
                    'document_type_id'         : this.selectedDocumentType.id,
                    'type'                     : this.selectedDocumentType.label,
                    'type_reception_id'        : this.type_reception_id,
                    'dependency_shipping_id'   : this.dependency_shipping_id,
                    'dependency_reception_id'  : this.selectedDependencyProvided.id,
                    'shipping'                 : this.shipping,
                    'folio'                    : this.folio,
                    'dependency_type_id'       : this.dependency_type_id,
                    'detail_document_type_id'  : this.selectedDocumentType.id,
                    'states'                   : this.states,
                    'requirements'             : this.requirements,
                    'provided_id'              : this.selectedProvided.id,
                    'tupa'                     : this.tupa_original,
                    'document_id'              : this.document_id,
                    'documentary_procedure_id' : this.documentary_procedure_id,
                    'records'                  : this.records,
                    'created_by'               : this.created_by,
                    'person_reception'         : this.person_reception,
                    'shipping_date'            : this.shipping_date
                }).then(() => {
                    this.closeModal();
                    this.getSent(1);
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.getSent(page);
            },
            showModalDocument(id) {
                this.showModal = 1;
                this.document_id = id;
            },
            closeModalDocument()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            documentInvalid(documentary_procedure_id,id) {
                Swal.fire({
                    title: '<h4>Motivo por el cual se anula el documento.</h4>',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Anular',
                    showLoaderOnConfirm: true,
                    preConfirm: (observation) => {
                        let me=this;
                        return axios.post(this.ruta + '/documentInvalid', {
                            'documentary_procedure_id' : documentary_procedure_id,
                            'id'                       : id,
                            'observation'              : observation
                        }).then(() => {
                            me.arrayRequirements  = [];
                            me.getSent(1)
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
                            'Anulado!',
                            'El documento ha sido anulado con éxito.',
                            'success'
                        )
                    }
                })
            },
            documentFinalize(documentary_procedure_id,id) {
                Swal.fire({
                    title: '<h4>Motivo por el cual se finaliza el documento.</h4>',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Finalizar',
                    showLoaderOnConfirm: true,
                    preConfirm: (observation) => {
                        return axios.post(`${this.ruta}/documentFinalize`, {
                            'documentary_procedure_id' : documentary_procedure_id,
                            'id'                       : id,
                            'observation'              : observation
                        }).then(() => {
                            this.arrayRequirements  = [];
                            this.getSent(1)
                        }, (error) => {
                            this.errors = error.response.data.errors;
                            Swal.showValidationMessage(
                                `${this.errors}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        Swal.fire(
                            'Finalizado!',
                            'El documento ha sido finalizado con éxito.',
                            'success'
                        )
                    }
                })
            },
            addDocumentSent() {
                axios.post(`${this.ruta}/addDocumentSent`,{
                    'dependency_shipping_id'   : this.dependency_shipping_id,
                    'dependency_reception_id'  : this.selectedDependencyProvided.id,
                    'provided_id'              : this.selectedProvided.id,
                    'document_id'              : this.document_id,
                    'documentary_procedure_id' : this.documentary_procedure_id,
                    'person_reception'         : this.dependency_charge,
                    'person_id'                : this.person_id
                }).then(() => {
                    this.closeModal();
                    this.getSent(1);
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            printDocument (id) {
                let me=this;
                window.open(`${me.ruta}/document/reportDocument/${id}`, '_blank');
            },
        },
        mounted() {
            this.getSent(1);
            this.getDocumentToSent();
        }
    }

</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-document-sent{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
    .show-document-generate{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
    .modal-lg {
        max-width: 80%;
    }
</style>
