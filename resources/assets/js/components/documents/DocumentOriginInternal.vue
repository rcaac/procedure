<template>
    <div>
        <br>
        <div class="form-group row">
            <label class="col-md-2 form-control-label">Tipo de Dependencia:</label>
            <div class="col-md-6">
                <select v-model="origin_data.dependency_type_id" class="form-control form-control-sm" @change="dependencyType">
                    <option value="0" disabled>Elije Tipo de Dependencia</option>
                    <option
                        v-for="type in origin_data.arrayDependencyTypes"
                        :key="type.id"
                        :value="type.id"
                        v-text="type.description"
                    >
                    </option>
                </select>
                <div v-if="origin_data.errors && origin_data.errors.dependency_type_id" class="text-danger">{{ origin_data.errors.dependency_type_id[0] }}</div>
            </div>
        </div>
        <div class="form-group row" v-if="origin_data.arrayDependencies.length">
            <label class="col-md-2 form-control-label">Dependencia:</label>
            <div class="col-md-6">
                <v-select
                    v-model="origin_data.selectedDependency"
                    :options="origin_data.arrayDependencies"
                    placeholder="Elije una Dependencia"
                    @input="$emit('dependencyCharge', origin_data.selectedDependency.id)"
                    class="small"
                    :clearable="false"
                />
                <div v-if="origin_data.errors && origin_data.errors.dependency_shipping_id" class="text-danger">{{ origin_data.errors.dependency_shipping_id[0] }}</div>
            </div>
        </div>
        <div class="form-group row" v-if="Object.keys(origin_data.dependency_charge).length">
            <label class="col-md-2 form-control-label">Responsable:</label>
            <div class="col-md-6">
                <input type="text" v-model="origin_data.dependency_charge.fullName" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row" v-if="Object.keys(origin_data.dependency_charge).length">
            <label class="col-md-2 form-control-label">Cargo:</label>
            <div class="col-md-6">
                <input type="text" v-model="origin_data.dependency_charge.charge" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</template>

<script>
    import vSelect from 'vue-select'
    export default {
        components: {
            vSelect
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            }
        },
        methods: {
            dependencyType(){
                this.origin_data.selectedDependency = '';
                this.origin_data.dependency_charge = {};
                let me=this;
                var url= me.origin_data.route + '/dependencyBringType/' + this.origin_data.dependency_type_id;
                axios.get(url).then((response) => {
                    me.origin_data.arrayDependencies   = response.data.dependencies;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
        },
        mounted() {
            this.origin_data.dependency_type_id  = 0;
            this.origin_data.arrayDependencies   = [];
            this.origin_data.dependency_charge   = ''
        }
    }
</script>

