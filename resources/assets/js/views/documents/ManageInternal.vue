<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="icon-doc"/>DOCUMENTOS INTERNOS&nbsp;&nbsp;
                    <button type="button" class="btn btn-sm btn-info" @click="restart()"><i class="fa fa-eraser"/> Limpiar</button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    class="form-control form-control-sm"
                                    placeholder="N° de Registro"
                                    @keyup="getSent(1)"
                                >
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="fa fa-search"/>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select
                                v-model="pending_change_id"
                                class="form-control form-control-sm"
                                @change="getSent(1)"
                            >
                            <option :value="0">Todos los estados</option>
                            <option :value="1">PENDIENTES</option>
                            <option :value="2">PARA TRAMITAR</option>
                            <option :value="3">DERIVADO</option>
                            <option :value="4">ARCHIVADO</option>
                            <option :value="6">FINALIZADO</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <select
                                    v-model="date"
                                    class="form-control form-control-sm"
                                    @change="getSent(1)"
                                >
                                    <option :value="0">Todos los años</option>
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
                        <div class="col-sm-2">
                            <label class="switch switch-md switch-3d switch-primary">
                                <input
                                    type="checkbox"
                                    class="switch-input"
                                    v-model="transparency"
                                    @change="getSent(1)"
                                    :true-value="1"
                                    :false-value="0"
                                >
                                <span class="switch-label"/>
                                <span class="switch-handle"/>
                            </label>
                            &nbsp;Transparencia
                        </div>
                        <div class="form-input col-sm-2">
                            <input
                                type="date"
                                v-model="initial_date"
                                class="form-control form-control-sm"
                                @change="getSent(1)"
                            >
                        </div>
                        <div class="col-sm-2" v-if="this.initial_date !== ''">
                            <input
                                type="date"
                                v-model="final_date"
                                class="form-control form-control-sm"
                                @change="getSent(1)"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <v-select
                                v-model="selectedDependencyProvided"
                                :options="dependencies"
                                class="small"
                                :clearable="false"
                                @input="getSent(1)"
                            />
                        </div>
                        <div class="col-sm-2" v-if="selectedDependencyProvided.id !== 0">
                            <label class="switch switch-md switch-3d switch-primary">
                                <input
                                    type="checkbox"
                                    class="switch-input"
                                    v-model="typeDependency"
                                    @change="getSent(1)"
                                    :true-value="1"
                                    :false-value="0"
                                >
                                <span class="switch-label"/>
                                <span class="switch-handle"/>
                            </label>
                            &nbsp;Emisor
                        </div>
                        <div class="col-md-3">
                            <v-select
                                v-model="selectedDocumentType"
                                :options="arrayDocumentTypes"
                                class="small"
                                :clearable="false"
                                @input="getSent(1)"
                            />
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="content">
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
                                    <th><small><b>FECHA DE ENVÍO</b></small></th>
                                    <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                                    <th><small><b>TRÁMITE</b></small></th>
                                    <th><small><b>FECHA DE TRÁMITE</b></small></th>
                                    <th><small><b>ACCIONES</b></small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(tracing, i) in arrayTable" :key="`${i}-${tracing.id}`">
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
                                            <small><b>Usuario:</b> {{ tracing.fullNameT }}<br></small>
                                            <small><b>Dni:</b> {{ tracing.dniT }}<br></small>
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
                                            <small><b>Usuario:</b> {{ tracing.fullNameR }}<br></small>
                                            <small><b>Dni:</b> {{ tracing.dniR }}<br></small>
                                            <small><b>Entidad:</b> {{ tracing.entityDestination }}</small>
                                        </div>
                                    </td>
                                    <td><small>{{ tracing.code }}</small></td>
                                    <td><small>{{ tracing.affair }}</small></td>
                                    <td><small>{{ tracing.provided }}</small></td>
                                    <td v-text="tracing.folio"/>
                                    <td><small>{{ tracing.priority }}</small></td>
                                    <td v-text="tracing.shipping_date"/>
                                    <td v-text="tracing.reception_date"/>
                                    <td v-if="tracing.state ===1"><span  class="badge badge-pill badge-success">Pendiente</span></td>
                                    <td v-if="tracing.state ===2"/>
                                    <td v-if="tracing.state ===3"><span  class="badge badge-pill badge-warning">Derivado</span></td>
                                    <td v-if="tracing.state ===4"><span  class="badge badge-pill badge-primary">Archivado</span></td>
                                    <td v-if="tracing.state ===5"/>
                                    <td v-if="tracing.state ===6"><span  class="badge badge-pill badge-dark">Finalizado</span></td>
                                    <td>
                                        {{ tracing.procedure_date }}
                                    </td>
                                    <td>
                                        <div v-if="tracing.tupa === 1">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                @click=" modalShowRequirement(tracing.records)"
                                            >
                                                <i class="fa fa-eye"/> Requisitos
                                            </button>
                                        </div>
                                        <div v-if="tracing.annexes === '1'">
                                            <button
                                                type="button"
                                                class="btn btn-outline-warning btn-sm"
                                                @click="modalShowAnnexe(tracing.records)"
                                            >
                                                <i class="fa fa-eye"></i> Anexos
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            class="btn btn-outline-info btn-sm"
                                            @click="showModalDocument(tracing)"
                                        >
                                            <i class="icon-pencil"/> Editar
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            @click="printDocument(tracing.id)"
                                        >
                                            <i class="icon-printer"/> Imprimir
                                        </button>
                                        <div v-if="tracing.state ===1">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                @click="deleted(tracing.id, tracing.documentary_procedure_id)"
                                            >
                                                <i class="icon-trash"/> Eliminar
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
            :data="this.document"
            :id="this.document_id"
            :internalSent="this.showInternalSent"
            @getSent="getSent($event)"
            @closeModalDocument="closeModalDocument"
            @closeModalRequirement="closeModalRequirement"
            @closeModalAnnexe="closeModalAnnexe"
        />
    </main>
</template>

<script>
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement'
    import DocumentList from '../../components/documents/DocumentList'
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
    import ModalDocumentExternal from '../../components/modals/ModalDocumentExternal';
    import ModalCheckAnnexe from '../../components/modals/ModalCheckAnnexe'
    import ModalCheckRequirement from '../../components/modals/ModalCheckRequirement'
    import {
        Doc,
        Text,
        Paragraph,
        Heading,
        Bold,
        Underline,
        Italic,
        ListItem,
        BulletList,
        OrderedList,
        TodoItem,
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
            DocumentListRequirement,
            DocumentList,
            VueBootstrapTypeahead,
            vSelect,
            ModalDocumentExternal,
            ModalCheckAnnexe,
            ModalCheckRequirement
        },
        props : ['ruta'],
        watch: {
            query(newQuery) {
                if (this.query.length > 0) {
                    axios.get(`${this.ruta}/documentProcedureSearchExternal?newQuery=${newQuery}&records=${this.records}`)
                        .then((response) => {
                            this.procedures        = response.data.procedures;
                            this.procedure_id      = response.data.procedure_id;
                            this.procedure_like_id = response.data.procedure_like_id;
                            if (this.procedure_id === this.procedure_like_id ) {
                                this.selectedProcedures      = response.data.procedures[0];
                                this.requirement_document_id = response.data.requirement_document_id;
                            }
                        })
                }
            },
            selectedProcedures() {
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
                                    this.states.push(value.id)
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
            },
            initial_date() {
              this.final_date = '';
            }
        },
        data () {
            return {
                extensions: [
                    new Doc(),
                    new Text(),
                    new Paragraph(),
                    new Heading({ level: 5 }),
                    new Bold(),
                    new Underline(),
                    new Italic(),
                    new TextAlign(),
                    new ListItem(),
                    new BulletList(),
                    new OrderedList(),
                    new TodoItem(),
                    new TrailingNode(),
                    new Table({
                        resizable: true,
                    }),
                    new TableHeader(),
                    new TableCell(),
                    new TableRow(),
                    new History(),
                ],
                arrayTable: [],
                arrayTableReport: [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                arrayRequirements: [],
                show: false,
                transparency: 0,
                viewPending: 1,
                document_type_id: 0,
                document_priority_id: 0,
                dependency_shipping_id: 0,
                requirement_document_id: 0,
                number: '',
                initial_date: '',
                final_date: '',
                downloadLoading: false,
                countRow: 0,
                person: '',
                dependency: '',
                entity: '',
                arrayDocumentTypes: [],
                dependencies: [],
                date: 0,
                modal: 0,
                modal2: 0,
                titleModal : '',
                typeAction : 0,
                typeAction2: 0,
                errors: [],
                affair: '',
                detail: '',
                annexes: '',
                provided: [],
                folio: '',
                shipping_date: '',
                tupa: 0,
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
                document_id: 0,
                documentary_procedure_id: 0,
                type_reception_id: 0,
                arrayDocumentPriorities: [],
                selectedProvided: {id: 0, label: ''},
                selectedDocumentType: {id: 0, label: 'Todos los tipos de documentos'},
                selectedDependencyProvided: {id: 0, label: 'Todas las dependencias'},
                tupa_original: 0,
                auth: 0,
                selectedProcedures: '',
                document_sent: 1,
                procedure_id: 0,
                procedure_like_id: 0,
                pending_change_id: 0,
                document_change_id: 0,
                search: '',
                state_id: 0,
                created_at: '',
                created_by: '',
                person_reception: '',
                person_id: 0,
                typeDependency:0,
                showModal: 0,
                showInternalSent: 0,
                dependency_id: 0,
                document: '',
            }
        },
        computed: {
            isActived() {
                return this.pagination.current_page;
            },
            pagesNumber() {
                if(!this.pagination.to) {
                    return [];
                }
                let from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                let pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },
            modalType(){
                switch(this.showModal){
                    case 1: {
                        return 'modal-document-external'
                    }
                    case 2: {
                        return 'modal-check-requirement'
                    }
                    case 3: {
                        return 'modal-check-annexe'
                    }
                }
            },
        },
        methods: {
            modalShowRequirement(id) {
                this.showModal = 2;
                this.document_id = id;
            },
            closeModalRequirement()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            modalShowAnnexe(id) {
                this.showModal = 3;
                this.document_id = id;
            },
            closeModalAnnexe()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            getSent(page) {
                let url = `${this.ruta}/tableDocumentReportInternal?page=${page}&search=${this.search}&pending_change_id=${this.pending_change_id}&transparency=${this.transparency}&search=${this.search}&initial_date=${this.initial_date}&final_date=${this.final_date}&date=${this.date}&document_type=${this.selectedDocumentType.id}&dependency=${this.selectedDependencyProvided.id}&typeDependency=${this.typeDependency}`;
                axios.get(url)
                    .then((response) => {
                        this.arrayTable = response.data.queryReport.data;
                        this.pagination = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            restart() {
                this.pending_change_id    = 0;
                this.transparency         = 0;
                this.search               = '';
                this.initial_date         = '';
                this.final_date           = '';
                this.date                 = 0;
                this.selectedDocumentType = {id: 0, label: 'Todos los tipos de documentos'};
                this.selectedDependencyProvided = {id: 0, label: 'Todas las dependencias'};
                this.getSent(1);
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.getSent(page);
            },
            getDependencies() {
                axios.get(`${this.ruta}/documentDependencyReport`)
                .then((response) => {
                    this.dependencies = response.data.dependencies;
                })
            },
            showModalDocument(tracing) {
                this.showModal = 1;
                this.document = tracing;
            },
            closeModalDocument()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            selectedQuery()
            {
                if (this.query !== '') {
                    this.affair = this.query;
                    this.closeModalProcedure()
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            getDocumentToSent() {
                axios.get(`${this.ruta}/documentToSent`)
                    .then((response) => {
                    this.arrayDocumentTypes      = response.data.document_type;
                    this.dependencies            = response.data.sent_document_dependencies;
                    this.provided                = response.data.provided;
                    this.arrayDocumentPriorities = response.data.priorities;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            deleted(document_id, documentary_procedure_id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar este registro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        axios.patch(`${this.ruta}/internal?document_id=${document_id}&documentary_procedure_id=${documentary_procedure_id}`)
                            .then(() => {
                                this.getSent(1);
                                Swal.fire(
                                    'Eliminado!',
                                    'El registro ha sido eliminado con éxito.',
                                    'success'
                                )
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                })
            },
            printDocument(id) {
                window.open(`${this.ruta}/document/reportDocument/${id}`, '_blank');
            }
        },
        mounted() {
            this.getDocumentToSent();
            this.getDependencies();
            this.getSent(1);
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
