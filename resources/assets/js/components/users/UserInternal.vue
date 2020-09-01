<template>
    <div class="row">
        <div class="form-group col-sm-6">
            <label class="col-md-6 col-form-label">Asignar Dependencia</label>
            <div class="col-md-12">
                <v-select
                    v-model="activity_user.selectedDependencyInternal"
                    :options="activity_user.arrayDependencyInternal"
                    label="description"
                    :clearable="false"
                    class="small"
                />
                <div v-if="activity_user.errors && activity_user.errors.dependency_internal_id" class="text-danger">{{ activity_user.errors.dependency_internal_id[0] }}</div>
                <div v-if="activity_user.validate" class="text-danger">{{ activity_user.validate}}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-8 col-form-label">Cargo</label>
            <div class="col-md-12">
                <input type="text" v-model="activity_user.charge" class="form-control form-control-sm" placeholder="Ingrese cargo">
                <div v-if="activity_user.errors && activity_user.errors.charge" class="text-danger">{{ activity_user.errors.charge[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-8 col-form-label">Documento Sustentatorio</label>
            <div class="col-md-12">
                <input type="text" v-model="activity_user.detail" class="form-control form-control-sm" placeholder="Ingrese documento">
                <div v-if="activity_user.errors && activity_user.errors.detail" class="text-danger">{{ activity_user.errors.detail[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-8 col-form-label">Fecha de Inicio de Contrato</label>
            <div class="col-md-12">
                <input type="date" v-model="activity_user.startDate" class="form-control form-control-sm" placeholder="Inicio del contrato">
                <div v-if="activity_user.errors && activity_user.errors.startDate" class="text-danger">{{ activity_user.errors.startDate[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label class="col-md-8 col-form-label">Fecha de Finalización de Contrato</label>
            <div class="col-md-12">
                <input type="date" v-model="activity_user.finalDate" class="form-control form-control-sm" placeholder="Final del contrato">
                <div v-if="activity_user.errors && activity_user.errors.finalDate" class="text-danger">{{ activity_user.errors.finalDate[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label class="col-md-4 col-form-label">Role</label>
            <div class="col-md-12">
                <select v-model="activity_user.role_internal_id" class="form-control form-control-sm">
                    <option value="0" disabled>Seleccione</option>
                    <option v-for="role in activity_user.arrayRoleInternal" :key="role.id" :value="role.id" v-text="role.description"/>
                </select>
                <div v-if="activity_user.errors && activity_user.errors.role_internal_id" class="text-danger">{{ activity_user.errors.role_internal_id[0] }}</div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label class="col-md-4 col-form-label">Estado</label>
            <div class="col-md-12">
                <select v-model="activity_user.charge_state_id" class="form-control form-control-sm">
                    <option value="0" disabled>Seleccione</option>
                    <option
                        v-for="state in activity_user.arrayState"
                        :key="state.id"
                        :value="state.id"
                        v-text="state.charge"
                    >
                    </option>
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
        mounted() {
            this.activity_user.validate= ''
        }
    }
</script>
