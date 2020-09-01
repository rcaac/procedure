<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="vld-parent">
                <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
            </div>
            <div class="card">
                <div class="card-header">
                    <h6><i class="icon-doc"/> DOCUMENTOS PARA TRAMITAR</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row mt-sm-0">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="getProcess(search)"
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
                                @change="getProcess(1)"
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
                                @change="getProcess(1)"
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
                            <th><small><b>DEPENDENCIA EMISOR</b></small></th>
                            <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                            <th><small><b>ASUNTO</b></small></th>
                            <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                            <th><small><b>FOLIOS</b></small></th>
                            <th><small><b>PRIORIDAD</b></small></th>
                            <th><small><b>FECHA DE ENVÍO</b></small></th>
                            <th style="width: 100px;"><small><b>ACCIONES</b></small></th>
                        </tr>
                        </thead>
                        <tbody>
                        <document-list
                            v-for="process in arrayProcess"
                            :key="process.id"
                            :pending="process"
                            :viewProcess="origin_data.viewProcess"
                            @openModalRequirement="showModalRequirement($event)"
                            @archiveDocument="archiveDocument(process.documentary_procedure_id)"
                            @archiveReturn="archiveReturn(process.documentary_procedure_id)"
                            @openModal="openModal('pending', 'derive', process)"
                            @showModalAnnexes="showModalAnnexes($event)"
                        />
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
        <!--Inicio del modal-->
        <div class="modal fade" tabindex="-1" :class="{'show' : modal1}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" :class="{'modal-lg-charge': typeAction===1}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"/>
                        <button type="button" class="close" @click="closeModalDerive()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DETALLES DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-generate
                                            :origin_data="origin_data"
                                            @openModalRequirement="showModalRequirement($event)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DESTINO DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-destination
                                            @getDependencies="getDependencies($event)"
                                            @getReception="getReception($event)"
                                            :origin_data="origin_data"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModalDerive()" style="color: white">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1" class="btn btn-primary" @click="deriveDocument()" style="color: white">
                            <i class="fa fa-paper-plane-o"/> Derivar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->
        <component
            :is="modalType"
            :ruta="ruta"
            :id="this.document_id"
            @closeModalAnnexe="closeModalAnnexes"
            @closeModalRequirement="closeModalRequirement"
        />
    </main>
</template>

<script>
    import DocumentGenerate from '../../components/documents/DocumentGenerate';
    import DocumentDestination from '../../components/documents/DocumentDestination';
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement';
    import DocumentList from '../../components/documents/DocumentList'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import ModalCheckAnnexe from '../../components/modals/ModalCheckAnnexe';
    import ModalRequirementInternal from '../../components/modals/ModalRequirementInternal';

    export default {
        components: {
            DocumentGenerate,
            DocumentDestination,
            DocumentListRequirement,
            DocumentList,
            Loading,
            ModalCheckAnnexe,
            ModalRequirementInternal
        },
        props : ['ruta'],
        data () {
            return{
                document_origin_id: 0,
                document_process_origin_id: 0,
                document_id: 0,
                arrayDocumentOrigin: [],
                arrayProcess: [],
                origin_data: {
                    annexes: 0,
                    search: '',
                    shipping_date: '',
                    archive: '',
                    affair: '',
                    folio: '',
                    detail: '',
                    requirements: [],
                    selectedUser: [],
                    selectedCitizen: [],
                    arrayDependencies: [],
                    arrayDocumentTypes: [],
                    actions: [],
                    shipping: [],
                    states: [],
                    arrayTypeReceptions: [],
                    arrayDependencyTypes: [],
                    destinations: [],
                    destination: '',
                    type_reception_id: 1,
                    detail_document_type_id: 0,
                    document_priority_id: 2,
                    route: this.ruta,
                    dependencies: [],
                    procedures: [],
                    dependencyEntity: [],
                    selectedDependencyEntity: {},
                    selectedDependency: {
                        id: 0,
                        label: ''
                    },
                    selectedDependencyProvided: {
                        id: 0,
                        label: 'Elija una dependencia'
                    },
                    selectedProvided: {
                        id: 0,
                        label: 'Elija un proveído'
                    },
                    selectedDocumentType: {
                        id: 0,
                        label: ''
                    },
                    selectedEntityProvided: {
                        id: 5,
                        label: 'MUNICIPALIDAD DISTRITAL DE PILLCO MARCA'
                    },
                    errors: [],
                    references: '',
                    query: '',
                    document_tupa: 0,
                    countDocument: 0,
                    tupa: 0,
                    editDocument: 1,
                    registry: '',
                    dependency_charge: {},
                    person_id: 0,
                    document_process: 0,
                    document_id: 0,
                    records: '',
                    transparency: 0,
                    dependency_shipping_id: 0,
                    reception: {},
                    person_reception: 0,
                    users: [],
                    detailDocumentTypes: [],
                    arrayDocumentPriorities: [],
                    entities: [],
                    provided: [],
                    entity_external: [],
                    number: '',
                    document_origin_id: 0,
                    annexeFile: [
                        {
                            annexe: '',
                            archive: -1,
                            name: '',
                            validateText: '',
                            validateFile: ''
                        }
                    ],
                    imageListAnnexes: [],
                    viewProcess: 1,
                },
                dependency_charge: {},
                modal1 : 0,
                modal2 : 0,
                titleModal : '',
                typeAction : 0,
                errors: [],
                auth: 0,
                dependency: {},
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                records: '',
                arrayRequirements: [],
                dependency_type: {},
                dependency_type_id: 0,
                arrayDocumentTypes: [],
                pending_change_id: 0,
                document_change_id: 0,
                document_type_id: 0,
                responseRecords: 0,
                responseRegistry: 0,
                entity_id: 0,
                search: '',
                isLoading: false,
                fullPage: true,
                show: 0,
            }
        },
        computed:{
            isActived: function() {
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
            reception() {
                return this.origin_data.reception
            },
            modalType() {
                switch(this.show){
                    case 1: {
                        return 'modal-check-annexe'
                    }
                    case 2: {
                        return 'modal-requirement-internal'
                    }
                }
            } 
        },
        methods : {
            showModalAnnexes(id) {                
                this.show = 1;
                this.document_id = id;        
            },
            closeModalAnnexes() {
                this.show = 0;
                this.document_id = 0;               
            },
            showModalRequirement(id) {
                this.show = 2;
                this.document_id = id;
            },
            closeModalRequirement() {
                this.show = 0;
                this.document_id = 0;
            },
            getProcess(page) {
                let url = '';

                if (this.search !== '')
                {
                    url= `${this.ruta}/process?page=${page}&search=${this.search}`;
                }else {
                    url= `${this.ruta}/process?page=${page}&pending_change_id=${this.pending_change_id}&document_type_id=${this.document_change_id}`;
                }

                axios.get(url)
                    .then((response) => {
                        this.arrayProcess = response.data.listProcess.data;
                        this.pagination   = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDateNow() {
                axios.get(`${this.ruta}/documents`)
                    .then((response) => {
                        this.arrayDocumentOrigin                 = response.data.origin;
                        this.origin_data.arrayTypeReceptions     = response.data.type_reception;
                        this.origin_data.arrayDependencyTypes    = response.data.dependency_type;
                        this.origin_data.shipping_date           = response.data.date;
                        this.dependency_type_id                  = response.data.dependency_type_id;
                        this.origin_data.detailDocumentTypes     = response.data.details;
                        this.origin_data.arrayDocumentPriorities = response.data.priorities;
                        this.origin_data.entities                = response.data.entities;
                        this.origin_data.provided                = response.data.provided;
                        if (this.document_origin_id === 0)
                        {
                            this.auth = response.data.auth;
                        }
                        this.dependency_id                       = response.data.dependency;
                        this.entity_id                           = response.data.entity;
                        this.origin_data.selectedDependency.id   = this.dependency_id;
                        this.origin_data.entity_external         = response.data.entity_external;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            deriveDocument() {

                if(this.wait){
                    return
                }
                this.wait = true;

                setTimeout(() => this.wait = false, 5000);

                this.isLoading = true;

                let data = new FormData();

                $.each(this.origin_data.imageListAnnexes, function (key, image) {
                    data.append(`images[${key}]`, image);
                });

                let annexes = JSON.stringify({
                    'annexes': this.origin_data.annexeFile
                });
                data.append('annexes', annexes);

                let destinations = JSON.stringify({
                    'destinations': this.origin_data.destinations
                });
                data.append('destinations', destinations);

                let actions = JSON.stringify({
                    'actions'     : this.origin_data.actions
                });
                data.append('actions', actions);

                let shipping = JSON.stringify({
                    'shipping'     : this.origin_data.shipping
                });
                data.append('shipping', shipping);

                data.append('affair'                 , this.origin_data.affair);
                data.append('description'            , this.origin_data.detail);
                data.append('date'                   , this.origin_data.shipping_date);
                data.append('dependency_id'          , this.origin_data.selectedDependencyProvided.id);
                data.append('document_origin_id'     , this.document_process_origin_id);
                data.append('document_priority_id'   , this.origin_data.document_priority_id);
                data.append('document_type_id'       , this.origin_data.selectedDocumentType.id);
                data.append('type_reception_id'      , this.origin_data.type_reception_id);
                data.append('dependency_shipping_id' , this.origin_data.selectedDependency.id);
                data.append('type'                   , this.origin_data.selectedDocumentType.label);
                data.append('folio'                  , this.origin_data.folio);
                data.append('dependency_type_id'     , this.dependency_type_id);
                data.append('detail_document_type_id', this.origin_data.detail_document_type_id);
                data.append('document_id'            , this.document_id);
                data.append('registry'               , this.origin_data.registry);
                data.append('tupa'                   , this.origin_data.tupa);
                data.append('reference'              , this.origin_data.reference);
                data.append('records'                , this.origin_data.records);
                data.append('person_id'              , this.origin_data.person_id);
                data.append('person_reception'       , this.origin_data.person_reception);
                data.append('transparency'           , this.origin_data.transparency);
                data.append('number'                 , this.origin_data.number);
                data.append('annexe'                 , this.origin_data.annexes);

                axios.post(`${this.ruta}/process/register`, data)
                    .then((response) => {
                        this.isLoading = false;
                        this.arrayRequirements  = [];
                        this.getProcess(1, this.pending_change_id, this.document_change_id);
                        this.closeModalDerive();
                        this.responseRecords = response.data.records;
                        this.responseRegistry = response.data.registry;
                        Swal.fire("Expediente: " + this.responseRecords + "\nRegistro: " + this.responseRegistry);
                        this.printPdf(response.data.id)
                }, (error) => {
                    this.isLoading = false;
                    this.origin_data.errors = error.response.data.errors
                });
            },
            archiveDocument(id) {
                Swal.fire({
                    title: '<h4>Motivo por el cual se archiva el documento.</h4>',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Archivar',
                    showLoaderOnConfirm: true,
                    preConfirm: (observation) => {
                        return axios.post(`${this.ruta}/records`, {
                            'id' : id,
                            'observation': observation
                        }).then(() => {
                            this.arrayRequirements  = [];
                            this.getProcess(1)
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
                            'Archivado!',
                            'El documento ha sido archivado con éxito.',
                            'success'
                        )
                    }
                })
            },
            archiveReturn(id) {
                return axios.post(`${this.ruta}/documentReturn`, {
                    'id' : id
                }).then(() => {
                    this.arrayRequirements  = [];
                    this.getProcess(1)
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            clearForm() {
                this.origin_data.countDocument = 0;
                Object.assign(
                    this.$data,
                    this.$options.data.bind(this.origin_data.destinations=[], this.origin_data.shipping=[], this.origin_data.actions=[])
                )
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.getProcess(page);
            },
            openModal(model, action, data = []) {
                switch (model) {
                    case "pending":
                    {
                        switch (action) {
                            case 'derive':
                            {
                                this.modal1                                  = 1;
                                this.titleModal                             = 'Derivar Documento';
                                this.typeAction                             = 1;
                                this.dependency_id                          = data['dependency_id'];
                                this.records                                = data['records'];
                                this.document_origin_id                     = data['document_origin_id'];
                                this.documentary_procedure_id               = data['documentary_procedure_id'];
                                this.document_id                            = data['documentary_procedure_id'];
                                this.origin_data.document_tupa              = data['tupa'];
                                this.origin_data.tupa                       = data['tupa'];
                                this.origin_data.document_process           = data['tupa'];
                                this.origin_data.affair                     = data['affair'];
                                this.origin_data.detail_document_type_id    = data['detail_document_type_id'];
                                this.origin_data.selectedDocumentType.id    = data['document_type_id'];
                                this.origin_data.selectedDocumentType.label = data['type'];
                                this.origin_data.registry                   = data['registry'];
                                this.origin_data.records                    = data['records'];
                                this.origin_data.document_id                = data['id'];
                                this.origin_data.annexes                    = data['annexes'];
                                break;
                            }
                        }
                    }
                }
            },
            closeModalDerive() {
                this.modal1                              = 0;
                this.titleModal                          = '';
                this.selectedDependencyProvided          = {id: 0, label: 'Elija una dependencia'};
                this.selectedProvided                    = {id: 0, label: 'Elija un proveído'};
                this.origin_data.folio                   = '';
                this.origin_data.have_tupa               = 0;
                this.origin_data.document_process        = 0;
                this.origin_data.errors                  = 0;
                this.origin_data.query                   = '';
                this.origin_data.detail_document_type_id = 0;
                this.origin_data.requirements            = [];
                this.origin_data.annexeFile              = [
                    {
                        annexe: '',
                        archive: -1,
                        name: '',
                        validateText: '',
                        validateFile: ''
                    }
                ],
                this.origin_data.imageListAnnexes        = [],
                this.clearForm();
            },
            getDependencies(id) {
                axios.get(`${this.ruta}/documentDependencyDestination?id=${id}&selected_id=${this.origin_data.selectedDependency.id}`)
                    .then((response) => {
                        this.origin_data.dependencies = response.data.dependencies;
                    })
                    .catch((error) => {
                        console.log(error);
                    })
            },
            getReception(id) {
                axios.get(`${this.ruta}/documentDependencyReception?id=${id}`)
                    .then((response) => {
                        this.origin_data.reception = response.data.receptionthis
                    })
                    .catch((error) => {
                        console.log(error);
                    })
            },
            getDocumentToSent() {
                axios.get(`${this.ruta}/documentToSent`)
                    .then((response) => {
                        this.arrayDocumentTypes = response.data.document_type;
                    })
                    .catch((error) => {
                        console.log(error);
                    })
            },
            printPdf(id) {
                window.open(`${this.ruta}/document/reportDocument/${id}`, '_blank')
            }
        },
        watch: {
            auth() {
                this.document_process_origin_id = 1;
                this.origin_data.dependency_shipping_id = this.origin_data.selectedDependency.id;
                this.getDependencies(this.entity_id)
            },
            dependency() {
                this.getDependencies(this.origin_data.selectedEntityProvided.id)
            },
            selectedDependency() {
                this.getDependencies(this.origin_data.selectedEntityProvided.id)
            },
            reception() {
                this.origin_data.person_reception = this.origin_data.reception.id
            }
        },
        mounted() {
            this.getProcess(1, this.pending_change_id, this.document_change_id);
            this.getDateNow();
            this.getDocumentToSent()
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
        max-width: 80%;
    }
    .show{
        display: list-item !important;
        opacity: 1 !important;
        overflow: auto !important;
        background-color: #3c29297a !important;
    }
</style>