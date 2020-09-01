<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="icon-printer"/>REPORTES&nbsp;&nbsp;
                    <button type="button" class="btn btn-sm btn-info" @click="restart()"><i class="fa fa-eraser"/> Limpiar</button>
                    <button class="btn btn-sm btn-primary float-right" @click="createPDF()"><i class="fa fa-file-pdf-o"/> PDF</button>
                    <button class="btn btn-sm btn-success float-right" @click="createExcel()"><i class="fa fa-file-excel-o"/> EXCEL</button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <select
                                v-model="pending_change_id"
                                class="form-control form-control-sm"
                                @change="getTableDocumentReport(1); getTableReport()"
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
                                    @change="getTableDocumentReport(1); getTableReport()"
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
                                    @change="getTableDocumentReport(1); getTableReport()"
                                    :true-value="1"
                                    :false-value="0"
                                >
                                <span class="switch-label"/>
                                <span class="switch-handle"/>
                            </label>
                            &nbsp;Transparencia
                        </div>
                        <div class="col-sm-2">
                            <input type="date" v-model="initial_date" class="form-control form-control-sm" @change="getTableDocumentReport(1); getTableReport()">
                        </div>
                        <div class="col-sm-2" v-if="this.initial_date !== ''">
                            <input type="date" v-model="final_date" class="form-control form-control-sm" @change="getTableDocumentReport(1); getTableReport()">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <v-select
                                v-model="selectedDependencyProvided"
                                :options="dependencies"
                                class="small"
                                :clearable="false"
                                @input="getTableDocumentReport(1); getTableReport()"
                            />
                        </div>
                        <div class="col-sm-2" v-if="selectedDependencyProvided.id !== 0">
                            <label class="switch switch-md switch-3d switch-primary">
                                <input
                                    type="checkbox"
                                    class="switch-input"
                                    v-model="typeDependency"
                                    @change="getTableDocumentReport(1)"
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
                                @input="getTableDocumentReport(1); getTableReport()"
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
                                    <th><small><b>ANEXOS</b></small></th>
                                    <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                                    <th><small><b>FOLIOS</b></small></th>
                                    <th><small><b>PRIORIDAD</b></small></th>
                                    <th><small><b>FECHA DE ENVÍO</b></small></th>
                                    <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                                    <th><small><b>TRÁMITE</b></small></th>
                                    <th><small><b>FECHA DE TRÁMITE</b></small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tracing in arrayTable" :key="tracing.id">
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
    </main>
</template>

<script>
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement'
    import DocumentList from '../../components/documents/DocumentList'
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
    import PDF from 'jspdf'
    import 'jspdf-autotable'
    import * as Export2Excel from '../../../../vendor/Export2Excel.js'

    export default {
        components: {
            DocumentListRequirement,
            DocumentList,
            VueBootstrapTypeahead,
            vSelect
        },
        props : ['ruta'],
        watch: {
            query(newQuery) {
                if (this.query.length > 0) {
                    var url = this.ruta + '/documentProcedureSearchExternal?newQuery=' + newQuery + '&records=' + this.records;
                    axios.get(url)
                    .then((res) => {
                        this.procedures              = res.data.procedures;
                        this.procedure_id            = res.data.procedure_id;
                        this.procedure_like_id       = res.data.procedure_like_id;
                        if (this.procedure_id === this.procedure_like_id ) {
                            this.selectedProcedures      = res.data.procedures[0];
                            this.requirement_document_id = res.data.requirement_document_id;
                        }
                    })
                }
            },
            selectedProcedures() {
                let me=this;
                this.requirements = [];
                this.states       = [];

                if (this.procedure_id === this.procedure_like_id)
                {
                    var url = this.ruta + '/documentProcedureQueryState/' + this.requirement_document_id;
                }else {
                    var url = this.ruta + '/documentProcedureQuery/' + this.selectedProcedures.id;
                }
                axios.get(url)
                    .then((res) => {
                        this.requirements = res.data.requirements;
                        if (this.procedure_id === this.procedure_like_id) {
                            this.requirements = res.data.requirement_states;
                            $.each(res.data.requirement_states, function (key, value) {
                                if (value.process_state_id === 1) {
                                    me.states.push(value.id)
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
                arrayTable: [],
                arrayTableReport: [],
                offset : 3,
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
                pending_change_id: 0,
                search: '',
                query: '',
                type_reception_id: 0,
                document_type_id: 0,
                document_priority_id: 0,
                dependency_shipping_id: 0,
                requirement_document_id: 0,
                selectedDependencyProvided: {
                    id: 0,
                    label: 'Todas las dependencias'
                },
                selectedDocumentType: {
                    id: 0,
                    label: 'Todos los tipos de documentos'
                },
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
                typeDependency: 0,
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
            getTableDocumentReport(page) {
                let me=this;
                var url= me.ruta + '/tableDocumentReport?page=' + page
                    + '&pending_change_id=' + me.pending_change_id
                    + '&transparency=' + me.transparency
                    + '&search=' + me.search
                    + '&initial_date=' + me.initial_date
                    + '&final_date=' + me.final_date
                    + '&date=' + me.date
                    + '&document_type=' + me.selectedDocumentType.id
                    + '&dependency=' + me.selectedDependencyProvided.id
                    + '&typeDependency=' + me.typeDependency;
                axios.get(url).then((response) => {
                    var res       = response.data;
                    me.arrayTable = res.queryReport.data;
                    me.pagination = res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getTableReport() {
                let me=this;
                var url= this.ruta + '/tableReport?pending_change_id=' + this.pending_change_id
                    + '&transparency=' + this.transparency
                    + '&search=' + this.search
                    + '&initial_date=' + this.initial_date
                    + '&final_date=' + this.final_date
                    + '&date=' + this.date
                    + '&document_type=' + this.selectedDocumentType.id
                    + '&dependency=' + this.selectedDependencyProvided.id
                    + '&typeDependency=' + this.typeDependency;
                axios.get(url).then((response) => {
                    me.arrayTableReport = response.data.tableReport;
                    me.countRow         = response.data.count;
                    me.entity           = response.data.entity;
                    me.dependency       = response.data.dependency;
                    me.person           = response.data.person;
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
                this.getTableDocumentReport(1);
            },
            changePage(page) {
                let me = this;
                me.pagination.current_page = page;
                me.getTableDocumentReport(page);
            },
            getDocumentToSent() {
                var url = this.ruta + '/documentToSent';
                axios.get(url).then((response) => {
                    this.arrayDocumentTypes      = response.data.document_type;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            getDependencies() {
                let me=this;
                var url = me.ruta + '/documentDependencyReport';
                axios.get(url)
                .then((response) => {
                    me.dependencies = response.data.dependencies;
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
                    //description: 'CONTENIDO',
                    //annexes: 'ANEXO',
                    provided: 'PROVEÍDO DE ATENCIÓN',
                    folio: 'FOLIOS',
                    priority: 'PRIORIDAD',
                    shipping_date: 'FECHA DE ENVÍO',
                    reception_date: 'FECHA DE RECEPCIÓN',
                    state: 'TRÁMITE',
                    //procedure_date: 'FECHA DE TRÁMITE'
                }];
            },
            bodyRows(rowCount, pdfBody) {
                rowCount = rowCount || 10;
                let body = [];
                for (var i = 0; i < rowCount; i++) {

                    body.push({
                        records: pdfBody[i].records,
                        registry: pdfBody[i].registry+'\nReferencia: '+pdfBody[i].reference,
                        Dependency: pdfBody[i].Dependency+'\nUsuario: '+pdfBody[i].fullNameT+'\nDni: '+pdfBody[i].dniT+'\nEntidad: '+pdfBody[i].entityDependency,
                        Destination: pdfBody[i].Destination+'\nUsuario: '+pdfBody[i].fullNameR+'\nDni: '+pdfBody[i].dniR+'\nEntidad: '+pdfBody[i].entityDestination,
                        code: pdfBody[i].code,
                        affair: pdfBody[i].affair,
                        //description: pdfBody[i].description,
                        //annexes: pdfBody[i].annexes,
                        provided: pdfBody[i].provided,
                        folio: pdfBody[i].folio,
                        priority: pdfBody[i].priority,
                        shipping_date: pdfBody[i].shipping_date,
                        reception_date: pdfBody[i].reception_date,
                        state: pdfBody[i].state === 1 ? 'Pendiente' : '' || pdfBody[i].state === 3 ? 'Derivado' : '' || pdfBody[i].state === 4 ? 'Archivado' : '' || pdfBody[i].state === 6 ? 'Finalizado' : '',
                        //procedure_date: pdfBody[i].procedure_date,
                    });
                }
                return body;
            },
            bodyRowsExcel(rowCount, pdfBody) {
                rowCount = rowCount || 10;
                let body = [];
                for (var i = 0; i < rowCount; i++) {

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
            createPDF () {
                let doc = new PDF("l", "pt", "letter");

                let v = this;

                var header = function () {
                    var header_0 = function () {
                        doc.setFontSize(6);
                        doc.text(39, 30, v.entity)
                    };
                    doc.autoTable({didDrawPage : header_0});

                    var header_1 = function () {
                        doc.setFontSize(6);
                        doc.text(39, 40, 'MEZA DE PARTES')
                    };
                    doc.autoTable({didDrawPage : header_1});

                    var header_2 = function () {
                        doc.setFontSize(5);
                        doc.text(39, 50, 'SYSDOC V1.0')
                    };
                    doc.autoTable({didDrawPage : header_2});

                    var today = new Date();
                    var header_3 = function () {
                        doc.setFontSize(6);
                        doc.text(698, 30, `Fecha: ${today.getDate()<10 ? '0'+today.getDate() : today.getDate()}/${today.getMonth()<10 ? '0'+today.getMonth():today.getMonth()}/${today.getFullYear()<10 ? '0'+today.getFullYear():today.getFullYear()}`)
                    };
                    doc.autoTable({didDrawPage : header_3});

                    var header_4 = function () {
                        doc.setFontSize(6);
                        doc.text(698, 40, `Hora: ${today.getHours()<10 ? '0'+today.getHours() : today.getHours()}:${today.getMinutes()<10 ? '0'+today.getMinutes():today.getMinutes()}:${today.getSeconds()<10 ? '0'+today.getSeconds():today.getSeconds()}`)
                    };
                    doc.autoTable({didDrawPage : header_4});

                    var header_5 = function () {
                        doc.setFontSize(6);
                        doc.text(698, 50, `Página ${doc.internal.getNumberOfPages()}`);
                    };
                    doc.autoTable({didDrawPage : header_5});

                    var header_6 = function () {
                        doc.setFontSize(14);
                        doc.text(`${v.pending_change_id === 0 ? 'TODOS LOS DOCUMENTOS' : '' || v.pending_change_id === 1 ? 'DOCUMENTOS PENDIENTES' : '' || v.pending_change_id === 2 ? 'DOCUMENTOS ATENDIDOS' : ''}`, doc.internal.pageSize.width/2, 70, 'center');
                    };
                    doc.autoTable({didDrawPage : header_6});

                    var header_7 = function () {

                        var month_default = new Date();
                        var option_default = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        var month_initial = new Date(v.initial_date);
                        var option_initial = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        var month_final = new Date(v.final_date);
                        var option_final = {timeZone:'UTC', day : 'numeric', month: 'long', year: 'numeric'};

                        if (isNaN(month_initial.getFullYear())) {
                            doc.setFontSize(10);
                            doc.text(`${month_default.toLocaleDateString("es-ES", option_default)}`, doc.internal.pageSize.width/2, 85, 'center')
                        }else {
                            doc.setFontSize(10);
                            doc.text(`${month_initial.toLocaleDateString("es-ES",option_initial)} ${isNaN(month_final.getFullYear()) ? "" : "a"} ${isNaN(month_final.getFullYear()) ? "" : month_final.toLocaleDateString("es-ES", option_final)}`, doc.internal.pageSize.width/2, 85, 'center')
                        }
                    };
                    doc.autoTable({didDrawPage : header_7});

                    var header_8 = function () {
                        doc.setFontSize(8);
                        doc.text(39, 110, `DEPENDENCIA:  ${v.dependency}`)
                    };
                    doc.autoTable({didDrawPage : header_8});

                    var header_9 = function () {
                        doc.setFontSize(8);
                        doc.text(39, 120, `RESPONSABLE:  ${v.person}`)
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
                    body: this.bodyRows(this.countRow, this.arrayTableReport),

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
                        //6: {cellWidth: 50},
                        //7: {cellWidth: 40},
                        6: {cellWidth: 60},
                        7: {cellWidth: 30},
                        8: {cellWidth: 40},
                        9: {cellWidth: 50},
                        10: {cellWidth: 50},
                        11: {cellWidth: 50},
                        //14: {cellWidth: 70}
                    }
                });

                doc.save(`Reporte${Date.now()}.pdf`);
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
                    const List = this.bodyRowsExcel(this.countRow, this.arrayTableReport);
                    const Data = this .formatJson (filterVal, List);
                    const filename = `Reporte${Date.now()}`;
                    const bookType = 'xlsx';
                    const autoWidth = true;
                    Export2Excel.export_json_to_excel ({header:tHeader, data:Data, autoWidth:autoWidth, filename:filename,bookType:bookType});
                })
            }
        },
        mounted() {
            this.getDocumentToSent();
            this.getDependencies();
        }
    }
</script>
