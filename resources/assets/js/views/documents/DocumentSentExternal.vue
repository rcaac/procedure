<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h6><i class="icon-doc"/> DOCUMENTOS ENVIADOS</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row mt-sm-0">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input
                                    type="text"
                                    v-model="search"
                                    @keyup="getSent(search)"
                                    class="form-control form-control-sm"
                                    placeholder="N° de Expediente"
                                >
                                <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"/> Buscar</button>
                            </div>
                        </div>                       
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th><small><b>EXPEDIENTE</b></small></th>
                                <th><small><b>REGISTRO</b></small></th>
                                <th><small><b>DEPENDENCIA RECEPTOR</b></small></th>
                                <th><small><b>TIPO DE DOCUMENTO</b></small></th>
                                <th><small><b>ASUNTO</b></small></th>
                                <th><small><b>PROVEÍDO DE ATENCIÓN</b></small></th>
                                <th><small><b>FOLIOS</b></small></th>
                                <th><small><b>PRIORIDAD</b></small></th>
                                <th><small><b>FECHA DE ENVÍO</b></small></th>
                                <th><small><b>FECHA DE RECEPCIÓN</b></small></th>
                                <th><small><b>ACCIONES</b></small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(sent, i) in arraySent" :key="`${i}-${sent.id}`">
                                <td v-text="sent.records"/>
                                <td>
                                    {{ sent.registry }}<br>
                                    <small><b>Referencia:</b> {{ sent.reference }}</small>
                                </td>
                                <td>
                                    <div v-if="sent.Destination === 'USUARIO EXTERNO'">
                                        <small><b>Usuario:</b> {{ sent.fullName }}<br></small>
                                        <small><b>Dni:</b> {{ sent.dni }}</small>
                                    </div>
                                    <div v-else>
                                        <small>{{ sent.Destination }}<br></small>
                                        <small><b>Entidad:</b> {{ sent.entity }}</small>
                                    </div>
                                </td>
                                <td><small>{{ sent.code }}</small></td>
                                <td v-text="sent.affair"/>
                                <td><small>{{ sent.provided }}</small></td>
                                <td v-text="sent.folio"/>
                                <td><small>{{ sent.priority }}</small></td>
                                <td v-text="sent.shipping_date"/>
                                <td v-text="sent.reception_date"/>
                                <td>
                                    <div v-if="sent.tupa === 1">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            @click="showModalRequirement(sent.id)"
                                        >
                                            <i class="fa fa-eye"/> Requisitos
                                        </button>
                                    </div>
                                    <div v-if="sent.annexes === '1'">
                                        <button
                                            type="button"
                                            class="btn btn-outline-warning btn-sm"
                                            @click="showModalAnnexe(sent.id)"
                                        >
                                            <i class="fa fa-eye"/> Anexos
                                        </button>
                                    </div>
                                    <div v-if="!sent.reception_date">
                                        <button
                                            type="button"
                                            class="btn btn-outline-info btn-sm"
                                            @click="showModalDocument(sent.id)"
                                        >
                                            <i class="icon-pencil"/> Editar
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        @click="printDocument(sent.id)"
                                    >
                                        <i class="icon-printer"/> Imprimir
                                    </button>
                                </td>
                            </tr>
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
            @getSent="getSent($event)"
            @closeModalDocument="closeModalDocument"
            @closeModalRequirement="closeModalRequirement"
            @closeModalAnnexe="closeModalAnnexe"
            :internalSent="this.showInternalSent"
        />        
    </main>
</template>

<script>
    import ModalDocument from '../../components/modals/ModalDocumentInternal';
    import ModalCheckRequirement from '../../components/modals/ModalCheckRequirement';
    import ModalCheckAnnexe from '../../components/modals/ModalCheckAnnexe';

    export default {
        components: {
            ModalDocument,
            ModalCheckRequirement,
            ModalCheckAnnexe
        },
        props : ['ruta'],        
        data () {
            return {                
                files: '',
                archive: '',
                validateTupa: '',
                arraySent: [],
                offset : 3,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },               
                document_id: 0,
                type_reception_id: 0,
                arrayDocumentPriorities: [],                
                arrayDocumentTypes: [],                           
                tupa: 0,
                tupa_original: 0,
                auth: 0,
                selectedProcedures: '',
                document_sent: 1,
                procedure_id: 0,
                procedure_like_id: 0,
                search: '',
                responseRecords: 0,
                responseRegistry: 0,
                responseCode: 0,
                responseAffair: '',
                responseDescription: '',
                responseFullNameT: '',
                responseFullNameR: '',
                responseDependency: '',
                responseDestination: '',
                responseEntityDependency: '',
                responseEntityDestination: '',
                responseReference: '',
                created_at: '',
                created_by: '',
                person_reception: '',
                dependency_charge: '',           
                showModal: 0,
                dataDocuments: [],
                dataRequirements: [],
                reception_date: '',
                code: 0,
                showInternalSent: 0,
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
                var from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            },  
            modalType(){
                switch(this.showModal){
                    case 1: {
                        return 'modal-document'
                    }
                    case 2: {
                        return 'modal-check-requirement'
                    }
                    case 3: {
                        return 'modal-check-annexe'
                    }
                }
            },          
        },
        methods : {
            getSent(page)
            {
                axios.get(`${this.ruta}/sent?page=${page}&search=${this.search}`)
                    .then((response) => {
                        this.arraySent  = response.data.sent.data;
                        this.pagination = response.data.pagination;
                        this.auth       = response.data.auth;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            showModalDocument(id) {
                this.showModal = 1;
                this.document_id = id;
            },
            closeModalDocument()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            showModalRequirement(id) {
                this.showModal = 2;
                this.document_id = id;
            },
            closeModalRequirement()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            showModalAnnexe(id) {
                this.showModal = 3;
                this.document_id = id;
            },
            closeModalAnnexe()
            {
                this.showModal = 0;
                this.document_id = 0;
            },
            selectedQuery()
            {
                if (this.query !== '') {
                    this.affair = this.query;
                    this.closeModalProcedure()
                }else {
                    this.validateTupa = 'Debe de elegir un tupa'
                }
            },
            changePage(page)
            {
                let me = this;
                me.pagination.current_page = page;
                me.getSent(page);
            },         
            closeModal()
            {
                this.modal                      = 0;
                this.titleModal                 = '';
                this.selectedDocumentType       = {id: 0, label: ''};
                this.selectedDependencyProvided = {id: 0, label: ''};
                this.shipping_date              = '';
                this.tupa                       = 0;
                this.requirements               = [];
                this.states                     = [];
                this.errors                     = '';
            },          
            addDocumentSent()
            {
                let me = this;
                axios.post(`${this.ruta}/addDocumentSent`,{
                    'dependency_shipping_id'   : this.dependency_shipping_id,
                    'dependency_reception_id'  : this.selectedDependencyProvided.id,
                    'provided_id'              : this.selectedProvided.id,
                    'document_id'              : this.document_id,
                    'documentary_procedure_id' : this.documentary_procedure_id,
                    'person_reception'         : this.dependency_charge,
                    'person_id'                : this.person_id
                }).then(() => {
                    me.closeModal();
                    me.getSent(1);
                }, function (error) {
                    me.errors = error.response.data.errors
                });
            },
            printDocument (id)
            {
                let me=this;
                window.open(`${me.ruta}/document/reportDocument/${id}`, '_blank');
            }      
        },
        mounted() {
            this.getSent(1);
        }
    }

</script>