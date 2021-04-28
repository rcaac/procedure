<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h6><i class="icon-doc"/> DOCUMENTOS PENDIENTES</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row mt-sm-0">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="getSlopes(search)"
                                    class="form-control form-control-sm"
                                    placeholder="N° de Expediente"
                                >
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"/> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select
                                v-model="pending_change_id"
                                class="form-control form-control-sm"
                                @change="getSlopes(1)"
                            >
                                <option :value=0>Todos los documentos</option>
                                <option :value=1>INTERNO</option>
                                <option :value=2>EXTERNO</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select
                                v-model="document_change_id"
                                class="form-control form-control-sm"
                                @change="getSlopes(1)"
                            >
                                <option :value=0>Todos los tipos de documentos</option>
                                <option
                                    v-for="types in arrayDocumentTypes"
                                    :key="types.id"
                                    :value="types.id"
                                    v-text="types.label">
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th><small><b>EXPEDIENTE</b></small></th>
                            <th><small><b>REGISTRO</b></small></th>
                            <th><small><b>DEPENDENCIA EMISOR</b></small></th>
                            <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                            <th><small><b>ASUNTO</b></small></th>
                            <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                            <th><small><b>FOLIOS</b></small></th>
                            <th><small><b>PRIORIDAD</b></small></th>
                            <th><small><b>FECHA DE ENVÍO</b></small></th>
                            <th><small><b>ESTADO</b></small></th>
                            <th><small><b>ACCIONES</b></small></th>
                        </tr>
                        </thead>
                        <tbody>
                            <document-list
                                v-for="pending in arraySlopes"
                                :key="pending.id"
                                :pending="pending"
                                :viewPending="viewPending"
                                @openModalRequirement="openModal($event)"
                                @receive="receive(pending.documentary_procedure_id)"
                                @modalShowAnnexe="modalShowAnnexe($event)"
                            />
                        </tbody>
                    </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Anterior</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"/>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <component
            :is="modalType"
            :ruta="ruta"
            :id="this.document_id"
            @closeModalAnnexe="closeModalAnnexe"
            @closeModalRequirement="closeModal"
        />
    </main>
</template>

<script>
    import DocumentListRequirement from '../../components/documents/DocumentListRequirement'
    import DocumentList from '../../components/documents/DocumentList'
    import ModalCheckAnnexe from '../../components/modals/ModalCheckAnnexe'
    import ModalRequirementInternal from "../../components/modals/ModalRequirementInternal";

    export default {
        components: {
            DocumentListRequirement,
            DocumentList,
            ModalCheckAnnexe,
            ModalRequirementInternal
        },
        props : ['ruta'],
        data () {
            return {
                arraySlopes: [],
                offset : 3,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                arrayRequirements: [],
                viewPending: 1,
                pending_change_id: 0,
                arrayDocumentTypes: [],
                document_change_id: 0,
                modal: 0,
                titleModal: '',
                typeAction: 0,
                search: '',
                show: 0,
                document_id: 0
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                let from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                let pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },
            modalType(){
                switch(this.show){
                    case 1: {
                        return 'modal-requirement-internal'
                    }
                    case 2: {
                        return 'modal-check-annexe'
                    }
                }
            }
        },
        methods : {
            modalShowAnnexe(id) {
                this.show = 2;
                this.document_id = id;
            },
            closeModalAnnexe()
            {
                this.show = 0;
                this.document_id = 0;
            },
            getSlopes(page) {
                let url = '';

                if (this.search !== '')
                {
                    url= `${this.ruta}/slopes?page=${page}&search=${this.search}`;
                }else {
                    url= `${this.ruta}/slopes?page=${page}&pending_change_id=${this.pending_change_id}&document_type_id=${this.document_change_id}`;
                }

                axios.get(url).then((response) => {
                    this.arraySlopes = response.data.slopes.data;
                    this.pagination  = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page) {
                this.pagination.current_page = page;
                this.getSlopes(page);
            },
            receive(id) {
                axios.get(`${this.ruta}/slopes/${id}`)
                    .then(() => {
                        this.arrayRequirements  = [];
                        this.getSlopes(1)
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getRequirements(records) {
                this.arrayRequirements = [];
                axios.get(`${this.ruta}/tracingRequirement/${records}`)
                    .then((response) => {
                        this.arrayRequirements = response.data.requirements;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            onChecked(id, check, document_id) {
                axios.get(`${this.ruta}/checkedRequirements?id=${id}&check=${check}&document_id=${document_id}`)
                    .then((response) => {
                        console.log(response);
                })
                .catch(function (error) {
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
            getDocumentToSent() {
                axios.get(`${this.ruta}/documentToSent`)
                    .then((response) => {
                        this.arrayDocumentTypes = response.data.document_type;
                })
                .catch(function (error) {
                    console.log(error);
                })
            },
            openModal(id) {
                this.show = 1;
                this.document_id = id;
            },
            closeModal() {
                this.show       = 0;
                this.document_id = 0;
            },
        },
        mounted() {
            this.getSlopes(1);
            this.getDocumentToSent()
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
