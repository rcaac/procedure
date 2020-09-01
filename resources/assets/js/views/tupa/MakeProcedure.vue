<template>
    <main class="main">
        <ol></ol>
        <div class="container-fluid">
            <div id="ui-view">
                <div class="card">
                    <div class="card-header">
                        <i class="icon-list"></i> CREAR PROCEDIMIENTO
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-info">PROCEDIMIENTOS</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-10">
                                                <v-select
                                                    v-model="selectedProcedureList"
                                                    :options="arrayProcedureLists"
                                                    placeholder="Elije un procedimiento"
                                                    :clearable="false"
                                                    class="small"
                                                    label="description"
                                                    @input="listDependentTheProcedure(1, selectedProcedureList.id)"
                                                />
                                                <div v-if="errors && errors.procedure_id" class="text-danger">{{ errors.procedure_id[0] }}</div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-info btn-sm"
                                                    style="color: white; float: right"
                                                    @click="openModal('procedure','register')"
                                                >
                                                    <i class="icon-plus"></i> Procedimiento
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-info">REQUISITOS</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-2">
                                                <button type="button" class="btn btn-info btn-sm" style="color: white" @click="openModal('requirement','register')">
                                                    <i class="icon-plus"></i> Requisito
                                                </button>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>REQUISITOS</th>
                                                            <th>COSTO</th>
                                                            <th>ACCIONES</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="lists in arrayProcedureRequirements" :key="lists.id">
                                                            <td v-text="lists.description"></td>
                                                            <td v-text="lists.cost"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-outline-info btn-sm" @click="openModal('procedureRequirement','update',lists)" >
                                                                    <i class="icon-pencil"></i> Editar
                                                                </button>
                                                                <button type="button" class="btn btn btn-outline-danger btn-sm" @click="deleteRequirement(lists.id)">
                                                                    <i class="icon-trash"></i> Eliminar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                </div>
                                                <nav>
                                                    <ul class="pagination" v-if="arrayProcedureRequirements.length">
                                                        <li class="page-item" v-if="pagination.current_page > 1">
                                                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)"><<</a>
                                                        </li>
                                                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page === isActived ? 'active' : '']">
                                                            <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"></a>
                                                        </li>
                                                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                                            <a class="page-link"  href="#" @click.prevent="changePage(pagination.current_page + 1)">>></a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 col-md-10 m-md-auto">
                                <div class="card">
                                    <div class="card-header text-white bg-info">RESPONSABLES</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <v-select
                                                    v-model="selectedDependency"
                                                    :options="arrayDependencies"
                                                    placeholder="Elije dependencia responsable"
                                                    :clearable="false"
                                                    label="description"
                                                    class="small"
                                                />
                                                <div v-if="errors && errors.dependency_id" class="text-danger">{{ errors.dependency_id[0] }}</div>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <select v-model="attention_type_id" class="form-control form-control-sm" style="margin-right: 15%" @change="listAttentionTypes(attention_type_id)">
                                                    <option value="0" disabled>Elije tipo de recurso</option>
                                                    <option
                                                        v-for="type in arrayAttentionTypes"
                                                        :key="type.id"
                                                        :value="type.id"
                                                        v-text="type.description"
                                                    >
                                                    </option>
                                                </select>
                                                <div v-if="errors && errors.attention_type_id" class="text-danger">{{ errors.attention_type_id[0] }}</div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-info btn-sm"
                                                    @click="registerAttentionProcedure()"
                                                    style="color: white; float: right"
                                                >
                                                    <i class="fa fa-save"></i> Guardar
                                                </button>
                                            </div>
                                            <div class="form-group col-sm-6" v-if="resource.resourceDetail===1">
                                                <textarea v-model="detail" rows="1" class="form-control form-control-sm" placeholder="Detalles..."></textarea>
                                                <div v-if="errors && errors.detail" class="text-danger">{{ errors.detail[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                    <tr>
                                                        <th>RESUELVE EL RECURSO</th>
                                                        <th>RECURSO</th>
                                                        <th>DETALLE</th>
                                                        <th>ACCIONES</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="attention in arrayAttentionProcedures" :key="attention.id">
                                                        <td v-text="attention.description"></td>
                                                        <td v-text="attention.types"></td>
                                                        <td v-text="attention.detail"></td>
                                                        <td v-text="attention.resource_detail" style="display:none;"></td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-info btn-sm" @click="openModal('attentionProcedure','update',attention)">
                                                                <i class="icon-pencil"></i> Editar
                                                            </button>
                                                            <button type="button" class="btn btn btn-outline-danger btn-sm" @click="deleteResource(attention.id)">
                                                                <i class="icon-trash"></i> Eliminar
                                                            </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'show-procedure' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-info modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="titleModal==='Registrar Procedimiento'">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="col-md-8 col-form-label">Módulo</label>
                                    <div class="col-md-12">
                                        <select v-model="module_id" class="form-control form-control-sm">
                                            <option value="0" disabled>Seleccione un módulo</option>
                                            <option
                                                v-for="mod in arrayModule"
                                                :key="mod.id"
                                                :value="mod.id"
                                                v-text="mod.description"
                                            >
                                            </option>
                                        </select>
                                        <div v-if="errors && errors.module_id" class="text-danger">{{ errors.module_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-8 col-form-label">Calificación</label>
                                    <div class="col-md-12">
                                        <select v-model="procedure_qualification_id" class="form-control form-control-sm">
                                            <option value="0" disabled>Seleccione una calificación</option>
                                            <option
                                                v-for="qualification in arrayProcedureQualifications"
                                                :key="qualification.id"
                                                :value="qualification.id"
                                                v-text="qualification.description"
                                            >
                                            </option>
                                        </select>
                                        <div v-if="errors && errors.procedure_qualification_id" class="text-danger">{{ errors.procedure_qualification_id[0] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="col-md-2 form-control-label">Procedimiento</label>
                                    <div class="col-md-12">
                                        <textarea v-model="description" rows="1" class="form-control form-control-sm" placeholder="Ingrese el procedimiento"></textarea>
                                        <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-2 form-control-label">Nota</label>
                                    <div class="col-md-12">
                                        <textarea v-model="note" rows="1" class="form-control form-control-sm" placeholder="Ingrese nota"></textarea>
                                        <div v-if="errors && errors.note" class="text-danger">{{ errors.note[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-4 form-control-label">Base Legal</label>
                                    <div class="col-md-12">
                                        <textarea v-model="legal_base" rows="1" class="form-control form-control-sm" placeholder="Ingrese base legal"></textarea>
                                        <div v-if="errors && errors.legal_base" class="text-danger">{{ errors.legal_base[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-12 form-control-label">Plazo Para Resolver</label>
                                    <div class="input-group col-md-5">
                                        <input v-model="term" class="form-control form-control-sm" placeholder="1 ó 2 ó 3 ...">&nbsp;(días)
                                        <div v-if="errors && errors.term" class="text-danger">{{ errors.term[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-body" v-if="titleModal==='Registrar Requisito'">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <template  v-if="errors">
                                <div v-for="(error, key, index) in errors" :key="key">
                                    <div v-if="errors['procedure_id']">
                                        <div class="text-danger" v-if="errors['procedure_id'] && index === 0">{{error[0]}}</div>
                                        <div class="text-danger" v-else>{{error[0]}}</div>
                                    </div>
                                    <div v-else>
                                        <div class="text-danger">{{error[0]}}</div>
                                    </div>
                                </div>
                            </template>
                            <div class="form-group row" v-for="(items,k) in inputs" :key="k">
                                <div class="col-md-8">
                                    <textarea v-model="items.description" rows="1" class="form-control form-control-sm" :placeholder="'Ingrese requisito '+(k+1)"></textarea>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" v-model="items.cost" class="form-control form-control-sm" placeholder="0.0">
                                </div>
                                <div class="col-md-2">
                                    <span>
                                        <button type="button" class="btn btn-danger btn-sm" style="color: white" @click="remove(k)" v-show="k || ( !k && inputs.length > 1)">
                                            <i class="icon-close"></i>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" style="color: white" @click="add()" v-show="k === inputs.length-1">
                                            <i class="icon-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-body" v-if="titleModal==='Actualizar Requisitos'">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <div class="form-group col-sm-8">
                                    <label class="col-md-4 form-control-label">Requisito</label>
                                    <div class="col-md-12">
                                        <textarea v-model="description" rows="1" class="form-control form-control-sm" placeholder="Ingrese el procedimiento"></textarea>
                                        <div v-if="errors && errors.description" class="text-danger">{{ errors.description[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="col-md-12 form-control-label">Costo (S/.)</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="cost" class="form-control form-control-sm" placeholder="Costo">
                                        <div v-if="errors && errors.cost" class="text-danger">{{ errors.cost[0] }}</div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-body" v-if="titleModal==='Actualizar Recursos'">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <div class="form-group col-sm-6">
                                    <label class="col-md-12 form-control-label">Dependencia Responsable</label>
                                    <div class="col-md-12">
                                        <v-select
                                            v-model="selectedUpdatedDependency"
                                            :options="arrayDependency"
                                            placeholder="Seleccione un procedimiento"
                                            :clearable="false"
                                            class="small"
                                        />
                                        <div v-if="errors && errors.dependency_id" class="text-danger">{{ errors.dependency_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-md-6 form-control-label">Tipo de Recurso</label>
                                    <div class="col-md-12">
                                        <select v-model="attention_type_id" class="form-control form-control-sm" @change="listAttentionTypes(attention_type_id)">
                                            <option value="0" disabled>Seleccione tipo de atención</option>
                                            <option
                                                v-for="type in arrayAttentionTypes"
                                                :key="type.id"
                                                :value="type.id"
                                                v-text="type.description"
                                            >
                                            </option>
                                        </select>
                                        <div v-if="errors && errors.attention_type_id" class="text-danger">{{ errors.attention_type_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" v-if="resource.resourceDetail === 1">
                                    <label class="col-md-2 form-control-label">Detalle</label>
                                    <div class="col-md-12">
                                        <textarea v-model="detail" rows="1" class="form-control form-control-sm" placeholder="No aplica plazo"></textarea>
                                        <div v-if="errors && errors.detail" class="text-danger">{{ errors.detail[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" style="color: white" @click="closeModal()">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" v-if="typeAction === 1 && titleModal==='Registrar Procedimiento'" style="color: white" class="btn btn-info btn-sm" @click="registerProcedure()">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 1 && titleModal==='Registrar Requisito'" style="color: white" class="btn btn-info btn-sm" @click="registerRequirement()">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" v-if="typeAction === 2 && titleModal==='Actualizar Requisitos'" style="color: white" class="btn btn-info btn-sm" @click="updateRequirement()">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                        <button type="button" v-if="typeAction === 2 && titleModal==='Actualizar Recursos'" style="color: white" class="btn btn-info btn-sm" @click="updateResource()">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>
    import vSelect from 'vue-select';
    import 'vue-select/dist/vue-select.css';
    export default {
        components: {
            vSelect,
        },
        props : ['ruta'],
        data () {
            return {
                cost: '',
                note: '',
                term: '',
                legal_base: '',
                resource_detail: 0,
                detail: '',
                module_id: 0,
                procedure_qualification_id: 0,
                procedure_requirement_id:0,
                dependency_id: 0,
                requirement_id: 0,
                attention_type_id: 0,
                attention_procedure_id: 0,
                attention_detail_id: 0,
                resource: {},
                arrayModule: [],
                arrayDependencies: [],
                arrayProcedureQualifications: [],
                arrayProcedureLists: [],
                arrayRequirements: [],
                arrayProcedureRequirements: [],
                arrayAttentionTypes: [],
                arrayAttentionProcedures: [],
                inputs: [
                    {
                        description: '',
                        cost: ''
                    }
                ],
                modal : 0,
                description: '',
                titleModal : '',
                typeAction : 0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                errors: [],
                criterion : 0,
                selectedDependency: '',
                selectedProcedureList: {
                    id: 0,
                    description: 'Elije Procedimiento'
                },
                selectedUpdatedDependency: {
                    id: 0,
                    label: ''
                }
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
            }
        },
        methods: {
            listProcedure(){
                axios.get(`${this.ruta}/procedure`)
                    .then((response) => {
                        this.arrayModule                  = response.data.modules;
                        this.arrayProcedureQualifications = response.data.qualifications;
                        this.arrayProcedureLists          = response.data.procedures;
                        this.arrayDependencies            = response.data.dependencies;
                        this.arrayDependency              = response.data.dependency;
                        this.arrayAttentionTypes          = response.data.attentionTypes;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            listDependentTheProcedure(page, id){
                axios.get(`${this.ruta}/dependentTheProcedure/?page=${page}&id=${id}`)
                    .then((response) => {
                        this.arrayProcedureRequirements = response.data.procedureRequirements.data;
                        this.arrayAttentionProcedures   = response.data.attentionProcedures;
                        this.pagination                 = response.data.pagination;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            listAttentionTypes(id)
            {
                axios.get(`${this.ruta}/procedure/attentionTypes/${id}`)
                    .then((response) => {
                        this.resource = response.data
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            registerProcedure(){

                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                axios.post(`${this.ruta}/procedure/register`,{
                    'module_id':                  this.module_id,
                    'procedure_qualification_id': this.procedure_qualification_id,
                    'description':                this.description,
                    'note':                       this.note,
                    'legal_base':                 this.legal_base,
                    'term':                       this.term
                }).then(() => {
                    this.closeModal();
                    this.listProcedure();
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            registerRequirement(){
                axios.post(`${this.ruta}/requirement/register`,{
                    'inputs': this.inputs,
                    'procedure_id': this.selectedProcedureList.id,
                }).then(() => {
                    this.closeModal();
                    this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                }, (error) => {
                    this.errors = error.response.data.errors;
                });
            },
            registerAttentionProcedure(){
                if(this.wait){
                    return
                }
                this.wait = true;
                setTimeout(() => this.wait = false, 1000);

                axios.post(`${this.ruta}/attentionProcedure/register`,{
                    'detail':            this.detail,
                    'dependency_id':     this.selectedDependency.id,
                    'procedure_id':      this.selectedProcedureList.id,
                    'attention_type_id': this.attention_type_id
                }).then(() => {
                    this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                    this.attention_type_id  = 0;
                    this.selectedDependency = null;
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            updateRequirement(){
                axios.put(`${this.ruta}/procedureRequirement/update`,{
                    'procedure_requirement_id': this.procedure_requirement_id,
                    'procedure_id'            : this.selectedProcedureList.id,
                    'requirement_id'          : this.requirement_id,
                    'description'             : this.description,
                    'cost'                    : this.cost,
                }).then(() => {
                    this.closeModal();
                    this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            deleteRequirement(id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar este registro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        axios.patch(`${this.ruta}/trashRequirement/${id}`)
                            .then(() => {
                                this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                                Swal.fire(
                                    'Eliminado!',
                                    'El registro ha sido eliminado con éxito.',
                                    'success'
                                )
                        }).catch((error) => {
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {

                    }
                })
            },
            updateResource(){
                axios.put(`${this.ruta}/attentionProcedure/update`,{
                    'procedure_id':           this.procedure_id,
                    'dependency_id':          this.selectedUpdatedDependency.id,
                    'attention_type_id':      this.attention_type_id,
                    'detail':                 this.detail,
                    'attention_detail_id':    this.attention_detail_id,
                    'resource_detail':        this.resource_detail,
                    'attention_procedure_id': this.attention_procedure_id
                }).then(() => {
                    this.closeModal();
                    this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                }, (error) => {
                    this.errors = error.response.data.errors
                });
            },
            deleteResource(id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar este registro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value) {
                        axios.patch(`${this.ruta}/trashAttentionProcedure/${id}`)
                            .then(() => {
                                this.listDependentTheProcedure(1, this.selectedProcedureList.id);
                                Swal.fire(
                                    'Eliminado!',
                                    'El registro ha sido eliminado con éxito.',
                                    'success'
                                )
                        }).catch((error) => {
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {

                    }
                })
            },
            closeModal(){
                this.modal                      = 0;
                this.module_id                  = 0;
                this.procedure_qualification_id = 0;
                this.titleModal                 = '';
                this.description                = '';
                this.note                       = '';
                this.legal_base                 = '';
                this.term                       = '';
                this.cost                       = '';
                this.requirement_id             = 0;
                this.procedure_requirement_id   = 0;
                this.dependency_id              = 0;
                this.attention_type_id          = 0;
                this.attention_procedure_id     = 0;
                this.detail                     = '';
                this.resource                   = {};
                this.errors                     = [];
                this.inputs                     = [{description: ''}];
                this.selectedDependency         = { }
            },
            openModal(model, action, data = []){
                if(model === 'procedure' || model === 'procedureRequirement')
                    {
                        switch(action){
                            case 'register':
                            {
                                this.modal       = 1;
                                this.titleModal  = 'Registrar Procedimiento';
                                this.typeAction  = 1;
                                this.description = '';
                                this.note        = '';
                                this.legal_base  = '';
                                this.term        = '';
                                break;
                            }
                            case 'update':
                            {
                                this.modal                    = 1;
                                this.titleModal               = 'Actualizar Requisitos';
                                this.typeAction               = 2;
                                this.cost                     = data['cost'];
                                this.description              = data['description'];
                                this.requirement_id           = data['id'];
                                this.procedure_requirement_id = data['procedure_requirement_id'];
                                break;
                            }
                        }
                    }
                if (model === 'requirement' || model === 'attentionProcedure')
                    {
                    switch(action){
                        case 'register':
                        {
                            this.modal       = 1;
                            this.titleModal  = 'Registrar Requisito';
                            this.typeAction  = 1;
                            this.description = '';
                            break;
                        }
                        case 'update':
                        {
                            this.listAttentionTypes(data['attention_type_id']);
                            this.modal                            = 1;
                            this.titleModal                       = 'Actualizar Recursos';
                            this.typeAction                       = 2;
                            this.detail                           = data['detail'];
                            this.procedure_id                     = data['procedure_id'];
                            this.selectedUpdatedDependency.id     = data['dependency_id'];
                            this.selectedUpdatedDependency.label  = data['description'];
                            this.attention_type_id                = data['attention_type_id'];
                            this.attention_procedure_id           = data['id'];
                            this.attention_detail_id              = data['attention_detail_id'];
                            this.resource_detail                  = data['resource_detail'];
                            break;
                        }
                    }
                }
            },
            add() {
                this.inputs.push({ description: '' });
            },
            remove(index) {
                this.inputs.splice(index, 1);
            },
            changePage(page) {
                let me = this;
                me.pagination.current_page = page;
                me.listDependentTheProcedure(page, this.selectedProcedureList.id);
            },
        },
        mounted() {
            this.listProcedure();
        }
    }
</script>

<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show-procedure{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed;
        background-color: #3c29297a !important;
    }

    .pagination li.active a {
        z-index: 2;
        color: #fff;
        background-color: #63c2de;
        border-color: #63c2de;
    }

    .pagination li a {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #63c2de;
        background-color: #fff;
        border: 1px solid #ddd;
    }
</style>
