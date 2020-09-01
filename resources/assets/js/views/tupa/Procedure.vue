<template>
    <main class="main">
        <ol></ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-institution"></i> PROCEDIMIENTOS&nbsp;&nbsp;
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" v-model="search" @keyup="listProcedure(1, module_change_id)" class="form-control form-control-sm" placeholder="Texto a buscar">
                                <button type="button" class="btn btn-info btn-sm" style="color: white"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select v-model="module_change_id" class="form-control form-control-sm" @change="listProcedure(1, module_change_id)">
                                <option :value=0>Todos los módulos</option>
                                <option
                                    v-for="change in arrayChangeModules"
                                    :key="change.id"
                                    :value="change.id"
                                    v-text="change.description"
                                >
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th><small><b>CALIFICACIÓN</b></small></th>
                            <th><small><b>PROCEDIMIENTO</b></small></th>
                            <th><small><b>NOTA</b></small></th>
                            <th><small><b>BASE LEGAL</b></small></th>
                            <th><small><b>PLAZO</b></small></th>
                            <th><small><b>ACCIONES</b></small></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="procedure in arrayProcedure" :key="procedure.id">
                            <td><small>{{ procedure.qualification }}</small></td>
                            <td v-text="procedure.description"></td>
                            <td v-text="procedure.note"></td>
                            <td v-text="procedure.legal_base"></td>
                            <td v-text="procedure.term"></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" @click="openModal('procedure','update',procedure)">
                                    <i class="icon-pencil"></i> Editar
                                </button> &nbsp;
                                <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteProcedure(procedure.id)">
                                    <i class="icon-trash"></i> Eliminar
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
                                <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'show' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-info modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Módulo</label>
                                <div class="col-md-9">
                                    <select v-model="module_id" class="form-control form-control-sm">
                                        <option
                                            v-for="modules in arrayModules"
                                            :key="modules.id"
                                            :value="modules.id"
                                            v-text="modules.description"
                                        >
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Calificación</label>
                                <div class="col-md-9">
                                    <select v-model="procedure_qualification_id" class="form-control form-control-sm">
                                        <option
                                            v-for="qualification in arrayProcedureQualifications "
                                            :key="qualification.id"
                                            :value="qualification.id"
                                            v-text="qualification.description"
                                        >
                                        </option>
                                    </select>
                                    <div v-if="errors && errors.procedure_qualification_id" class="text-danger">{{ errors.procedure_qualification_id[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Procedimiento</label>
                                <div class="col-md-9">
                                    <textarea type="text" v-model="procedure" rows="2" class="form-control form-control-sm"></textarea>
                                    <div v-if="errors && errors.procedure" class="text-danger">{{ errors.procedure[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Nota</label>
                                <div class="col-md-9">
                                    <textarea type="text" v-model="note" rows="2" class="form-control form-control-sm"></textarea>
                                    <div v-if="errors && errors.note" class="text-danger">{{ errors.note[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Base Legal</label>
                                <div class="col-md-9">
                                    <textarea type="text" v-model="base" rows="2" class="form-control form-control-sm"></textarea>
                                    <div v-if="errors && errors.base" class="text-danger">{{ errors.base[0] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 form-control-label">Plazo para resolver</label>
                                <div class="col-md-2">
                                    <input type="text" v-model="term" class="form-control form-control-sm">
                                    <div v-if="errors && errors.term" class="text-danger">{{ errors.term[0] }}</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()" style="color: white">
                            <i class="icon-close"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-info btn-sm" @click="updateProcedure()" style="color: white">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>
                 <!--/.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data () {
            return {
                id: 0,
                module_id: 0,
                module_change_id: 0,
                procedure_qualification_id: 0,
                procedure: '',
                note: '',
                base: '',
                term: '',
                modal: 0,
                titleModal : '',
                typeAction : 0,
                arrayProcedure: [],
                arrayProcedureQualifications: [],
                arrayModules: [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterion : '',
                search : '',
                errors: [],
                arrayChangeModules: []
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
        methods : {
            listProcedure(page) {
                let me=this;
                var url= this.ruta + '/listProcedure?page=' + page + '&search=' + this.search + '&module=' + this.module_change_id;
                axios.get(url).then((response) => {
                    var res= response.data;
                    me.arrayProcedure = res.procedure.data;
                    me.pagination     = res.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listProcedureQualifications() {
                let me=this;
                var url= this.ruta + '/listProcedureQualification';
                axios.get(url).then((response) => {
                    me.arrayProcedureQualifications = response.data.procedure_qualifications;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectModules() {
                let me=this;
                var url= this.ruta + '/selectedModules';
                axios.get(url).then((response) => {
                    me.arrayModules       = response.data.modules;
                    me.arrayChangeModules = response.data.modules;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page) {
                let me = this;
                me.pagination.current_page = page;
                me.listProcedure(page);
            },
            updateProcedure() {
                let me = this;
                axios.put(this.ruta + '/procedure/update',{
                    'description':                this.procedure,
                    'note':                       this.note,
                    'legal_base':                 this.base,
                    'term':                       this.term,
                    'id':                         this.id,
                    'module_id':                  this.module_id,
                    'procedure_qualification_id': this.procedure_qualification_id
                }).then(() => {
                    me.listProcedure(1);
                    me.closeModal();
                },function (error) {
                    me.errors = error.response.data.errors
                });
            },
            closeModal() {
                this.modal                        = 0;
                this.titleModal                   = '';
                this.procedure                    = '';
                this.note                         = '';
                this.base                         = '';
                this.term                         = '';
                this.id                           = 0;
                this.module_id                    = 0;
                this.procedure_qualification_id   = 0;
                this.errors                       = []
            },
            openModal(model, action, data = []) {
                switch (model) {
                    case "procedure":
                    {
                        switch (action) {
                            case 'update':
                            {
                                this.modal                      = 1;
                                this.titleModal                 = 'Actualizar Procedimiento';
                                this.procedure                  = data['description'];
                                this.note                       = data['note'];
                                this.base                       = data['legal_base'];
                                this.term                       = data['term'];
                                this.id                         = data['id'];
                                this.module_id                  = data['module_id'];
                                this.procedure_qualification_id = data['procedure_qualification_id'];
                                break;
                            }
                        }
                    }
                }
            },
            deleteProcedure(id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar este registro?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.patch(this.ruta + '/trashProcedure/' + id).then(() => {
                            me.listProcedure(1);
                            Swal.fire(
                                'Eliminado!',
                                'El registro ha sido eliminado con éxito.',
                                'success'
                            )
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {

                    }
                })
            }
        },
        mounted() {
            this.listProcedure(1);
            this.selectModules();
            this.listProcedureQualifications();
        }
    }
</script>
<style scoped>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
</style>


