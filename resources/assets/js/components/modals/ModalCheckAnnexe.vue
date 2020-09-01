<template>
    <div class="modal show-document-generate fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Anexos</h4>
                    <button type="button" class="close" @click="$emit('closeModalAnnexe')" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Dependencia</th>
                                <th>Anexo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(annexe, index) in annexes" :key="index">
                                <td>{{ annexe.description }}</td>
                                <td v-if="annexe.file">
                                    <a
                                        :href="`https://munipillcomarca.s3.us-east-2.amazonaws.com/${annexe.file}`"
                                        target="_blank"
                                    >
                                        {{ annexe.annexe }}
                                    </a>
                                </td>
                                <td v-else></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data () {
            return {
                annexes: [],
            }
        },
        props: ['ruta', 'id'],
        methods: {
            getAnnexes(id) {
                axios.get(`${this.ruta}/documentAnnexes/${id}`)
                    .then((response) => {
                        this.annexes = response.data.annexes;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.getAnnexes(this.id);
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
        max-width: 50%;
    }
</style>