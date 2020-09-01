<template>
    <main class="main">
    <ol></ol>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Dependencias Eliminadas
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" v-model="search" @keyup="listDependencyTrashed(1,search)" class="form-control form-control-sm" placeholder="Texto a buscar">
                            <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                    <tr>
                        <th>Código</th>
                        <th>Dependencia</th>
                        <th>Dependencia</th>
                        <th width="20px">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="dependency in arrayDependencyTrashed" :key="dependency.id">
                        <td v-text="dependency.code"></td>
                        <td v-text="dependency.description"></td>
                        <td v-text="dependency.dependency"></td>
                        <td>&nbsp;
                            <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteDependency(dependency.id)">
                                <i class="icon-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1,search,criterion)">Anterior</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="changePage(page,search,criterion)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1,search,criterion)">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data: function () {
            return {
                id: 0,
                code: '',
                description: '',
                dependency: '',
                arrayDependencyTrashed: [],
                pagination: {
                    'total': 0,
                    'current_page': 0,
                    'per_page': 0,
                    'last_page': 0,
                    'from': 0,
                    'to': 0,
                },
                offset: 3,
                search: '',
                criterion: ''
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
            listDependencyTrashed(page, search) {
                let me=this;
                var url= this.ruta + '/dependency/trashed?page=' + page + '&search=' + search;
                axios.get(url).then((response) => {
                    var res               = response.data.listTrashed;
                    me.arrayDependencyTrashed = res.data;
                    me.pagination         = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage (page) {
                let me                     = this;
                me.pagination.current_page = page;
                me.listDependencyTrashed(page, this.search);
            },
            deleteDependency (id) {
                Swal.fire({
                    title: 'Esta seguro de eliminar este registro de la base de datos?',
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
                        axios.delete(this.ruta + '/dependency/' + id + '/delete').then(() => {
                            me.listDependencyTrashed(1,this.search);
                            Swal.fire(
                                'Eliminado!',
                                'El registro ha sido eliminado con éxito de la base de datos.',
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
        mounted () {
            this.listDependencyTrashed(1, this.search);
        }
    }
</script>
