<template>
    <main class="main">
        <ol/>
        <div class="container">
            <div class="vld-parent">
                <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
            </div>
            <div id="ui-view">
                <div class="card">
                    <div class="card-header">
                        <h6><i class="icon-doc"/> NUEVO DOCUMENTO</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">REFERENCIA DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-reference @queryReference="queryReference($event)" :origin_data="origin_data"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DETALLES DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-generate @detail="setDetail($event)" :origin_data="origin_data"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 col-md-12 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DESTINO DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-destination
                                            @getDependencies="getDependencies($event)"
                                            :origin_data="origin_data"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6 col-md-1 m-md-auto">
                                <div class="input-group">
                                    <button type="submit" @click="registerDocument()" class="btn btn-primary btn-lg">
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
                document_origin_id: 0,
                arrayDocumentOrigin: [],
                origin_data: {
                    search: '',
                    shipping_date: '',
                    archive: '',
                    affair: '',
                    folio: '',
                    detail: '',
                    annexes: '',
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
                    dependency_type_id: 0,
                    detail_document_type_id: 0,
                    document_priority_id: 2,
                    route: this.ruta,
                    dependencies: [],
                    procedures: [],
                    dependencyEntity: [],
                    selectedDependencyEntity: {},
                    selectedDependency: {},
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
                    records: 0,
                    registry: '',
                    dependency_charge: {},
                    person_id: 0,
                    transparency: false,
                    dependency_shipping_id: 0,
                    users: [],
                    have_tupa: 1,
                    entities: [],
                    provided: [],
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
                    number: '',
                    append: false,
                },
                dependency_charge: {
                    fullName: '',
                    charge: false
                },
                errors: [],
                auth: 0,
                dependency_type_id: 0,
                responseRecords: 0,
                responseRegistry: 0,
                dependency_id: 0,
                entity_id: 0,
                isLoading: false,
                fullPage: true
            }
        },
        computed:{
            documentType(){
                switch(this.document_origin_id){
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
            selectedDependency()
            {
                return this.origin_data.selectedDependency
            },
            selectedUser(){
                return this.origin_data.selectedUser
            },
            dependencyEntity()
            {
                return this.origin_data.selectedDependencyEntity
            }
        },
        methods: {
            getDateNow() {
                let me=this;
                var url= me.ruta + '/documents';
                axios.get(url).then(function (response) {
                    me.arrayDocumentOrigin                 = response.data.origin;
                    me.origin_data.arrayTypeReceptions     = response.data.type_reception;
                    me.origin_data.arrayDependencyTypes    = response.data.dependency_type;
                    me.origin_data.shipping_date           = response.data.date;
                    me.dependency_type_id                  = response.data.dependency_type_id;
                    me.origin_data.detailDocumentTypes     = response.data.details;
                    me.origin_data.provided                = response.data.provided;
                    me.origin_data.arrayDocumentPriorities = response.data.priorities;
                    me.auth                                = response.data.auth;
                    me.dependency_id                       = response.data.dependency;
                    me.entity_id                           = response.data.entity;
                    me.origin_data.entity_external         = response.data.entity_external;
                    me.origin_data.entities                = response.data.entities;

                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            listDependencyCharge(id)
            {
                this.dependency_charge = {};

                let me=this;
                var url= this.ruta + '/dependencyCharge/' + id;
                axios.get(url).then((response) => {
                    me.dependency_charge = response.data.dependency_charge;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            setDetail(id)
            {
                this.origin_data.detail_document_type_id = id;
            },
            registerDocument()
            {
                this.isLoading = true;

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
                data.append('document_origin_id'        ,this.document_origin_id);
                data.append('document_priority_id'      ,this.origin_data.document_priority_id);
                data.append('document_type_id'          ,this.origin_data.selectedDocumentType.id);
                data.append('type_reception_id'         ,this.origin_data.type_reception_id);
                data.append('type'                      ,this.origin_data.selectedDocumentType.label);
                data.append('dependency_shipping_id'    ,this.origin_data.dependency_shipping_id);
                data.append('folio'                     ,this.origin_data.folio);
                data.append('detail_document_type_id'   ,this.origin_data.detail_document_type_id);
                data.append('tupa'                      ,this.origin_data.tupa);
                data.append('reference'                 ,this.origin_data.reference);
                data.append('records'                   ,this.origin_data.records);
                data.append('person_id'                 ,this.origin_data.person_id);
                data.append('person_reception'          ,this.origin_data.person_reception);
                data.append('transparency'              ,this.origin_data.transparency);
                data.append('number'                    ,this.origin_data.number);
                data.append('append'                    ,this.origin_data.append);

                axios.post(`${this.ruta}/documents/register`,data)
                    .then((response) => {
                        setTimeout(() => {
                            this.isLoading = false
                        },5000);
                        this.clearForm();
                        this.arrayDocumentTypes     = [];
                        this.origin_data.query      = '';
                        this.origin_data.references = '';
                        this.responseRecords = response.data.records;
                        this.responseRegistry = response.data.registry;
                        Swal.fire({
                            icon: 'success',
                            title: "Expediente: " + this.responseRecords + "\nRegistro: " + this.responseRegistry,
                            text: 'La operación se realizó con éxito',
                            footer: '<a href='+`${this.ruta}/document/reportExternal/${response.data.id}`+'>Descarga tu documento</a>'
                        });
                }, (error) => {
                    this.isLoading = false;
                    this.errors = error.response.data.errors;
                    this.origin_data.errors = error.response.data.errors
                });
            },
            clearForm()
            {
                Object.assign(this.$data, this.$options.data.apply(this));
                this.getDateNow();
            },
            queryReference(queryRef)
            {
                var url = this.origin_data.route + '/documentReferences?queryRef=' + queryRef;
                axios.get(url)
                .then((res) => {
                    this.origin_data.ArrayReferences = res.data.references;
                    this.origin_data.reference         = this.origin_data.ArrayReferences.records;
                })
            },
            selectedProcedures(id)
            {
                var url = this.origin_data.route + '/documentProcedureQuery/' + id;
                axios.get(url)
                .then((res) => {
                    this.origin_data.requirements = res.data.requirements;
                });
            },
            getDependencies(id)
            {
                let me=this;
                var url = me.ruta + '/documentDependencyDestination?id=' + id + '&selected_id=' + this.origin_data.selectedDependency.id;
                axios.get(url)
                .then((res) => {
                    me.origin_data.dependencies = res.data.dependencies;
                })
            }
        },
        watch: {
            auth() {
                this.document_origin_id = 1;
                this.origin_data.dependency_shipping_id = this.dependency_id;
                if (this.auth !== 0) {
                    this.origin_data.selectedDependency.id  = this.dependency_id;
                    this.getDependencies(this.origin_data.selectedEntityProvided.id)
                }

            }
        },
        mounted() {
            this.getDateNow();
        }
    }

</script>

