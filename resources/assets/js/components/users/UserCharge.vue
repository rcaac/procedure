<template>
    <tr class="user">
        <td>
            <span>{{charge.entity}}</span>
        </td>
        <td>
            <span v-if="!editable">{{charge.dependency}}</span>
            <div class="col-md-12" v-else>
                <select v-model="charge.dependency_id" class="form-control form-control-sm">
                    <option value="0" disabled>Seleccione</option>
                    <option
                        v-for="dependency in arrayDependency"
                        :key="dependency.id"
                        :value="dependency.id"
                        v-text="dependency.description"
                    >
                    </option>
                </select>
            </div>
        </td>
        <td>
            <span v-if="!editable" >{{charge.charge}}</span>
            <input v-else type="text" v-model="charge.charge" class="form-control form-control-sm">
        </td>
        <td>
            <span v-if="!editable" >{{charge.detail}}</span>
            <input v-else-if="editable && charge.detail !== null" type="text" v-model="charge.detail" class="form-control form-control-sm">
            <span v-else>{{charge.detail}}</span>
        </td>
        <td>
            <span v-if="!editable" >{{charge.startDate}}</span>
            <input v-else-if="editable && charge.startDate !== null" type="date" v-model="charge.startDate" class="form-control form-control-sm">
            <span v-else>{{charge.startDate}}</span>
        </td>
        <td>
            <span v-if="!editable" >{{charge.finalDate}}</span>
            <input v-else-if="editable && charge.finalDate !== null" type="date" v-model="charge.finalDate" class="form-control form-control-sm">
            <span v-else>{{charge.finalDate}}</span>
        </td>
        <td>
            <div v-if="charge.state==='Activo'">
                <span v-if="!editable" class="badge badge-pill badge-success">{{charge.state}}</span>
                <div class="col-md-12" v-else>
                    <select v-model="charge.charge_state_id" class="form-control form-control-sm">
                        <option value="0" disabled>Seleccione</option>
                        <option
                            v-for="state in arrayState"
                            :key="state.id"
                            :value="state.id"
                            v-text="state.charge"
                        >
                        </option>
                    </select>
                </div>
            </div>
            <div v-else>
                <span v-if="!editable" class="badge badge-pill badge-danger">{{charge.state}}</span>
                <div class="col-md-12" v-else>
                    <select v-model="charge.charge_state_id" class="form-control form-control-sm">
                        <option value="0" disabled>Seleccione</option>
                        <option
                            v-for="state in arrayState"
                            :key="state.id"
                            :value="state.id"
                            v-text="state.charge"
                        >
                        </option>
                    </select>
                </div>
            </div>
        </td>
        <td>
            <span v-if="!editable">{{charge.role}}</span>
            <div class="col-md-12" v-else>
                <select v-model="charge.role_id" class="form-control form-control-sm">
                    <option value="0" disabled>Seleccione</option>
                    <option
                        v-for="role in arrayRole"
                        :key="role.id"
                        :value="role.id"
                        v-text="role.description"
                    >
                    </option>
                </select>
            </div>
        </td>
        <td>
            <button
                v-if="editable"
                type="button"
                class="btn btn-outline-success btn-sm"
                @click="toggleEditable(); $emit('edit', charge)">
                <i class="fa fa-refresh"></i> Actualizar
            </button>
            <div v-else>
                <button type="button" class="btn btn-outline-primary btn-sm" @click="toggleEditable()">
                    <i class="icon-pencil"></i> Editar
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" @click="$emit('remove', charge.charge_id)">
                    <i class="icon-trash"></i> Eliminar
                </button>
            </div>

        </td>
    </tr>
</template>

<script>
   export default {
       props: ['charge', 'arrayDependency', 'arrayState', 'arrayRole', 'errors'],
       data() {
           return {
               editable: false,
           }
       },
       methods: {
           toggleEditable()
           {
               this.editable = !this.editable;
           },
       }
   }
</script>


