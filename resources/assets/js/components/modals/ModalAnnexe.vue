<template>
    <div>
        <div class="vld-parent">
            <loading :active.sync="isLoading" :can-cancel="false" :is-full-page="fullPage" />
        </div>
        <div class="modal show-document-generate fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Anexos</h4>
                        <button type="button" class="close" @click="$emit('closeModalAnnexes')" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="table-responsive" v-if="this.annexes && this.annexes.length >= 1">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>Anexo</th>
                                        <th>Sustento</th>
                                        <th>Archivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(annexe, index) in annexes" :key="index">
                                        <td>
                                            <input type="text" v-model="annexe.annexe" class="form-control form-control-sm">
                                        </td>
                                        <td v-if="annexe.file">
                                            <a
                                                :href="`https://munipillcomarca.s3.us-east-2.amazonaws.com/${annexe.file}`"
                                                target="_blank"
                                            >
                                                {{ annexe.annexe }}
                                            </a>
                                        </td>
                                        <td v-else></td>
                                        <td>
                                            <input
                                                type="file"
                                                name="picture"
                                                accept="image/*, application/pdf"
                                                @change="updateAnnexeList($event)"
                                            />
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                @click="deleteAnnexes(annexe.id)" style="color: white"
                                            >
                                                <i class="fa fa-trash"/> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row" v-if="annexeCreate !== '1'">
                                <template v-for="(item,index) in annexeFile">
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
                                                v-show="index || ( !index && annexeFile.length > 1)"
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
                                                v-show="index === annexeFile.length-1"
                                            >
                                                <i class="icon-plus"></i> Anexo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2"/>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" v-if="annexeCreate === '1'">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="updateAnnexes()" style="color: white"
                        >
                            <i class="fa fa-save"/> Guardar
                        </button>
                    </div>
                    <div class="modal-footer" v-if="this.imageListAnnexes.length > 0">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="createAnnexes()" style="color: white"
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
                annexes: [],
                selectedProcedures: '',
                query: '',
                imageList: [],
                imageListAnnexes: [],
                showInputSearch: '',
                annexeFile: [
                    {
                        annexe: '',
                        archive: -1,
                        name: '',
                        validateText: '',
                        validateFile: ''
                    }
                ],
                fileName: '',
                isLoading: false,
                fullPage: true,
            }
        },
        props: ['ruta', 'records', 'annexeCreate', 'document_id'],
        filters: {
            nameFile(str) {
                if (str.length > 15) {
                    return str.substr(0, 8) + '...' + str.substr(str.length-8, str.length);
                }
                return str;
            }
        },
        methods: {
            updateAnnexeList(e)
            {
                let file = e.target.files[0];
                this.imageList.push(file);
            },
            getAnnexes(records) {
                axios.get(`${this.ruta}/documentAnnexes/${records}`)
                    .then((response) => {
                        this.annexes = response.data.annexes;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            updateAnnexes()
            {
                this.isLoading = true;

                let data = new FormData();

                $.each(this.imageList, function (key, image) {
                    data.append(`images[${key}]`, image);
                });

                let annexes = JSON.stringify({
                    'annexes': this.annexes
                });
                data.append('annexes', annexes);

                data.append('document_id', this.document_id);

                axios.post(`${this.ruta}/documentAnnexeUpdate`, data)
                    .then(() => {
                        this.isLoading = false;
                        this.$emit('closeModalAnnexes');
                        Swal.fire(
                            'Actualizado!',
                            'Los annexos se actualizaron con éxito.',
                            'success'
                        )
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            deleteAnnexes($id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar el anexo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        let me = this;
                        me.isLoading = true;
                        axios.patch(`${me.ruta}/externalAnnexe`, {
                            'annexe_id': $id,
                            'document_id': this.document_id
                        })
                        .then((response) => {
                            me.isLoading = false;
                            me.getAnnexes(me.id);
                            if (response.data.annexe === false) {
                                me.$emit('closeModalAnnexes');
                            }
                            Swal.fire(
                                'Eliminado!',
                                'El annexo ha sido eliminado con éxito.',
                                'success'
                            )
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                    }
                })
            },
            updateImageListAnnexes(e, index)
            {
                if (!e.target.files.length) return;
                let file = e.target.files[0];
                this.imageListAnnexes.push(file);
                this.annexeFile[index].archive = index;
                this.annexeFile[index].name = e.target.files[0].name;
            },
            createAnnexes()
            {
                this.isLoading = true;

                let data = new FormData();

                $.each(this.imageListAnnexes, function (key, image) {
                    data.append(`images[${key}]`, image);
                });

                let annexes = JSON.stringify({
                    'annexes': this.annexeFile
                });
                data.append('annexes', annexes);

                data.append('document_id', this.document_id);

                axios.post(`${this.ruta}/documentAnnexeCreate`, data)
                    .then(() => {
                        this.isLoading = false;
                        this.$emit('closeModalAnnexes');
                        Swal.fire(
                            'Guardado!',
                            'Los annexos se guardaron con éxito.',
                            'success'
                        )
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            add(index) {
                if (this.annexeFile[index].annexe !== '' && this.imageListAnnexes.length > index) {
                    this.annexeFile.push({ annexe: '', name: '', archive: -1, validateText: '', validateFile: ''});
                }else {
                    if (this.annexeFile[index].annexe === '') {
                        this.annexeFile[index].validateText = 'Debe ingresar la descripción del anexo';
                    }
                    if (this.imageListAnnexes.length === index) {
                        this.annexeFile[index].validateFile = 'Debe Seleccionar un archivo'
                    }
                }
            },
            remove(index) {
                this.annexeFile.splice(index, 1);
                this.imageListAnnexes.splice(index, 1);
            },
        },
        mounted() {
            this.getAnnexes(this.records);
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