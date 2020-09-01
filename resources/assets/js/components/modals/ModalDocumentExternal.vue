<template>
    <div>
        <div class="modal show-document-sent fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Actualizar Documento</h4>
                        <button type="button" class="close" @click="$emit('closeModalDocument'), $emit('getSent', 1)" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="container col-md-12">
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Tipo de Documento:</label>
                                    <div class="col-md-4">
                                        <v-select
                                            v-model="selectedDocumentType"
                                            :options="arrayDocumentTypes"
                                            :clearable="false"
                                            class="small"
                                        />
                                        <div v-if="errors && errors.document_type_id" class="text-danger">{{ errors.document_type_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Número de Documento:</label>
                                    <div class="col-md-2">
                                        <input type="text" v-model="number" class="form-control form-control-sm">
                                        <div v-if="errors && errors.number" class="text-danger">{{ errors.number[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Dependencia Destino</label>
                                    <div class="col-md-7">
                                        <v-select
                                            v-model="selectedDependencyProvided"
                                            :options="dependencies"
                                            class="small"
                                            :clearable="false"
                                        />
                                        <em v-if="errors" class="text-danger">{{ errors }}</em>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Asunto de Documento:</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <textarea v-model="affair" rows="1" class="form-control form-control-sm" :disabled="this.tupa === 1" />
                                        </div>
                                        <div v-if="errors && errors.affair" class="text-danger">{{ errors.affair[0] }}</div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <button
                                                type="button"
                                                @click="showModalRequirement()"
                                                :class="{ 'btn btn-primary btn-sm': this.tupa === 0, 'btn btn-success btn-sm': this.tupa === 1 }"
                                            >
                                                <i class="icon-plus" v-if="this.tupa === 0"/>
                                                <i class="icon-check" v-if="this.tupa === 1"/>
                                                Trámite Tupa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Observaciones:</label>
                                    <div class="col-md-9">
                                        <el-tiptap
                                            v-model="detail"
                                            :extensions="extensions"
                                        />
                                        <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Anexos:</label>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <button
                                                type="button"
                                                @click="showModalAnnexe()"
                                                :class="{
                                                'btn btn-primary btn-sm': this.annexes === '0' || this.annexes === null || this.annexes !== null,
                                                'btn btn-success btn-sm': this.annexes=== '1'
                                                }"
                                            >
                                                <i :class="{
                                                'icon-plus': this.annexes === '0' || this.annexes === null || this.annexes !== null,
                                                'icon-check': this.annexes=== '1'
                                                }"/>
                                                Anexos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Proveído de Atención:</label>
                                    <div class="col-md-4">
                                        <v-select
                                            v-model="selectedProvided"
                                            :options="provided"
                                            class="small"
                                            :clearable="false"
                                        />
                                    </div>
                                    <div v-if="errors && errors.provided_id" class="text-danger">{{ errors.provided_id[0] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Número de Folios:</label>
                                    <div class="col-md-1">
                                        <input type="text" v-model="folio" class="form-control form-control-sm" placeholder="Folios">
                                        <div v-if="errors && errors.folio" class="text-danger">{{ errors.folio[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Prioridad:</label>
                                    <div class="col-md-2">
                                        <select v-model="document_priority_id" class="form-control form-control-sm">
                                            <option
                                                v-for="priority in arrayDocumentPriorities"
                                                :key="priority.id"
                                                :value="priority.id"
                                                v-text="priority.priority">
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Fecha:</label>
                                    <div class="col-md-4">
                                        <input type="date" v-model="shipping_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 form-control-label">Estado:</label>
                                    <div class="col-md-3">
                                        <select
                                            v-model="state_id"
                                            class="form-control form-control-sm"
                                        >
                                            <option :value="1">PENDIENTE</option>
                                            <option :value="2">PARA TRAMITAR</option>
                                            <option :value="3">DERIVADO</option>
                                            <option :value="4">ARCHIVADO</option>
                                            <option :value="6">FINALIZADO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="$emit('closeModalDocument')" style="color: white">
                            <i class="icon-close"/> Cerrar
                        </button>
                        <button v-if="this.internalSent === 1" type="button" class="btn btn-success btn-sm" @click="addDocumentSent()">
                            <i class="fa fa-plus"/> Añadir
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" @click="updateDocumentSent()">
                            <i class="fa fa-refresh"/> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <component
            :is="modalType"
            :ruta="ruta"
            :data="this.document"
            :records="this.data['records']"
            :document_id="this.data['id']"
            :annexeCreate="this.document['annexes']"
            :origin_data="this.origin_data"
            @closeModalRequirement="closeModalRequirement($event)"
            @closeModalAnnexes="closeModalAnnexe"
            @getAffair="getAffair($event)"
        />
    </div>
</template>

<script>
    import vSelect from 'vue-select';
    import ModalRequirementAdministrator from '../../components/modals/ModalRequirementAdministrator';
    import ModalAnnexe from '../../components/modals/ModalAnnexe';
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
            vSelect,
            ModalRequirementAdministrator,
            ModalAnnexe
        },
        data () {
            return {
                selectedDocumentType: {
                    id: 0,
                    label: ''
                },
                arrayDocumentTypes: [],
                document: [],
                selectedDependencyProvided: {
                    id: 0,
                    label: ''
                },
                dependencies: [],
                affair: '',
                detail: '',
                annexes: '',
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
                provided: [],
                selectedProvided: {
                    id: 0,
                    label: '',
                    person_id: ''
                },
                folio: '',
                arrayDocumentPriorities: [],
                shipping_date: '',
                errors: '',
                document_priority_id: 0,
                dependency_shipping_id: 0,
                requirement_document_id: 0,
                document_type_id: 0,
                provided_id: 0,
                entity_type_id: 0,
                document_origin_id: 0,
                tupa_original: '',
                tupa: '',
                records: '',
                number: '',
                state_id: 0,
                documentary_procedure_id: 0,
                type_reception_id: 0,
                created_by: '',
                person_reception: 0,
                reception_date: '',
                dataModal: [],
                show: 0,
                origin_data: {
                    imageList: [],
                    ids: [],
                }
            }
        },
        props: ['ruta', 'data', 'internalSent'],
        computed:{
            selectedDependency() {
                return this.selectedDependencyProvided.id
            },
            modalType(){
                switch(this.show){
                    case 1: {
                        return 'modal-requirement-administrator'
                    }
                    case 2: {
                        return 'modal-annexe'
                    }
                }
            }
        },
        watch: {
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
        methods: {
            getDocumentToSent()
            {
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
            getDocument(id, dependency_id, person_id)
            {
                axios.get(`${this.ruta}/documentModalExternal/${id}/${dependency_id}/${person_id}`)
                    .then((response) => {
                        this.document = response.data.documentModal;
                        this.selectedDocumentType.id              = this.document['document_type_id'];
                        this.selectedDocumentType.label           = this.document['type'];
                        this.selectedDependencyProvided.id        = this.document['dependency_reception_id'];
                        this.selectedDependencyProvided.label     = this.document['Destination'];
                        this.selectedDependencyProvided.person_id = this.document['person_reception'];
                        this.selectedProvided.id                  = this.document['provided_id'];
                        this.selectedProvided.label               = this.document['provided'];
                        this.document_type_id                     = this.document['document_type_id'];
                        this.dependency_id                        = this.document['dependency_id'];
                        this.affair                               = this.document['affair'];
                        this.detail                               = this.document['description'];
                        this.annexes                              = this.document['annexes'];
                        this.provided_id                          = this.document['provided_id'];
                        this.entity_type_id                       = this.document['entity_type_id'];
                        this.folio                                = this.document['folio'];
                        this.document_priority_id                 = this.document['document_priority_id'];
                        this.dependency_shipping_id               = this.document['dependency_shipping_id'];
                        this.document_origin_id                   = this.document['document_origin_id'];
                        this.shipping_date                        = this.document['shipping_show'];
                        this.document_id                          = this.document['id'];
                        this.tupa_original                        = this.document['tupa'];
                        this.tupa                                 = this.document['tupa'];
                        this.records                              = this.document['records'];
                        this.documentary_procedure_id             = this.document['documentary_procedure_id'];
                        this.type_reception_id                    = this.document['type_reception_id'];
                        this.created_by                           = this.document['created_by'];
                        this.person_id                            = this.document['person_id'];
                        this.person_reception                     = this.document['person_reception'];
                        this.reception_date                       = this.document['reception_date'];
                        this.number                               = this.document['number'];
                        this.state_id                             = this.document['procedure_state_id'];
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            updateDocumentSent() {
                let data = {
                    'affair'                  :  this.affair,
                    'description'             :  this.detail,
                    'document_type_id'        :  this.selectedDocumentType.id,
                    'type'                    :  this.selectedDocumentType.label,
                    'folio'                   :  this.folio,
                    'annexes'                 :  this.annexes,
                    'reference'               :  '',
                    'date'                    :  this.created_at,
                    'document_origin_id'      :  this.document_origin_id,
                    'document_priority_id'    :  this.document_priority_id,
                    'type_reception_id'       :  this.type_reception_id,
                    'dependency_shipping_id'  :  this.dependency_shipping_id,
                    'dependency_reception_id' :  this.selectedDependencyProvided.id,
                    'dependency_type_id'      :  this.dependency_type_id,
                    'detail_document_type_id' :  this.selectedDocumentType.id,
                    'provided_id'             :  this.selectedProvided.id,
                    'tupa'                    :  this.tupa_original,
                    'document_id'             :  this.document_id,
                    'documentary_procedure_id':  this.documentary_procedure_id,
                    'records'                 :  this.records,
                    'created_by'              :  this.created_by,
                    'person_reception'        :  this.selectedDependencyProvided.person_id,
                    'shipping_date'           :  this.shipping_date,
                    'number'                  :  this.number,
                    'person_id'               :  this.person_id,
                    'state_id'                :  this.state_id,
                }
                axios.put(`${this.ruta}/sent/update`,data)
                    .then(() => {
                        this.$emit('closeModalDocument');
                        this.$emit('getSent', 1);
                        Swal.fire(
                            'Actualizado!',
                            'El documento ha sido correctamente actualizado.',
                            'success'
                        )
                    }, (error) => {
                        this.errors = error.response.data.errors
                    });
            },
            showModalRequirement() {
                this.show = 1;
            },
            closeModalRequirement() {
                this.show = 0;
                this.origin_data = {
                    imageList: [],
                        ids: [],
                }
                this.$emit('getSent', 1);
                this.internalSent === 0 ? this.getDocument(this.data['id'], this.data['dependency_shipping_id'], this.data['person_id']) : this.getDocument(this.data['id']);

            },
            showModalAnnexe() {
                this.show = 2;
            },
            closeModalAnnexe()
            {
                this.show = 0;
                this.$emit('getSent', 1);
                this.internalSent === 0 ? this.getDocument(this.data['id'], this.data['dependency_shipping_id'], this.data['person_id']) : this.getDocument(this.data['id']);
            },
            getAffair(affair)
            {
                if(affair !== '') {
                    this.affair = affair;
                }
            },
            addDocumentSent() {
                axios.post(`${this.ruta}/addDocumentSent`,{
                    'dependency_shipping_id'   : this.document['dependency_shipping_id'],
                    'dependency_reception_id'  : this.document['dependency_reception_id'],
                    'provided_id'              : this.document['provided_id'],
                    'document_id'              : this.document['id'],
                    'documentary_procedure_id' : this.document['documentary_procedure_id'],
                    'person_reception'         : this.document['person_reception'],
                    'person_id'                : this.document['person_id']
                }).then(() => {
                    this.closeModal();
                    this.getSent(1);
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
        },
        mounted() {
            this.getDocumentToSent();
            this.getDocument(this.data['id'], this.data['dependency_shipping_id'], this.data['person_id']);
            this.tupa = this.data['tupa'];
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
    .modal-lg {
        max-width: 80%;
    }
</style>