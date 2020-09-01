<template>    
    <tr>
        <td v-if="requirement.file">
            <a :href="`https://munipillcomarca.s3.us-east-2.amazonaws.com/${requirement.file}`" target="_blank">{{ requirement.description }}</a>
        </td>
        <td v-else>{{ requirement.description }}</td>
        <td v-text="requirement.cost"/>
        <td v-if="!editable" v-text="requirement.observation"/>
        <td v-else>
            <textarea
                rows="1"
                class="form-control form-control-sm"
                v-model="requirement.observation"
            />
        </td>
        <td v-text="requirement.date"/>
        <td v-if="requirement.state === 1">
            <input
                type="checkbox"
                v-model="requirement.state"
                :true-value="1"
                :false-value="2"
                @change="$emit('onChecked', requirement.id, requirement.state, requirement.document_id)"
            >
        </td>
        <td v-if="requirement.state === 2">
            <input
                type="checkbox"
                v-model="requirement.state"
                :true-value="1"
                :false-value="2"
                @change="$emit('onChecked', requirement.id, requirement.state, requirement.document_id)"
            >
        </td>
        <td>
            <button
                v-if="editable"
                type="button"
                class="btn btn-outline-success btn-sm"
                @click="toggleEditable(); $emit('edit', requirement)">
                <i class="fa fa-refresh"/> Actualizar
            </button>
            <div v-else>
                <button type="button" class="btn btn-outline-primary btn-sm" @click="toggleEditable()">
                    <i class="icon-pencil"/> Editar
                </button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {
        props: ['requirement'],
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



