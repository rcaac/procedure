<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="vld-parent">
                <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
            </div>
            <div id="ui-view">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-doc"/> NUEVO DOCUMENTO
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="col-sm-12 col-md-11 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary ">REFERENCIA DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-reference
                                            @queryReference="queryReference($event)"
                                            @add="addReference($event)"
                                            @remove="removeReference($event)"
                                            :origin_data="origin_data"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-11 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">EMISOR DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <label class="col-md-2 form-control-label">Orígen:</label>
                                            <div class="col-md-4">
                                                <select
                                                    v-model="origin_data.document_origin_id"
                                                    class="form-control form-control-sm"
                                                >
                                                    <option value="0" disabled>Elije Orígen del Documento</option>
                                                    <option
                                                        v-for="origin in arrayDocumentOrigin"
                                                        :key="origin.id"
                                                        :value="origin.id"
                                                        v-text="origin.origin">
                                                    </option>
                                                </select>
                                                <div v-if="origin_data.errors && origin_data.errors.document_origin_id" class="text-danger">{{ origin_data.errors.document_origin_id[0] }}</div>
                                            </div>
                                        </div>
                                        <component
                                            :is="documentType"
                                            :origin_data="origin_data"
                                            @dependencyCharge="listDependencyCharge($event)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-11 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DETALLES DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-generate @detail="setDetail($event)" :origin_data="origin_data"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-11 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DESTINO DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-destination @getDependencies="getDependencies($event)" :origin_data="origin_data"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 col-md-1 m-md-auto">
                                <div class="input-group">
                                    <button
                                        type="button"
                                        @click.prevent="registerDocument()"
                                        class="btn btn-primary btn-lg"
                                    >
                                        <i class="fa fa-send-o"/> ENVIAR
                                    </button>
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
    import DocumentOriginInternal from '../../components/documents/DocumentOriginInternal';
    import DocumentOriginExternal from '../../components/documents/DocumentOriginExternal';
    import DocumentOriginCitizen from '../../components/documents/DocumentOriginCitizen';
    import DocumentGenerate from '../../components/documents/DocumentGenerate';
    import DocumentReference from '../../components/documents/DocumentReference';
    import DocumentDestination from '../../components/documents/DocumentDestination';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        components: {
            DocumentOriginInternal,
            DocumentOriginExternal,
            DocumentOriginCitizen,
            DocumentGenerate,
            DocumentDestination,
            DocumentReference,
            Loading
        },
        props : ['ruta'],
        data(){
            return{
                dependency_type_id: 0,
                arrayDocumentOrigin: [],
                origin_data: {
                    document_origin_id: 0,
                    search: '',
                    shipping_date: '',
                    archive: '',
                    affair: '',
                    folio: '',
                    detail: '',
                    annexes: '',
                    requirements: [],
                    selectedUser: {},
                    selectedCitizen: [],
                    arrayDependencies: [],
                    actions: [],
                    shipping: [],
                    arrayTypeReceptions: [],
                    arrayDependencyTypes: [],
                    destinations: [],
                    destination: '',
                    type_reception_id: 1,
                    dependency_type_id: 0,
                    detail_document_type_id: 0,
                    document_priority_id: 2,
                    route: this.ruta,
                    dependencies: [],
                    procedures: [],
                    dependencyEntity: [],
                    arrayDocumentTypes: [],
                    selectedDependencyEntity: {},
                    selectedDependency: {id: 0},
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
                        label: 'Elija un documento'
                    },
                    selectedEntityProvided: {
                        id: 5,
                        label: 'MUNICIPALIDAD DISTRITAL DE PILLCO MARCA'
                    },
                    errors: [],
                    reference: '',
                    ArrayReferences: '',
                    query: '',
                    document_tupa: 1,
                    tupa:0,
                    countDocument: 0,
                    editDocument: 0,
                    records: 0,
                    registry: '',
                    dependency_charge: {},
                    person_id: 0,
                    transparency: false,
                    dependency_shipping_id: 0,
                    users: [],
                    have_tupa: 1,
                    detailDocumentTypes: [],
                    arrayDocumentPriorities: [],
                    entities: [],
                    provided: [],
                    entity_external: [],
                    number: '',
                    append: false,
                    attaching: [],
                    viewProcess: 0,
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
                    idCode: [],
                    codeRequirement: [],
                    imageList: [],
                    idImage: [],
                    requirementArchive: {}
                },
                errors: [],
                auth: 0,
                dependency_id: 0,
                entity_id: 0,
                user: 1,
                responseRecords: 0,
                responseRegistry: 0,
                responseCode: 0,
                isLoading: false,
                fullPage: true,
                connectionStatus: ''
            }
        },
        computed:{
            documentType() {
                switch(this.origin_data.document_origin_id){
                    case 1: {
                        return 'document-origin-internal'
                    }
                    case 2: {
                        return 'document-origin-external'
                    }
                    case 3: {
                        return 'document-origin-citizen'
                    }
                }
            },
            selectedDependency() {
                return this.origin_data.selectedDependency
            },
            selectedUser() {
                return this.origin_data.selectedUser
            },
            dependencyCharge() {
                return this.origin_data.dependency_charge
            },
            users() {
                return this.origin_data.users
            },
            documentOrigin() {
                return this.origin_data.document_origin_id
            }
        },
        methods: {
            getDateNow() {
                axios.get(`${this.ruta}/documents`)
                    .then((response) => {
                        this.arrayDocumentOrigin                 = response.data.origin;
                        this.origin_data.arrayTypeReceptions     = response.data.type_reception;
                        this.origin_data.arrayDependencyTypes    = response.data.dependency_type;
                        this.origin_data.shipping_date           = response.data.date;
                        this.origin_data.detailDocumentTypes     = response.data.details;
                        this.origin_data.arrayDocumentPriorities = response.data.priorities;
                        this.origin_data.entities                = response.data.entities;
                        this.origin_data.provided                = response.data.provided;
                        if (this.origin_data.document_origin_id === 0)
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
            listDependencyCharge(id) {
                axios.get(`${this.ruta}/dependencyCharge/${id}`)
                    .then((response) => {
                    this.origin_data.dependency_charge = response.data.dependency_charge;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            setDetail(id) {
                this.origin_data.detail_document_type_id = id;
            },
            registerDocument() {
                this.isLoading = true;

                if (this.user === 1) {
                    this.origin_data.document_origin_id = 1
                }

                let data = new FormData();

                $.each(this.origin_data.idImage, function (key, id) {
                    data.append(`idImage[${key}]`, id);
                });

                $.each(this.origin_data.imageList, function (key, image) {
                    data.append(`images[${key}]`, image);
                });

                $.each(this.origin_data.idCode, function (key, id) {
                    data.append(`idCode[${key}]`, id);
                });

                $.each(this.origin_data.codeRequirement, function (key, code) {
                    data.append(`codeRequirement[${key}]`, code);
                });

                let annexes = JSON.stringify({
                    'annexes': this.origin_data.annexeFile
                });

                data.append('annexes', annexes);

                $.each(this.origin_data.imageListAnnexes, function (key, image) {
                    data.append(`imageAnnexes[${key}]`, image);
                });

                let destinations = JSON.stringify({
                    'destinations': this.origin_data.destinations
                });

                data.append('destinations', destinations);

                let requirements = JSON.stringify({
                    'requirements': this.origin_data.requirements
                });

                data.append('requirements', requirements);

                let actions = JSON.stringify({
                    'actions': this.origin_data.actions
                });

                data.append('actions', actions);

                let shipping = JSON.stringify({
                    'shipping': this.origin_data.shipping
                });

                data.append('shipping', shipping);

                data.append('affair'                    ,this.origin_data.affair);
                data.append('description'               ,this.origin_data.detail);
                data.append('date'                      ,this.origin_data.shipping_date);
                data.append('document_origin_id'        ,this.origin_data.document_origin_id);
                data.append('document_priority_id'      ,this.origin_data.document_priority_id);
                data.append('document_type_id'          ,this.origin_data.selectedDocumentType.id);
                data.append('type_reception_id'         ,this.origin_data.type_reception_id);
                data.append('type'                      ,this.origin_data.selectedDocumentType.label);
                data.append('dependency_shipping_id'    ,this.origin_data.dependency_shipping_id);
                data.append('folio'                     ,this.origin_data.folio);
                data.append('detail_document_type_id'   ,this.origin_data.detail_document_type_id);
                data.append('tupa'                      ,this.origin_data.tupa);
                data.append('reference'                ,this.origin_data.attaching);
                data.append('records'                   ,this.origin_data.records);
                data.append('person_id'                 ,this.origin_data.person_id);
                data.append('person_reception'          ,this.origin_data.person_reception);
                data.append('transparency'              ,this.origin_data.transparency);
                data.append('number'                    ,this.origin_data.number);
                data.append('append'                    ,this.origin_data.append);

                axios.post(`${this.ruta}/documents/register`, data)
                    .then((response) => {
                        setTimeout(() => {
                            this.isLoading = false
                        },5000);
                        this.clearForm();
                        this.responseRecords = response.data.records;
                        this.responseRegistry = response.data.registry;
                        Swal.fire({
                            icon: 'success',
                            title: "Expediente: " + this.responseRecords + "\nRegistro: " + this.responseRegistry,
                            text: 'La operación se realizó con éxito',
                            footer: '<a href='+`${this.ruta}/document/reportExternal/${response.data.id}`+'>Descarga tu documento</a>'
                        });
                },(error) => {
                    this.isLoading = false;
                    this.origin_data.errors = error.response.data.errors
                });
            },
            queryReference(queryRef) {
                axios.get(`${this.origin_data.route}/documentReferences?queryRef=${queryRef}`)
                    .then((response) => {
                        this.origin_data.ArrayReferences = response.data.references;
                        this.origin_data.reference       = this.origin_data.ArrayReferences.records;
                    })
            },
            selectedProcedures(id) {
                axios.get(`${this.origin_data.route}/documentProcedureQuery/${id}`)
                    .then((res) => {
                    this.origin_data.requirements = res.data.requirements;
                });
            },
            clearForm() {
                this.origin_data.countDocument = 0;
                Object.assign(this.$data, this.$options.data.bind(
                    this.origin_data.destinations            = [],
                    this.origin_data.shipping                = [],
                    this.origin_data.actions                 = [],
                    this.origin_data.detail_document_type_id = 0,
                    this.origin_data.selectedDocumentType    = {id: 0, label: 'Elija un documento'},
                    this.origin_data.arrayDocumentTypes      = 0,
                    this.origin_data.transparency            = false,
                    this.origin_data.number                  = '',
                    this.origin_data.affair                  = '',
                    this.origin_data.detail                  = '',
                    this.origin_data.annexes                 = '',
                    this.origin_data.folio                   = '',
                    this.origin_data.errors                  = [],
                    this.origin_data.annexeFile= [
                        {
                            annexe: '',
                            archive: -1,
                            name: '',
                            validateText: '',
                            validateFile: ''
                        }
                    ],
                    this.origin_data.imageListAnnexes = [],
                    this.origin_data.idCode = [],
                    this.origin_data.codeRequirement = [],
                    this.origin_data.imageList = [],
                    this.origin_data.idImage = []
                ));
                this.getDateNow();
            },
            getDependencies(id) {
                axios.get(`${this.ruta}/documentDependencyDestination?id=${id}&selected_id=${this.origin_data.selectedDependency.id}`)
                    .then((response) => {
                        this.origin_data.dependencies = response.data.dependencies;
                    })
            },
            printPdf(id) {
                window.open(`${this.ruta}/document/reportDocument/${id}`, '_blank')
            },
            addReference(referency) {
                if (referency === '') return
                this.origin_data.attaching.push(referency);
                this.origin_data.registry = '';
            },
            removeReference(index) {
                this.origin_data.attaching.splice(index, 1);
            }
        },
        watch: {
            selectedUser()
            {
                if (Object.keys(this.origin_data.selectedUser).length)
                {
                    this.user                               = 0;
                    this.origin_data.person_id              = this.origin_data.selectedUser.person_id;
                    this.origin_data.dependency_shipping_id = this.origin_data.selectedUser.id;
                    this.origin_data.selectedDependency.id  = this.origin_data.selectedUser.id;
                    this.getDependencies(this.entity_id)
                }
            },
            selectedDependency()
            {
                this.user = 0;
                this.origin_data.dependency_shipping_id = this.origin_data.selectedDependency.id;
            },
            dependencyCharge()
            {
                if (Object.keys(this.origin_data.dependency_charge).length)
                {
                    this.user                  = 0;
                    this.origin_data.person_id = this.origin_data.dependency_charge.id;
                    this.getDependencies(this.entity_id)
                }
            },
            auth()
            {
                if (this.auth !== 0)
                {
                    this.getDependencies(this.entity_id);
                    this.origin_data.dependency_shipping_id = this.dependency_id;
                    this.user                               = 1;
                }
            },
            users()
            {
                if (this.origin_data.users.length)
                {
                    this.origin_data.person_reception = this.origin_data.users[0].person_id
                }
            },
            documentOrigin()
            {
                this.clearForm();
            }
        },
        mounted() {
            this.getDateNow();
        }
    }

</script>

