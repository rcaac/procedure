<template>
    <div class="modal show-document-generate fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" @keydown.enter.prevent>
                    <div class="modal-header">
                        <h4 class="modal-title">Seleccionar Tupa</h4>
                        <button type="button" class="close" @click="$emit('closeModal')" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <template>
                            <vue-bootstrap-typeahead
                                v-model="origin_data.query"
                                :data="this.origin_data.procedures"
                                :serializer="item => item.description"
                                @hit="selectedProcedures = $event"
                                placeholder="Buscar Tupa"
                                ref="procedureAutocomplete"
                                :max-matches="5"
                                :min-matching-chars="2"
                                />
                            <div class="text-danger" v-text="this.validateTupa"></div>
                        </template>
                        <br>
                        <div v-if="this.origin_data.requirements.length">
                            <h5>REQUISITOS</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Requisito</th>
                                            <th>Costo</th>
                                            <th>Archivo</th>
                                            <th>Código de Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(requirement, index) in origin_data.requirements" :key="index">
                                        <td v-text="requirement.description"/>
                                        <td v-text="requirement.cost"/>
                                        <td>
                                            <label>
                                                <a class="btn btn-secondary" v-if="origin_data.requirementArchive[index].archive === -1">
                                                    <i class="icon-cloud-upload"/>
                                                    Cargar su archivo
                                                </a>
                                                <a class="btn btn-secondary" v-else>
                                                    <i class="icon-check" style="color: #00a67c" />
                                                    {{ origin_data.requirementArchive[index].name | nameFile(origin_data.requirementArchive[index].name)}}
                                                </a>
                                                <input
                                                    type="file"
                                                    name="requirement"
                                                    accept="image/*, application/pdf"
                                                    @change="updateImageList($event, requirement.id, index)"
                                                    style="display: none;"
                                                />
                                            </label>
                                        </td>
                                        <td v-if="requirement.cost !== '0.0'">
                                            <code-input @inputCode="changeCode($event, requirement.id)"/>
                                        </td>
                                        <td v-else></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="selectedQuery()" style="color: white">
                            Guardar
                        </button>
                        <button
                            v-if="this.origin_data.requirements && this.origin_data.requirements.length > 0"
                            type="button"
                            class="btn btn-danger"
                            @click="deleteRequirements()" style="color: white"
                        >
                            <i class="fa fa-trash"/> Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';
    import CodeInput from '../../components/inputs/CodeInput';

    export default {
        components: {
            VueBootstrapTypeahead,
            CodeInput
        },
        props : ['ruta', 'origin_data'],
        filters: {
            nameFile(str) {
                if (str.length > 15) {
                    return str.substr(0, 8) + '...' + str.substr(str.length-8, str.length);
                }
                return str;
            }
        },
        data() {
            return {
                validateTupa: '',
                selectedProcedures: {},
                showInputSearch: false,
            }
        },
        computed: {
            query() {
                return this.origin_data.query
            },
        },
        methods: {
            updateImageList(e, id, index) {
                if (!e.target.files.length) return;
                let file = e.target.files[0];
                this.origin_data.idImage.push(id);
                this.origin_data.imageList.push(file);
                this.origin_data.requirementArchive[index].archive = id;
                this.origin_data.requirementArchive[index].name    = e.target.files[0].name;
            },
            changeCode(e, id) {
                this.origin_data.codeRequirement.push(e);
                this.origin_data.idCode.push(id);
            },
            selectedQuery () {
                if (this.origin_data.query !== '') {
                    this.origin_data.affair = this.$refs.procedureAutocomplete.inputValue;
                    this.$emit('closeModal');
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            deleteRequirements() {
                this.origin_data.query           = '';
                this.origin_data.affair          = '';
                this.origin_data.procedures      = [];
                this.origin_data.requirements    = [];
                this.origin_data.idImage         = [];
                this.origin_data.imageList       = [];
                this.origin_data.codeRequirement = [];
                this.origin_data.idCode          = [];
                this.origin_data.requirementArchive = [];
                this.origin_data.showInputSearch = false;
                this.$refs.procedureAutocomplete.inputValue = '';
            }
        },
        watch: {
            query(newQuery) {
                if (this.query.length) {
                    axios.get(`${this.ruta}/documentProcess?newQuery=${newQuery}`)
                        .then((response) => {
                            this.origin_data.procedures = response.data.procedures;
                        })
                }
            },
            selectedProcedures() {
                if (this.origin_data.procedures.length) {
                    this.origin_data.idImage            = [];
                    this.origin_data.imageList          = [];
                    this.origin_data.codeRequirement    = [];
                    this.origin_data.idCode             = [];
                    this.origin_data.requirementArchive = [];
                    axios.get(`${this.ruta}/documentProcessQuery/${this.selectedProcedures.id}`)
                        .then((response) => {
                            this.origin_data.requirements = response.data.requirements;
                            response.data.requirements.forEach(() => {
                                this.origin_data.requirementArchive.push({archive: -1, name: ''});
                            })
                        });
                }
            }
        },
        mounted() {
            this.$refs.procedureAutocomplete.inputValue = this.origin_data.affair;
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
        position: fixed;
        overflow-y: auto;
    }
    .modal-lg {
        max-width: 70%;
    }
</style>