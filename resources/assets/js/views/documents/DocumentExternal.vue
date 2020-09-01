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
                        <h6><i class="icon-doc"/> NUEVO DOCUMENTO</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-primary">DETALLES DEL DOCUMENTO</div>
                                    <div class="card-body">
                                        <document-generate
                                            @detail="setDetail($event)"
                                            :origin_data="origin_data"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-1 m-md-auto">
                                <div class="input-group">
                                    <button type="submit" @click="setDocumentDestination()" class="btn btn-primary btn-lg">
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
    import DocumentGenerate from '../../components/documents/DocumentGenerate';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        components: {
            DocumentGenerate,
            Loading
        },
        props : ['ruta'],
        data(){
            return{
                document_origin_id: 3,
                entity_id: 0,
                entity: [],
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
                    arrayDependencies: [],
                    actions: [],
                    shipping: [],
                    states: [],
                    arrayTypeReceptions: [],
                    arrayDependencyTypes: [],
                    destinations: [],
                    destination: '',
                    type_reception_id: 1,
                    dependency_type_id: 2,
                    detail_document_type_id: 1,
                    document_priority_id: 2,
                    route: this.ruta,
                    dependencies: [],
                    dependencyEntity: [],
                    arrayDocumentTypes: [],
                    selectedDependencyEntity: {},
                    selectedDependency: {},
                    selectedDependencyProvided: {id: 0},
                    selectedProvided: {
                        id: 2,
                        label: 'Elija un proveído'
                    },
                    selectedDocumentType: {
                        id: 0,
                        label: 'Elija un documento'
                    },
                    selectedEntityProvided: {
                        id: 0,
                        label: 'Elija una entidad'
                    },
                    errors: [],
                    references: '',
                    query: '',
                    document_tupa: 1,
                    tupa:0,
                    auth: 0,
                    procedures: [],
                    countDocument: 0,
                    transparency: false,
                    have_tupa: 1,
                },
                dependency_charge: {
                    fullName: '',
                    charge: false
                },
                errors: [],
                auth: 0,
                dependency_id: 0,
                dependency_process_id: 0,
                dependency_type_id: 0,
                dependency_shipping_id: 0,
                person_transmitter: 0,
                person_id: 0,
                responseRecords: 0,
                responseRegistry: 0,
                isLoading: false,
                fullPage: true
            }
        },
        computed:{
            procedures()
            {
                return this.origin_data.procedures
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
                    me.dependency_process_id               = response.data.dependency_process_id;
                    me.dependency_type_id                  = response.data.dependency_type_id;
                    me.person_id                           = response.data.person_id;
                    me.origin_data.detailDocumentTypes     = response.data.details;
                    me.origin_data.provided                = response.data.provided;
                    me.origin_data.arrayDocumentPriorities = response.data.priorities;
                    me.dependency_id                       = response.data.dependency;
                    me.auth                                = response.data.auth;
                    me.origin_data.auth                    = response.data.auth;
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
                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 5000);

                this.isLoading = true;

                let me = this;
                axios.post(this.ruta + '/documents/register',{
                    'affair'                 : this.origin_data.affair,
                    'description'            : this.origin_data.detail,
                    'date'                   : this.origin_data.shipping_date,
                    'document_origin_id'     : this.document_origin_id,
                    'document_priority_id'   : this.origin_data.document_priority_id,
                    'document_type_id'       : this.origin_data.selectedDocumentType.id,
                    'type_reception_id'      : this.origin_data.type_reception_id,
                    'dependency_shipping_id' : this.dependency_shipping_id,
                    'type'                   : this.origin_data.selectedDocumentType.label,
                    'destinations'           : this.origin_data.destinations,
                    'shipping'               : this.origin_data.shipping,
                    'records'                : this.origin_data.references.records,
                    'folio'                  : this.origin_data.folio,
                    'dependency_type_id'     : this.dependency_type_id,
                    'detail_document_type_id': this.origin_data.detail_document_type_id,
                    'states'                 : this.origin_data.states,
                    'requirements'           : this.origin_data.requirements,
                    'actions'                : this.origin_data.actions,
                    'tupa'                   : this.origin_data.tupa,
                    'transparency'           : this.origin_data.transparency,
                    'person_id'              : 0,
                    'person_reception'       : this.person_transmitter,
                    'annexes'                : this.origin_data.annexes,
                }).then((res) => {
                    setTimeout(() => {
                        this.isLoading = false
                    },5000);
                    me.clearForm();
                    me.responseRecords = res.data.records;
                    me.responseRegistry = res.data.registry;
                    Swal.fire("Expediente: " + me.responseRecords + "\nRegistro: " + me.responseRegistry);
                }, function (error) {
                    me.isLoading = false;
                    me.errors = error.response.data.errors;
                    me.origin_data.errors = error.response.data.errors
                });
            },
            setDocumentDestination()
            {
                if (this.origin_data.procedures.length && this.origin_data.query !== '') {
                    var dependency_id  = this.origin_data.procedures[0]['dependency_id'];
                    var url = this.origin_data.route + '/personReception?id=' + dependency_id;
                    axios.get(url)
                    .then((res) => {
                        this.person_transmitter = res.data.person_id;
                    });
                } else {
                    var  dependency_id = this.dependency_process_id;
                    this.person_transmitter = this.person_id
                }

                let me = this;
                var url = this.origin_data.route + '/documentDestination';
                axios.post(url, {
                    'dependency_id'          : dependency_id,
                    'dependency_shipping_id' : this.dependency_shipping_id,
                    'provided_id'            : 1,
                    'document_type_id'       : this.origin_data.selectedDocumentType.id,
                    'type'                   : this.origin_data.selectedDocumentType.label,
                    'detail_document_type_id': this.origin_data.detail_document_type_id,
                    'destinations'           : this.origin_data.destinations,
                    'actions'                : this.origin_data.actions
                }).then((res) => {
                    me.origin_data.destinations = res.data.destinations;
                    me.origin_data.actions      = res.data.provided;
                    me.origin_data.shipping     = res.data.shipping;
                    this.registerDocument();
                })
                .catch(function (error) {
                    console.log(error);
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
                    this.origin_data.references = res.data.references;
                })
            },
            selectedProcedures(id)
            {
                var url = this.origin_data.route + '/documentProcedureQuery/' + id;
                axios.get(url)
                .then((res) => {
                    this.origin_data.requirements = res.data.requirements;
                });
            }
        },
        watch: {
            auth() {
                this.origin_data.selectedDependency.id   = this.dependency_id;
                this.dependency_shipping_id              = this.dependency_id;
                this.origin_data.selectedDocumentType.id = 1;
            }
        },
        mounted() {
            this.getDateNow();
        }
    }
</script>

