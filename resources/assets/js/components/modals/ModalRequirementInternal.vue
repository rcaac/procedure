<template>
    <div class="modal show-requirements fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-large" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Requisitos</h4>
                    <button type="button" class="close" @click="$emit('closeModalRequirement')" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                    <th><small><b>REQUISITOS</b></small></th>
                                    <th><small><b>COSTO</b></small></th>
                                    <th><small><b>OBSERVACIONES</b></small></th>
                                    <th><small><b>FECHA DE TRÁMITE</b></small></th>
                                    <th><small><b>ESTADO</b></small></th>
                                    <th><small><b>ACCIONES</b></small></th>
                                </tr>
                                </thead>
                                <tbody>
                                <document-list-requirement
                                    v-for="requirement in arrayRequirements"
                                    :key="requirement.id"
                                    :requirement="requirement"
                                    @onChecked="onChecked(requirement.id, requirement.state, requirement.document_id)"
                                    @edit="updateRequirement(requirement)"
                                />
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" @click="$emit('closeModalRequirement')" style="color: white">
                        <i class="icon-close"/> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement';

    export default {
        components: {
            DocumentListRequirement
        },
        data() {
            return {
                arrayRequirements: [],
            }
        },
        props: ['ruta', 'id'],
        methods: {
            onChecked(id, check, document_id) {
                axios.get(`${this.ruta}/checkedRequirements?id=${id}&check=${check}&document_id=${document_id}`)
                    .then((response) => {
                        console.log(response);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            updateRequirement(data = []) {
                axios.put(`${this.ruta}/document/updateObservationRequirement`,{
                    'observation'           : data['observation'],
                    'document_procedure_id' : data['document_procedure_id'],
                }).then((response) => {
                    console.log(response.data)
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            getRequirements(records) {
                axios.get(`${this.ruta}/tracingRequirement/${records}`)
                    .then((response) => {
                        this.arrayRequirements = response.data.requirements;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
        },
        mounted() {
            this.getRequirements(this.id)
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-requirements{
        display: list-item !important;
        opacity: 1 !important;
        background-color: #3c29297a !important;
        position: fixed;
        overflow-y: auto;
    }
    .modal-large {
        max-width: 80%;
    }
</style>