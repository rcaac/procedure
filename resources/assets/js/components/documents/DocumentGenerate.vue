<template>
    <div>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Fecha:</label>
            <div class="col-md-4">
                <input type="date" v-model="origin_data.shipping_date" class="form-control form-control-sm">
            </div>
            <label class="col-md-2 form-control-label">Prioridad:</label>
            <div class="col-md-4">
                <select v-model="origin_data.document_priority_id" class="form-control form-control-sm" :disabled="origin_data.auth === 4">
                    <option
                        v-for="priority in origin_data.arrayDocumentPriorities"
                        :key="priority.id"
                        :value="priority.id"
                        v-text="priority.priority">
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Tipo de Documento:</label>
            <div class="col-md-4">
                <select
                    v-model="origin_data.detail_document_type_id"
                    class="form-control form-control-sm"
                    @click="$emit('detail', origin_data.detail_document_type_id); changeProcedure()"
                    :disabled="origin_data.auth === 4"
                >
                    <option value="0" disabled>Elija Tipo de Documento</option>
                    <option
                        v-for="detail in origin_data.detailDocumentTypes"
                        :key="detail.id"
                        :value="detail.id"
                        v-text="detail.detail">
                    </option>
                </select>
                <div v-if="origin_data.errors && origin_data.errors.detail_document_type_id" class="text-danger">{{ origin_data.errors.detail_document_type_id[0] }}</div>
            </div>
            <label class="col-md-2 form-control-label">Forma de Recepción:</label>
            <div class="col-md-4">
                <select v-model="origin_data.type_reception_id" class="form-control form-control-sm" :disabled="origin_data.auth === 4">
                    <option
                        v-for="reception in origin_data.arrayTypeReceptions"
                        :key="reception.id"
                        :value="reception.id"
                        v-text="reception.reception">
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group row" v-if="this.origin_data.arrayDocumentTypes.length">
            <label class="col-md-2 form-control-label">Documento:</label>
            <div class="col-md-4">
                    <v-select
                        v-model="origin_data.selectedDocumentType"
                        :options="origin_data.arrayDocumentTypes"
                        placeholder="Elija Documento"
                        :clearable="false"
                        class="small"
                        @input="$emit('destinations')"
                    />
                <div v-if="origin_data.errors && origin_data.errors.document_type_id" class="text-danger">{{ origin_data.errors.document_type_id[0] }}</div>
            </div>
            <div class="col-md-2" v-if="origin_data.document_origin_id === 2 || origin_data.document_origin_id === 3">
                <input type="text" v-model="origin_data.number" class="form-control form-control-sm" placeholder="Número de Doc.">
                <div v-if="origin_data.errors && origin_data.errors.number" class="text-danger">{{ origin_data.errors.number[0] }}</div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <label class="switch switch switch-3d switch-primary">
                        <input type="checkbox" class="switch-input" :false-value="0" :true-value="1" v-model="origin_data.transparency">
                        <span class="switch-label"/>
                        <span class="switch-handle"/>
                    </label>
                    &nbsp;Transparencia
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Asunto:</label>
            <div class="col-md-4">
                <div class="input-group">
                    <textarea
                        v-model="origin_data.affair"
                        rows="1"
                        class="form-control form-control-sm"
                        placeholder="Escriba o seleccione un asunto"
                    >
                    </textarea>
                </div>
                <div v-if="origin_data.errors && origin_data.errors.affair" class="text-danger">{{ origin_data.errors.affair[0] }}</div>
            </div>
            <div class="col-md-3" v-if="origin_data.document_tupa === 1 && origin_data.detail_document_type_id === 1">
                <div class="input-group">
                    <button
                        type="submit"
                        @click="$emit('openModalRequirement', origin_data.records)"
                        :class="{ 'btn btn-primary btn-sm': this.origin_data.tupa === 0, 'btn btn-success btn-sm': this.origin_data.tupa === 1 }"
                        v-if="origin_data.viewProcess === 1"
                    >
                        <i class="icon-plus" v-if="this.origin_data.tupa === 0"/>
                        <i class="icon-check" v-if="this.origin_data.tupa === 1"/>
                        Trámite
                    </button>
                    <button
                        type="submit"
                        @click="openModalRequirements()"
                        :class="{ 'btn btn-primary btn-sm': this.origin_data.tupa === 0, 'btn btn-success btn-sm': this.origin_data.tupa === 1 }"
                        v-else
                    >
                        <i class="icon-plus" v-if="this.origin_data.tupa === 0"/>
                        <i class="icon-check" v-if="this.origin_data.tupa === 1"/>
                        Trámite
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Contenido:</label>
            <div class="col-md-8">
                <el-tiptap
                    v-model="origin_data.detail"
                    :extensions="extensions"
                    placeholder="Escribe contenido..."
                />
                <div v-if="origin_data.errors && origin_data.errors.description" class="text-danger">{{ origin_data.errors.description[0] }}</div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Anexos:</label>
            <template v-for="(item,index) in origin_data.annexeFile">
                <label class="col-md-2 form-control-label" v-if="index !== 0"></label>
                <div class="col-md-4">
                    <div class="form-group">
                        <textarea
                            rows="1"
                            v-model="item.annexe"
                            class="form-control form-control-sm"
                            :placeholder="'Ingrese la descripción del anexo '+(index+1)"
                            :class="{'is-invalid' : item.validateText}"
                        ></textarea>
                        <em class="invalid-feedback" v-if="item.validateText">{{item.validateText}}</em>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>
                        <a class="btn btn-secondary btn-sm" v-if="item.archive === -1">
                            <i class="icon-cloud-upload"/>
                            Cargar su archivo
                        </a>
                        <a class="btn btn-secondary btn-sm" v-else>
                            <i class="icon-check" style="color: #00a67c"/>
                            {{ item.name | nameFile(item.name)}}
                        </a>
                        <input
                            ref="imageUploader"
                            type="file"
                            name="annexe"
                            accept="image/*, application/pdf"
                            @change="updateImageListAnnexes($event, index)"
                            style="display: none;"
                        /><br>
                        <em class="text-danger" v-if="item.validateFile">{{item.validateFile}}</em>
                    </label>
                </div>
                <div class="col-md-1" v-if="index > 0">
                    <div class="input-group">
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            @click="remove(index)"
                            v-show="index || ( !index && origin_data.annexeFile.length > 1)"
                        >
                            <i class="icon-close"></i> Anexo
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="input-group">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="add(index)"
                            v-show="index === origin_data.annexeFile.length-1"
                        >
                            <i class="icon-plus"></i> Anexo
                        </button>
                    </div>
                </div>
                <div class="col-md-2"/>
            </template>
        </div>
        <div class="row">
            <label class="col-md-2 form-control-label">Número de Folios:</label>
            <div class="col-md-1">
                <input type="text" v-model="origin_data.folio" class="form-control form-control-sm" placeholder="Folios">
                <div v-if="origin_data.errors && origin_data.errors.folio" class="text-danger">{{ origin_data.errors.folio[0] }}</div>
            </div>
        </div>
        <component
            :is="modalType"
            :ruta="this.origin_data.route"
            :origin_data="this.origin_data"
            @closeModal="closeModalRequirements(...arguments)"
        />
    </div>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import vSelect from 'vue-select'
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
    import ModalRequirementSearch from '../../components/modals/ModalRequirementSearch'

    export default {
        components: {
            VueBootstrapTypeahead,
            vSelect,
            ModalRequirementSearch
        },
        data() {
            return {
                modal                 : 0,
                titleModal             : '',
                selectedDocumentType   : {},
                selectedProcedures     : {},
                typeAction             : 0,
                validateTupa           : '',
                requirement_document_id: 0,
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
                showModal: 0
            }
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            },
        },
        watch: {
            query(newQuery) {
                let url = '';
                if (this.origin_data.query.length)
                {
                    if (this.origin_data.document_process === 1 ) {
                        url = `${this.origin_data.route}/documentProcedureSearchExternal?newQuery=${newQuery}&records=${this.origin_data.records}`;
                    }else {
                        url = `${this.origin_data.route}/documentProcedureSearchExternal?newQuery=${newQuery}`;
                    }
                    axios.get(url)
                    .then((response) => {
                        this.origin_data.procedures = response.data.procedures;
                        if (this.origin_data.document_process === 1) {
                            this.selectedProcedures = response.data.procedures[0];
                            this.requirement_document_id = response.data.requirement_document_id;
                        }
                    })
                }

            },
            selectedProcedures() {
                let url = '';
                if (this.origin_data.document_process === 1) {
                    url = `${this.origin_data.route}documentProcedureQueryProcess/${this.origin_data.records}`;
                }else {
                    url = `${this.origin_data.route}documentProcedureQuery/${this.selectedProcedures.id}`;
                }
                axios.get(url)
                .then((response) => {
                    this.origin_data.requirements = response.data.requirements;
                    if (this.origin_data.document_process === 1) {
                        $.each(response.data.requirements, function (key, value) {
                            if (value.process_state_id === 1) {
                                origin_data.states.push(value.id)
                            }
                        })
                    }
                });
            },
            getDocumentTypes() {
                if (this.origin_data.editDocument === 0) {
                    this.origin_data.selectedDocumentType = {
                        id: 0,
                        label: 'Elija un documento'
                    };
                }

                if (this.origin_data.detail_document_type_id !== 0) {
                    axios.get(`${this.origin_data.route}/documentTypes/${this.origin_data.detail_document_type_id}`)
                        .then((response) => {
                            this.origin_data.arrayDocumentTypes = response.data.document_type;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                }
            }
        },
        computed:{
            getDocumentTypes() {
                return this.origin_data.detail_document_type_id
            },
            query() {
                return this.origin_data.query;
            },
            modalType(){
                switch(this.showModal) {
                    case 1: {
                        return 'modal-requirement-search'
                    }
                }
            }
        },
        methods: {
            openModalRequirements () {
                this.showModal = 1;
            },
            closeModalRequirements () {
                this.showModal = 0;
                if (this.origin_data.affair !== '') {
                    this.origin_data.tupa = 1;
                }else {
                    this.origin_data.tupa = 0;
                    this.origin_data.affair = '';
                    this.origin_data.requirements = [];
                    this.origin_data.requirementArchive = {}
                }
            },
            openModal() {
                this.modal       = 1;
                this.titleModal = 'Seleccionar Tupa';
                this.typeAction = 1;
                this.procedures =  [];
                if (this.origin_data.document_process === 1)
                {
                    this.origin_data.query = this.origin_data.affair;
                }
                if (this.origin_data.document_process !== 1)
                {
                    this.$refs.procedureAutocomplete.inputValue = this.origin_data.query;
                }
            },
            closeModal() {
                this.modal        = 0;
                this.titleModal   = '';
                this.validateTupa = '';
            },
            selectedQuery() {
                if (this.query !== '') {
                    this.origin_data.affair = this.query;
                    this.origin_data.tupa   = 1;
                    this.closeModal();
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            changeProcedure() {
                this.origin_data.tupa                       = 0;
                this.origin_data.requirements               = [];
                this.origin_data.affair                     = '';
                //this.$refs.procedureAutocomplete.inputValue = '';
                this.origin_data.query                      = ''
            },
            updateImageListAnnexes(e, index) {
                if (!e.target.files.length) return;
                let file = e.target.files[0];
                this.origin_data.imageListAnnexes.push(file);
                this.origin_data.annexeFile[index].archive = index;
                this.origin_data.annexeFile[index].name = e.target.files[0].name;
            },
            add(index) {
                if (this.origin_data.annexeFile[index].annexe !== '' && this.origin_data.imageListAnnexes.length > index) {
                    this.origin_data.annexeFile.push({ annexe: '', name: '', archive: -1, validateText: '', validateFile: ''});
                }else {
                    if (this.origin_data.annexeFile[index].annexe === '') {
                        this.origin_data.annexeFile[index].validateText = 'Debe ingresar la descripción del anexo';
                    }
                    if (this.origin_data.imageListAnnexes.length === index) {
                        this.origin_data.annexeFile[index].validateFile = 'Debe Seleccionar un archivo'
                    }
                }
            },
            remove(index) {
                this.origin_data.annexeFile.splice(index, 1);
                this.origin_data.imageListAnnexes.splice(index, 1);
            }
        }
    }
</script>

<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-document-generate {
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        height: 138%;
    }
    #detailEdit .ql-editor { min-height:100px }
    .wangEditor-txt {
        min-height:100px;
    }
</style>
