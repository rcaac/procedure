<template>
    <div class="modal show-document-generate fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Requisitos</h4>
                        <button
                            type="button"
                            class="close"
                            @click="$emit('closeModalRequirement')"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="this.requirements && this.requirements.length > 0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Requisito</th>
                                            <th>Costo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(requirement, index) in requirements" :key="index">
                                            <td v-if="requirement.file">
                                                <a :href="`https://munipillcomarca.s3.us-east-2.amazonaws.com/${requirement.file}`" target="_blank">{{ requirement.description }}</a>
                                            </td>
                                            <td v-else>{{ requirement.description }}</td>
                                            <td v-text="requirement.cost"/>
                                            <td v-if="requirement.state === 1">
                                                <input type="checkbox" checked disabled >
                                            </td>
                                            <td v-if="requirement.state === 2">
                                                <input type="checkbox" disabled >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    export default {
        data () {
            return {
                requirements: []
            }
        },
        props: ['ruta', 'id'],
        methods: {
            getRequirements(id)
            {
                axios.get(`${this.ruta}/tracingRequirement/${id}`)
                    .then((response) => {
                    this.requirements = response.data.requirements;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted() {
            this.getRequirements(this.id);
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