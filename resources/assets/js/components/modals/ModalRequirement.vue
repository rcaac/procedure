<template>
    <div>
        <div class="vld-parent">
            <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
        </div>
        <div class="modal show-document-generate fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Requisitos</h4>
                    <button
                        type="button"
                        class="close"
                        @click="$emit('closeModal')"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <template v-if="this.showInputSearch === null">                    
                        <vue-bootstrap-typeahead
                            v-model="query"
                            :data="procedures"
                            :serializer="item => item.description"
                            @hit="selectedProcedures = $event"
                            placeholder="Buscar Tupa"
                            ref="procedureAutocomplete"
                            :max-matches="5"
                            :min-matching-chars="2"
                        />
                    </template>
                    <!--<div class="text-danger" v-text="this.validateTupa"></div>-->
                    <br>
                    <div v-if="this.requirements && this.requirements.length > 0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Requisito</th>
                                    <th>Costo</th>
                                    <th>Subir Archivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(requirement, index) in requirements" :key="index">
                                    <td v-if="requirement.file">
                                        <a :href="`https://munipillcomarca.s3.us-east-2.amazonaws.com/${requirement.file}`" target="_blank">{{ requirement.description }}</a>
                                    </td>
                                    <td v-else>
                                        {{ requirement.description }}
                                    </td>
                                    <td v-text="requirement.cost"/>
                                    <td>
                                        <input
                                            type="file"
                                            name="picture"
                                            accept="image/*, application/pdf"
                                            @change="updateRequirementList($event, requirement.id)"
                                        />
                                    </td>                                        
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        v-if="this.requirements && this.requirements.length > 0"
                        type="button"
                        class="btn btn-danger btn-sm"
                        @click="deleteRequirements()" style="color: white"
                    >
                        <i class="fa fa-trash"/> Eliminar
                    </button>
                    <button 
                        v-if="this.imageList.length && this.imageList.length > 0"
                        type="button" class="btn btn-primary btn-sm"
                        @click="updateRequirements()" style="color: white"
                    >
                        <i class="fa fa-save"/> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        components: {
            VueBootstrapTypeahead,
            Loading
        },
        data () {
            return {
                procedures: [],
                requirements: [],
                selectedProcedures: '',
                query: '',
                records: '',
                document_id: 0,
                imageList: [],
                ids: [],
                showInputSearch: '',
                isLoading: false,
                fullPage: true,
            }
        },
        props: ['ruta', 'data'],
        watch: {
            query(newQuery) {
                if (this.query.length) {
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
                }else {
                    this.requirements = [];
                }
            },
            selectedProcedures()
            {
                this.requirements = [];
                this.states       = [];
                let url = '';

                if (this.procedure_id === this.procedure_like_id)
                {
                    url = `${this.ruta}/documentProcedureQueryState/${this.requirement_document_id}`;

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
        },
        methods: {
            updateRequirementList(e, id)
            {                
                let file = e.target.files[0];
                this.ids.push(id);
                this.imageList.push(file);
            },
            getRequirements(id)
            {
                axios.get(`${this.ruta}/tracingRequirement/${id}`)
                    .then((response) => {
                    this.requirements = response.data.requirements;
                    this.showInputSearch = this.requirements[0].file;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            updateRequirements() {
                this.isLoading = true;

                let data = new FormData();

                $.each(this.ids, function (key, id) {
                data.append(`ids[${key}]`, id);
                });

                $.each(this.imageList, function (key, image) {
                data.append(`images[${key}]`, image);
                });

                data.append('affair', this.query);
                data.append('document_id', this.document_id);
                data.append('records', this.records);

                axios.post(`${this.ruta}/documentRequirementUpdate`, data)
                .then(() => {
                    this.isLoading = false;
                    this.$emit('closeModal');
                    this.$emit('getAffair', this.query);
                    Swal.fire(
                        'Actualizado!',
                        'Los requisitos se actualizaron con éxito.',
                        'success'
                    )
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            deleteRequirements() {
                Swal.fire({
                    title: 'Esta seguro de eliminar los requisitos?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        this.isLoading = true;
                        axios.patch(`${this.ruta}/externalRequirement/${this.document_id}`)
                            .then(() => {
                                this.isLoading       = false;
                                this.showInputSearch = null;
                                this.requirements    = [];
                                Swal.fire(
                                    'Eliminado!',
                                    'Los requisitos han sido eliminados con éxito.',
                                    'success'
                                )
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                })
            }
        },
        mounted() {
            if(this.data['tupa'] === 1) {
                this.getRequirements(this.data['id']);
            }else {
               this.showInputSearch = null; 
            }
            this.records = this.data['records'];
            this.document_id  = this.data['id'];
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }    
    .show-document-generate{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
    .modal-lg {
        max-width: 60%;
    }
</style>