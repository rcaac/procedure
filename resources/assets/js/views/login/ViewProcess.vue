<template>
    <main class="main">
        <ol/>
        <div class="container">
            <div class="vld-parent">
                <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-screen-desktop"/> MESA DE PARTES VIRTUAL
                    </div>
                    <div class="card-body">
                        <div class="row mb-2 mt-1">
                            <div class="col-md-4 offset-md-1">
                                <h5>Información Personal</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 offset-md-1">
                                <label>Doc. de Identidad</label>
                                <div class="form-group">
                                    <select v-model="identification_document_id" class="form-control">
                                        <option
                                            v-for="identificationDocument in arrayIdentification"
                                            :key="identificationDocument.id"
                                            :value="identificationDocument.id"
                                            v-text="identificationDocument.document"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label>Dni:</label>
                                <div class="form-group">
                                    <input
                                        type="text"
                                        v-model="dni"
                                        class="form-control"
                                        :class="{'is-invalid' : errors && errors.dni}"
                                        placeholder="Escriba su dni"
                                    >
                                    <em v-if="errors && errors.dni" class="invalid-feedback">{{ errors.dni[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 offset-md-1">
                                <label>Apellidos:</label>
                                <div class="form-group">
                                    <input type="text" v-model="firstName" class="form-control" :class="{'is-invalid' : errors && errors.firstName}" placeholder="Escriba sus apellidos">
                                    <em v-if="errors && errors.firstName" class="invalid-feedback">{{ errors.firstName[0] }}</em>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label>Nombres:</label>
                                <div class="form-group">
                                    <input type="text" v-model="lastName" class="form-control" :class="{'is-invalid' : errors && errors.lastName}" placeholder="Escriba sus nombres">
                                    <em v-if="errors && errors.lastName" class="invalid-feedback">{{ errors.lastName[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 offset-md-1">
                                <label>Teléfono:</label>
                                <div class="form-group">
                                    <input type="text" v-model="phone" class="form-control" :class="{'is-invalid' : errors && errors.phone}" placeholder="Escriba su teléfono">
                                    <em v-if="errors && errors.phone" class="invalid-feedback">{{ errors.phone[0] }}</em>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label>Correo Electrónico:</label>
                                <div class="form-group">
                                    <input type="text" v-model="email" class="form-control" :class="{'is-invalid' : errors && errors.email}" placeholder="Escriba su correo electrónico">
                                    <em v-if="errors && errors.email" class="invalid-feedback">{{ errors.email[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 offset-md-1">
                                <label>Dirección:</label>
                                <div class="form-group">
                                    <input type="text" v-model="direction" class="form-control" :class="{'is-invalid' : errors && errors.direction}" placeholder="Escriba su dirección">
                                    <em v-if="errors && errors.direction" class="invalid-feedback">{{ errors.direction[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2 mt-2">
                            <div class="col-md-4 offset-md-1">
                                <h5>Información del Documento</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 offset-md-1">
                                <label>Tipo de Documento:</label>
                                <div class="form-group">
                                    <v-select
                                        v-model="selectedDocumentType"
                                        :options="arrayDocumentTypes"
                                        :clearable="false"
                                        :get-option-label="getLabel"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-1">
                                <label>Asunto:</label>
                                <div class="form-group">
                                    <textarea
                                        v-model="origin_data.affair"
                                        rows="1"
                                        class="form-control"
                                        placeholder="Escriba o seleccione un asunto"
                                        :disabled="this.tupa === 1"
                                        :class="{'is-invalid' : errors && errors.affair}"
                                    >
                                    </textarea>
                                    <em v-if="errors && errors.affair" class="invalid-feedback">{{ errors.affair[0] }}</em>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="input-group">
                                    <button
                                        type="submit"
                                        @click="openModal()"
                                        :class="{ 'btn btn-primary btn': this.tupa===0, 'btn btn-success btn': this.tupa===1 }"
                                    >
                                        <i class="icon-plus" v-if="this.tupa === 0"/>
                                        <i class="icon-check" v-if="this.tupa === 1"/>
                                        Trámite Tupa
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-1">
                                <label>Contenido:</label>
                                <div class="form-group">
                                    <textarea
                                        v-model="detail"
                                        rows="2"
                                        class="form-control"
                                        placeholder="Ingrese contenido del documento"
                                        :class="{'is-invalid' : errors && errors.description}"
                                    >
                                    </textarea>
                                    <em v-if="errors && errors.description" class="invalid-feedback">{{ errors.description[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <template v-for="(item,index) in annexes">
                                <div class="col-md-4 offset-md-1">
                                    <label v-if="index === 0">Anexos:</label>
                                    <div class="form-group">
                                        <textarea
                                            rows="1"
                                            v-model="item.annexe"
                                            class="form-control"
                                            :placeholder="'Ingrese la descripción del anexo '+(index+1)"
                                            :class="{'is-invalid' : item.validateText}"
                                        ></textarea>
                                        <em class="invalid-feedback" v-if="item.validateText">{{item.validateText}}</em>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label v-if="index === 0">&nbsp;</label>
                                    <label>
                                        <a class="btn btn-secondary">
                                            <i class="icon-cloud-upload" v-if="item.archive === -1"/>
                                            <i class="icon-check" style="color: #00a67c" v-else/>
                                            Cargar su archivo
                                        </a>
                                        <input
                                            ref="imageUploader"
                                            type="file"
                                            name="annexe"
                                            accept="image/*, application/pdf"
                                            @change="updateImageListAnnexes($event, index)"
                                            style="display: none;"
                                        />
                                        <em class="text-danger" v-if="item.validateFile">{{item.validateFile}}</em>
                                    </label>
                                </div>
                                <div class="col-md-1" v-if="index > 0">
                                    <label v-if="index === 0">&nbsp;</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-danger" style="color: white" @click="remove(index)" v-show="index || ( !index && annexes.length > 1)">
                                            <i class="icon-close"></i> Anexo
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label v-if="index === 0">&nbsp;</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-primary" style="color: white" @click="add(index)" v-show="index === annexes.length-1">
                                            <i class="icon-plus"></i> Anexo
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2"/>
                            </template>
                        </div>
                        <div class="row">
                            <div class="col-md-2 offset-md-1">
                                <label>Folios:</label>
                                <div class="form-group">
                                    <input
                                        v-model="folio"
                                        class="form-control"
                                        placeholder="Folios"
                                        :class="{'is-invalid' : errors && errors.folio}"
                                    >
                                    <em v-if="errors && errors.folio" class="invalid-feedback">{{ errors.folio[0] }}</em>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            v-model="agree"
                                            :true-value="1"
                                            :false-value="0"
                                        >
                                        <span>
                                            Declaro, bajo mi responsabilidad, que los datos ingresados en este formulario son verdaderos
                                            y autorizo a la Municipalidad a utilizarlos para los fines consignados en la presente solicitud.
                                        </span>
                                    </label>
                                </div>
                                <em v-if="errors && errors.agree" class="text-danger">{{ errors.agree[0] }}</em>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-sm-6 col-md-1 m-md-auto">
                            <button
                                type="button"
                                @click="setDocumentDestination()"
                                class="btn btn-primary btn-lg"
                            >
                                <i class="fa fa-send-o"/> ENVIAR
                            </button>
                        </div>
                        <br>
                        <div class="col-sm-6 col-md-1 m-md-auto">
                            <button
                                type="button"
                                class="btn btn-link btn-lg"
                                @click="login()"
                            >
                                <i class="fa fa-reply"/> ATRÁS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <component
            :is="modalType"
            :ruta="ruta"
            :origin_data="origin_data"
            @closeModal="closeModal"
        />
    </main>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import vSelect from "vue-select"
    import ModalRequirementSearch from '../../components/modals/ModalRequirementSearch';

    export default {
        components: {
            Loading,
            vSelect,
            ModalRequirementSearch
        },
        props : ['ruta'],
        data(){
            return{
                content: null,
                modal: 0,
                titleModal: '',
                typeAction: 0,
                dependency_type_id: 0,
                arrayDocumentOrigin: [],
                firstName: '',
                lastName: '',
                dni: '',
                phone: '',
                direction: '',
                email: '',
                search: '',
                archive: '',
                folio: '',
                detail: '',
                destination: '',
                type_reception_id: 1,
                detail_document_type_id: 0,
                document_priority_id: 2,
                route: this.ruta,
                dependencies: [],
                dependencyEntity: [],
                arrayDocumentTypes: [],
                selectedDependencyEntity: {},
                selectedDocumentType: {id: 1, type: 'SOLICITUD'},
                selectedDependency: {id: 0},
                reference: '',
                ArrayReferences: '',
                document_tupa: 1,
                tupa:0,
                errors: [],
                isLoading: false,
                fullPage: true,
                person_transmitter: 0,
                person_id: 0,
                transparency: false,
                actions: [],
                shipping: [],
                destinations: [],
                dependency_shipping_id: 0,
                arrayIdentification: [],
                identification_document_id: 1,
                countDocument: 0,
                origin_data: {
                    requirements: [],
                    procedures: [],
                    idCode: [],
                    codeRequirement: [],
                    imageList: [],
                    idImage: [],
                    requirementArchive: [],
                    affair: '',
                    query: '',
                },
                user: [],
                personDate: '',
                responseRecords: 0,
                responseRegistry: 0,
                imageListAnnexes: [],
                agree: 0,
                validateImage: '',
                annexes: [
                        {
                            annexe: '',
                            archive: -1,
                            validateText: '',
                            validateFile: ''
                        }
                    ],
                fileRequirement: [
                    {
                        archive: -1
                    }
                ],
                showModal: 0
            }
        },
        computed: {
            modalType() {
                switch(this.showModal){
                    case 1: {
                        return 'modal-requirement-search'
                    }
                }
            },
        },
        methods: {
            openModal () {
                this.showModal = 1;
            },
            closeModal () {
                this.showModal = 0;
                if (this.origin_data.affair !== '') {
                    this.tupa = 1;
                }else {
                    this.tupa = 0;
                    this.origin_data.affair = '';
                }
            },
            setDocumentDestination () {
                let dependency_id = this.origin_data.procedures.length ? this.origin_data.procedures[0]['dependency_id'] : 13;

                axios.post(`${this.route}/documentDestinationExternal`, {
                    'dependency_id'             : dependency_id,
                    'dependency_shipping_id'    : 46,
                    'provided_id'               : 1,
                    'document_type_id'          : this.selectedDocumentType.id,
                    'type'                      : this.selectedDocumentType.type,
                    'detail_document_type_id'   : 1,
                    'destinations'              : this.destinations,
                    'actions'                   : this.actions,
                    'countDocument'             : this.countDocument
                }).then((response) => {
                    this.destinations = response.data.destinations;
                    this.actions      = response.data.provided;
                    this.shipping     = response.data.shipping;
                    this.registerDocument();
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            registerDocument () {
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
                    'annexes': this.annexes
                });
                data.append('annexes', annexes);

                $.each(this.imageListAnnexes, function (key, image) {
                data.append(`imageAnnexes[${key}]`, image);
                });

                let destinations = JSON.stringify({
                    'destinations': this.destinations
                });
                data.append('destinations', destinations);

                let requirements = JSON.stringify({                    
                    'requirements': this.origin_data.requirements
                });
                data.append('requirements', requirements);

                let actions = JSON.stringify({
                    'actions': this.actions
                });
                data.append('actions', actions);

                let shipping = JSON.stringify({
                    'shipping': this.shipping
                });
                data.append('shipping', shipping);

                data.append('affair'                    , this.origin_data.affair);
                data.append('description'               , this.detail);  
                data.append('document_type_id'          , this.selectedDocumentType.id);
                data.append('type'                      , this.selectedDocumentType.type);
                data.append('dependency_shipping_id'    , '46');
                data.append('folio'                     , this.folio);
                data.append('tupa'                      , this.tupa);
                data.append('reference'                 , '');
                data.append('firstName'                 , this.firstName);
                data.append('lastName'                  , this.lastName);
                data.append('dni'                       , this.dni);
                data.append('phone'                     , this.phone);
                data.append('direction'                 , this.direction);
                data.append('identification_document_id', this.identification_document_id);
                data.append('code'                      , this.code);
                data.append('agree'                     , this.agree);
                data.append('email'                     , this.email);                

                axios.post(`${this.ruta}/documents/external`, data)
                .then((response) => {
                    setTimeout(() => {
                        this.isLoading = false
                    },5000);
                    this.responseRecords  = response.data.records;
                    this.responseRegistry = response.data.registry;
                    Swal.fire({
                        icon: 'success',
                        title: "Expediente: " + this.responseRecords + "\nRegistro: " + this.responseRegistry,
                        text: 'La operación se realizó con éxito, estaremos en contacto con usted al correo que registró, si el dni o ruc no corresponde al trámite será anulado automáticamente',
                        footer: '<a href='+`${this.ruta}/document/reportExternal/${response.data.id}`+'>Descarga tu documento</a>'
                    });
                    this.clearForm();
                }, (error) => {
                    this.isLoading = false;
                    this.errors = error.response.data.errors;
                });
            },
            clearForm () {
                Object.assign(this.$data, this.$options.data.apply(this));
                this.identification();
                this.getDocumentTypes();
            },
            selectedQuery () {
                if (this.query !== '') {
                    this.affair = this.query;
                    this.tupa = 1;
                    this.closeModal();
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            identification () {
                axios.get(`${this.ruta}/identification`)
                    .then((response) => {
                    this.arrayIdentification = response.data.identification;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getDocumentTypes () {
                axios.get(`${this.ruta}/documentTypeExternal`)
                    .then((response) => {
                    this.arrayDocumentTypes = response.data.document_type;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            getLabel (option) {
                return option.type
            },
            printPdf(id)
            {
                window.open(`${this.ruta}/document/reportExternal/${id}`, '_blank');
            },
            updateImageListAnnexes(e, index)
            {
                if (!e.target.files.length) return;
                let file = e.target.files[0];
                this.imageListAnnexes.push(file);
                this.annexes[index].archive = index;
            },
            login()
            {
                location.href = '/login';
            },
            add(index) {
                if (this.annexes[index].annexe !== '' && this.imageListAnnexes.length > index) {
                    this.annexes.push({ annexe: '', archive: -1, validateText: '', validateFile: ''});
                }else {
                    if (this.annexes[index].annexe === '') {
                        this.annexes[index].validateText = 'Debe ingresar la descripción del anexo';
                    }
                    if (this.imageListAnnexes.length === index) {
                        this.annexes[index].validateFile = 'Debe Seleccionar un archivo'
                    }
                }
            },
            remove(index) {
                this.annexes.splice(index, 1);
                this.imageListAnnexes.splice(index, 1);
            },                       
        },
        watch: {
            dni (query) {
                if (query.toString().length === 8) {
                    axios.get(`${this.ruta}/showPersonDate/${query}`)
                        .then((response) => {
                        this.personDate = response.data.personDate;
                        if (this.personDate !== null) {
                            this.firstName = this.personDate.firstName;
                            this.lastName  = this.personDate.lastName;
                            this.phone     = this.personDate.phone;
                            this.direction = this.personDate.direction;
                            this.email     = this.personDate.email
                        } else {
                            this.firstName = '';
                            this.lastName  = '';
                            this.phone     = '';
                            this.direction = '';
                            this.email     = ''
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                }else {
                    this.firstName = '';
                    this.lastName  = '';
                    this.phone     = '';
                    this.direction = ''
                }
            }
        },
        mounted () {
            this.identification();
            this.getDocumentTypes()
        }
    }

</script>