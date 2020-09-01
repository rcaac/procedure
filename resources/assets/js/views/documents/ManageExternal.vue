<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="icon-doc"/>DOCUMENTOS EXTERNOS&nbsp;&nbsp;
                    <button type="button" class="btn btn-sm btn-info" @click="restart()"><i class="fa fa-eraser"/> Limpiar</button>
                    <button class="btn btn-sm btn-primary float-right" @click="createPDF()"><i class="fa fa-file-pdf-o"/> PDF</button>
                    <button class="btn btn-sm btn-success float-right" @click="createExcel()"><i class="fa fa-file-excel-o"/> EXCEL</button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-3">
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
                                    <i class="fa fa-search"/> Buscar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select
                                v-model="pending_change_id"
                                class="form-control form-control-sm"
                                @change="getSent(1), getPartyTableReport()"
                            >
                                <option :value="0">Todos</option>
                                <option :value="1">PENDIENTES</option>
                                <option :value="2">ATENDIDOS</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="switch switch-md switch-3d switch-primary">
                                <input
                                    type="checkbox"
                                    class="switch-input"
                                    v-model="transparency"
                                    @change="getSent(1), getPartyTableReport()"
                                    :true-value="1"
                                    :false-value="0"
                                >
                                <span class="switch-label"/>
                                <span class="switch-handle"/>
                            </label>
                            &nbsp;Transparencia
                        </div>
                        <div class="col-sm-2">
                            <input type="date" v-model="initial_date" class="form-control form-control-sm" @change="getSent(1), getPartyTableReport()">
                        </div>
                        <div class="col-sm-2" v-if="this.initial_date !== ''">
                            <input type="date" v-model="final_date" class="form-control form-control-sm" @change="getSent(1), getPartyTableReport()">
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
                                    <th><small><b>ANEXOS</b></small></th>
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
                                <tr v-for="tracing in arrayPartyTable" :key="tracing.id">
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
                                    <td v-text="tracing.annexes"/>
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
                                    <td v-if="tracing.state ===6"><span  class="badge badge-pill badge-primary">Finalizado</span></td>
                                    <td>
                                        {{ tracing.procedure_date }}
                                    </td>
                                    <td>
                                        <div v-if="tracing.tupa === 1">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                @click=" openModalRequirement(tracing.records)"
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
                                            class="btn btn-outline-secondary btn-sm"
                                            @click="printDocument(tracing.id)"
                                        >
                                            <i class="icon-printer"/> Imprimir
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-outline-success btn-sm" @click="receive(tracing.documentary_procedure_id)">
                                                <i class="icon-check"/> Recepcionar
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" @click="openModal(tracing)">
                                                <i class="icon-pencil"/> Editar
                                            </button>
                                        </div>
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
            :ruta="ruta"
            :records="this.records"
            :document_id="this.document_id"
            :id="this.document_id"
            :data="this.document"
            :internalSent="this.showInternalSent"
            :annexeCreate="this.annexeCreate"
            @getSent="getSent($event)"
            @closeModalAnnexe="closeModalAnnexe"
            @closeModalRequirement="closeModalRequirement"
            @closeModalDocument="closeModalDocument"
        />
    </main>
</template>

<script>
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement'
    import DocumentList from '../../components/documents/DocumentList'
    import ModalCheckAnnexe from '../../components/modals/ModalCheckAnnexe'
    import ModalCheckRequirement from '../../components/modals/ModalCheckRequirement'
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
    import PDF from 'jspdf'
    import 'jspdf-autotable';
    import * as Export2Excel from '../../../../vendor/Export2Excel.js'
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
    import ModalDocumentExternal from "../../components/modals/ModalDocumentExternal";

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
            selectedProcedures() {
                this.requirements = [];
                this.states       = [];
                let url = '';

                if (this.procedure_id === this.procedure_like_id) {
                    url = `${this.ruta}/documentProcedureQueryState/${this.requirement_document_id}`
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
                arrayPartyTable: [],
                arrayPartyTableReport: [],
                offset : 3,
                documentary_procedure_id: 0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                show: 0,
                arrayRequirements: [],
                transparency: 0,
                viewPending: 1,
                pending_change_id: 0,
                search: '',
                modal1: 0,
                modal2: 0,
                modal3: 0,
                titleModal: '',
                typeAction: 0,
                procedures: [],
                requirements: [],
                states: [],
                records: '',
                query: '',
                files: '',
                archive: '',
                validateTupa: '',
                arraySent: [],
                errors: [],
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
                document_change_id: 0,
                document_origin_id: 0,
                number: '',
                initial_date: '',
                final_date: '',
                downloadLoading: false,
                countRow: 0,
                person: '',
                dependency: '',
                entity: '',
                created_at: '',
                created_by: '',
                person_reception: '',
                person_id: 0,
                document: '',
                annexeCreate: '',
                showInternalSent: 0
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
                switch(this.show){
                    case 1: {
                        return 'modal-check-annexe'
                    }
                    case 2: {
                        return 'modal-check-requirement'
                    }
                    case 3: {
                        return 'modal-document-external'
                    }
                }
            }    
        },
        methods : {
            openModalRequirement(id) {
                this.show = 2;
                this.document_id = id;
            },
            closeModalRequirement() {
                this.show = 0;
                this.document = '';
            },
            modalShowAnnexe(id) {
                this.show = 1;
                this.document_id= id;
            },
            closeModalAnnexe() {
                this.show = 0;
                this.document_id = 0;
            }, 
            getSent(page) {
                let url= `${this.ruta}/partyTable?page=${page}&pending_change_id=${this.pending_change_id}&transparency=${this.transparency}&search=${this.search}&initial_date=${this.initial_date}&final_date=${this.final_date}`;
                axios.get(url)
                    .then((response) => {
                        this.arrayPartyTable = response.data.partyTable.data;
                        this.pagination  = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getPartyTableReport() {
                let url= `${this.ruta}/partyTableReport?pending_change_id=${this.pending_change_id}&transparency=${this.transparency}&search=${this.search}&initial_date=${this.initial_date}&final_date=${this.final_date}`;
                axios.get(url)
                    .then((response) => {
                        this.arrayPartyTableReport = response.data.partyTableReport;
                        this.countRow              = response.data.count;
                        this.entity                = response.data.entity;
                        this.dependency            = response.data.dependency;
                        this.person                = response.data.person;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            restart() {
                this.pending_change_id = 0;
                this.transparency      = 0;
                this.search            = '';
                this.initial_date      = '';
                this.final_date        = '';
                this.getSent(1);
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.getSent(page);
            },
            receive(id) {
                axios.get(`${this.ruta}/slopes/${id}`)
                    .then(() => {
                        this.arrayRequirements  = [];
                        this.views = false;
                        this.getSent(1);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getRequirements(records) {
                this.arrayRequirements = [];
                axios.get(`${this.ruta}/tracingRequirement/${records}`)
                    .then((response) => {
                        this.arrayRequirements = response.data.requirements;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            onChecked(id, check, document_id) {
                axios.get(`${this.ruta}/checkedRequirements?id=${id}&check=${check}&document_id=${document_id}`)
                    .then((response) => {
                        console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            updateRequirement(data = []) {
                axios.put(`${this.ruta}/document/updateObservationRequirement`,{
                    'observation'           : data['observation'],
                    'document_procedure_id' : data['document_procedure_id'],
                }).then((response) => {
                    console.log(response.data)
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            openModal(tracing) {
                this.show = 3;
                this.document = tracing;
            },
            closeModalDocument() {
                this.show = 0;
                this.document_id = 0;
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
            headRows() {
                return [{
                    records: 'EXPEDIENTE',
                    registry: 'REGISTRO',
                    Dependency: 'DEPENDENCIA EMISOR',
                    Destination: 'DEPENDENCIA RECEPTOR',
                    code: 'TIPO DE DOCUMENTO',
                    affair: 'ASUNTO',
                    provided: 'PROVEÍDO DE ATENCIÓN',
                    folio: 'FOLIOS',
                    priority: 'PRIORIDAD',
                    shipping_date: 'FECHA DE ENVÍO',
                    reception_date: 'FECHA DE RECEPCIÓN',
                    state: 'TRÁMITE'
                }];
            },
            bodyRows(rowCount, pdfBody) {
                rowCount = rowCount || 10;
                let body = [];
                for (let i = 0; i < rowCount; i++) {

                    body.push({
                        records: pdfBody[i].records,
                        registry: pdfBody[i].registry+'\nReferencia: '+pdfBody[i].reference,
                        Dependency: pdfBody[i].Dependency+'\nUsuario: '+pdfBody[i].fullNameT+'\nDni: '+pdfBody[i].dniT+'\nEntidad: '+pdfBody[i].entityDependency,
                        Destination: pdfBody[i].Destination+'\nUsuario: '+pdfBody[i].fullNameR+'\nDni: '+pdfBody[i].dniR+'\nEntidad: '+pdfBody[i].entityDestination,
                        code: pdfBody[i].code,
                        affair: pdfBody[i].affair,
                        provided: pdfBody[i].provided,
                        folio: pdfBody[i].folio,
                        priority: pdfBody[i].priority,
                        shipping_date: pdfBody[i].shipping_date,
                        reception_date: pdfBody[i].reception_date,
                        state: pdfBody[i].state === 1 ? 'Pendiente' : '' || pdfBody[i].state === 3 ? 'Derivado' : '' || pdfBody[i].state === 4 ? 'Archivado' : '' || pdfBody[i].state === 6 ? 'Finalizado' : '',
                    });
                }
                return body;
            },
            createPDF () {
                let doc = new PDF("l", "pt", "letter");
                let header = () => {
                    let header_0 = () => {
                        doc.setFontSize(6);
                        doc.text(39, 30, this.entity)
                    };
                    doc.autoTable({didDrawPage : header_0});

                    let header_1 = () => {
                        doc.setFontSize(6);
                        doc.text(39, 40, 'MEZA DE PARTES')
                    };
                    doc.autoTable({didDrawPage : header_1});

                    let header_2 = () => {
                        doc.setFontSize(5);
                        doc.text(39, 50, 'SYSDOC V1.0')
                    };
                    doc.autoTable({didDrawPage : header_2});

                    let today = new Date();
                    let header_3 = () => {
                        doc.setFontSize(6);
                        doc.text(698, 30, `Fecha: ${today.getDate()<10 ? '0'+today.getDate() : today.getDate()}/${today.getMonth()<10 ? '0'+today.getMonth():today.getMonth()}/${today.getFullYear()<10 ? '0'+today.getFullYear():today.getFullYear()}`)
                    };
                    doc.autoTable({didDrawPage : header_3});

                    let header_4 = () => {
                        doc.setFontSize(6);
                        doc.text(698, 40, `Hora: ${today.getHours()<10 ? '0'+today.getHours() : today.getHours()}:${today.getMinutes()<10 ? '0'+today.getMinutes():today.getMinutes()}:${today.getSeconds()<10 ? '0'+today.getSeconds():today.getSeconds()}`)
                    };
                    doc.autoTable({didDrawPage : header_4});

                    let header_5 = () => {
                        doc.setFontSize(6);
                        doc.text(698, 50, `Página ${doc.internal.getNumberOfPages()}`);
                    };
                    doc.autoTable({didDrawPage : header_5});

                    let header_6 = () => {
                        doc.setFontSize(14);
                        doc.text(`${this.pending_change_id === 0 ? 'TODOS LOS DOCUMENTOS' : '' || this.pending_change_id === 1 ? 'DOCUMENTOS PENDIENTES' : '' || this.pending_change_id === 2 ? 'DOCUMENTOS ATENDIDOS' : ''}`, doc.internal.pageSize.width/2, 70, 'center');
                    };
                    doc.autoTable({didDrawPage : header_6});

                    let header_7 = () => {

                        let month_default = new Date();
                        let option_default = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        let month_initial = new Date(this.initial_date);
                        let option_initial = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        let month_final = new Date(this.final_date);
                        let option_final = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        if (isNaN(month_initial.getFullYear())) {
                            doc.setFontSize(10);
                            doc.text(`${month_default.toLocaleDateString("es-ES", option_default)}`, doc.internal.pageSize.width/2, 85, 'center')
                        }else {
                            doc.setFontSize(10);
                            doc.text(`${month_initial.toLocaleDateString("es-ES",option_initial)} ${isNaN(month_final.getFullYear()) ? "" : "a"} ${isNaN(month_final.getFullYear()) ? "" : month_final.toLocaleDateString("es-ES", option_final)}`, doc.internal.pageSize.width/2, 85, 'center')
                        }
                    };
                    doc.autoTable({didDrawPage : header_7});

                    let header_8 = () => {
                        doc.setFontSize(8);
                        doc.text(39, 110, `DEPENDENCIA:  ${this.dependency}`)
                    };
                    doc.autoTable({didDrawPage : header_8});

                    let header_9 = () => {
                        doc.setFontSize(8);
                        doc.text(39, 120, `RESPONSABLE:  ${this.person}`)
                    };
                    doc.autoTable({didDrawPage : header_9});
                };

                doc.autoTable({
                    didDrawPage : header,
                    margin: {top: 130},
                    styles: {
                        cellPadding: 1,
                        fontSize: 7,
                    },
                    head: this.headRows(),
                    body: this.bodyRows(this.countRow, this.arrayPartyTableReport),

                    bodyStyles: {
                        margin: 20,
                        fontSize: 6
                    },
                    columnStyles: {
                        0: {cellWidth: 55},
                        1: {cellWidth: 50},
                        2: {cellWidth: 100},
                        3: {cellWidth: 100},
                        4: {cellWidth: 60},
                        5: {cellWidth: 60},
                        6: {cellWidth: 60},
                        7: {cellWidth: 30},
                        8: {cellWidth: 40},
                        9: {cellWidth: 50},
                        10: {cellWidth: 50},
                        11: {cellWidth: 50}
                    }
                });

                doc.save(`Reporte${Date.now()}.pdf`);
            },
            bodyRowsExcel(rowCount, pdfBody) {
                rowCount = rowCount || 10;
                let body = [];
                for (let i = 0; i < rowCount; i++) {

                    body.push({
                        records: pdfBody[i].records,
                        registry: pdfBody[i].registry+'\nReferencia: '+pdfBody[i].reference,
                        Dependency: pdfBody[i].Dependency+'\nUsuario: '+pdfBody[i].fullNameT+'\nDni: '+pdfBody[i].dniT+'\nEntidad: '+pdfBody[i].entityDependency,
                        Destination: pdfBody[i].Destination+'\nUsuario: '+pdfBody[i].fullNameR+'\nDni: '+pdfBody[i].dniR+'\nEntidad: '+pdfBody[i].entityDestination,
                        code: pdfBody[i].code,
                        affair: pdfBody[i].affair,
                        description: pdfBody[i].description,
                        annexes: pdfBody[i].annexes,
                        provided: pdfBody[i].provided,
                        folio: pdfBody[i].folio,
                        priority: pdfBody[i].priority,
                        shipping_date: pdfBody[i].shipping_date,
                        reception_date: pdfBody[i].reception_date,
                        state: pdfBody[i].state === 1 ? 'Pendiente' : '' || pdfBody[i].state === 3 ? 'Derivado' : '' || pdfBody[i].state === 4 ? 'Archivado' : '' || pdfBody[i].state === 6 ? 'Finalizado' : '',
                        procedure_date: pdfBody[i].procedure_date,
                    });
                }
                return body;
            },
            formatJson(filterVal, jsonData){
                return jsonData.map(v => filterVal.map(j => v[j]))
            },
            createExcel () {
                require.ensure ([], () => {
                    const tHeader = [
                        'EXPEDIENTE',
                        'REGISTRO',
                        'DEPENDENCIA EMISOR',
                        'DEPENDENCIA RECEPTOR',
                        'TIPO DE DOCUMENTO',
                        'ASUNTO',
                        'CONTENIDO',
                        'ANEXO',
                        'PROVEÍDO DE ATENCIÓN',
                        'FOLIOS',
                        'PRIORIDAD',
                        'FECHA DE ENVÍO',
                        'FECHA DE RECEPCIÓN',
                        'TRÁMITE',
                        'FECHA DE TRÁMITE'
                    ];
                    const filterVal = [
                        'records',
                        'registry',
                        'Dependency',
                        'Destination',
                        'code',
                        'affair',
                        'description',
                        'annexes',
                        'provided',
                        'folio',
                        'priority',
                        'shipping_date',
                        'reception_date',
                        'state',
                        'procedure_date'
                    ];
                    const List = this.bodyRowsExcel(this.countRow, this.arrayPartyTableReport);
                    const Data = this .formatJson (filterVal, List);
                    const filename = `Reporte${Date.now()}`;
                    const bookType = 'xlsx';
                    const autoWidth = true;
                    Export2Excel.export_json_to_excel ({header:tHeader, data:Data, autoWidth:autoWidth, filename:filename,bookType:bookType});
                })
            },
            printDocument(id) {
                let me=this;
                window.open(`${me.ruta}/document/reportExternal/${id}`, '_blank');
            }
        },
        mounted() {
            this.getSent(1);
            this.getPartyTableReport();
            this.getDocumentToSent();
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-requirements{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
    .show-document-party{
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
