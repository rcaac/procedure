<template>
    <div class="row">
        <div class="form-group col-sm-6">
            <label class="col-md-4 col-form-label">Entidad</label>
            <div class="col-md-12">
                <v-select
                    v-model="activity_user.selectedEntity"
                    :options="activity_user.arrayEntity"
                    label="description"
                    placeholder="Elije Entidad"
                    :clearable="false"
                    class="small"
                    @input="bringDependency(activity_user.selectedEntity.id)"
                />
                <div v-if="activity_user.errors && activity_user.errors.change_entity_id" class="text-danger">{{ activity_user.errors.change_entity_id[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-6 col-form-label">Asignar Dependencia</label>
            <div class="col-md-12">
                <v-select
                    v-model="activity_user.selectedDependencyExternal"
                    :options="activity_user.arrayDependencyExternal"
                    label="description"
                    class="small"
                    placeholder="Elije Dependencia"
                    :clearable="false"
                />
                <div v-if="activity_user.errors && activity_user.errors.dependency_external_id" class="text-danger">{{ activity_user.errors.dependency_external_id[0] }}</div>
                <div v-if="activity_user.validate" class="text-danger">{{ activity_user.validate}}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-4 col-form-label">Role</label>
            <div class="col-md-12">
                <select v-model="activity_user.role_external_id" class="form-control form-control-sm">
                    <option v-for="role in activity_user.arrayRoleExternal" :key="role.id" :value="role.id" v-text="role.description"></option>
                </select>
                <div v-if="activity_user.errors && activity_user.errors.role_external_id" class="text-danger">{{ activity_user.errors.role_external_id[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-4 col-form-label">Estado</label>
            <div class="col-md-12">
                <select v-model="activity_user.charge_state_id" class="form-control form-control-sm">
                    <option value="0" disabled>Seleccione</option>
                    <option v-for="state in activity_user.arrayState" :key="state.id" :value="state.id" v-text="state.charge"></option>
                </select>
                <div v-if="activity_user.errors && activity_user.errors.charge_state_id" class="text-danger">{{ activity_user.errors.charge_state_id[0] }}</div>
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
        props:{
            activity_user: {
                type: Object,
                required: true
            },
        },
        methods: {
            bringDependency(id){
                let me=this;
                this.activity_user.selectedDependencyExternal = {'id': 0, 'description': 'Elije Dependencia'};
                var url= me.activity_user.route + '/bringDependency/' + id;
                axios.get(url).then((response) => {
                    me.activity_user.arrayDependencyExternal = response.data.bring_dependency;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
        },
        mounted() {
            this.activity_user.validate= ''
        }
    }
</script>

