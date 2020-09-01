<template>
    <div>
        <div class="row" :class="{'form-group' : origin_data.ArrayReferences}">
            <label class="col-md-2 form-control-label">Adjuntar Documento:</label>
            <div class="col-md-3">
                <div class="input-group">
                    <input
                        type="text"
                        v-model="origin_data.registry"
                        class="form-control form-control-sm"
                        placeholder="Buscar por Registro"
                        @keyup.enter="$emit('queryReference',origin_data.registry)"
                    >
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="$emit('queryReference', origin_data.registry)"
                    >
                        <i class="fa fa-search"/> Buscar
                    </button>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label class="switch switch switch-3d switch-primary">
                    <input type="checkbox" class="switch-input" v-model="origin_data.append">
                    <span class="switch-label"/>
                    <span class="switch-handle"/>
                </label>
                &nbsp;Anexar
            </div>
            <div class="form-group col-md-1">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="$emit('add', origin_data.registry)"
                >
                    <i class="fa fa-window-restore"/> Adjuntar
                </button>
            </div>
            <div class="form-group col-md-3">
                <span  class="badge badge-primary mr-1 mb-1" style="font-size: 100%" v-for="(referency, index) in origin_data.attaching">
                    <i class="fa fa-remove" @click="$emit('remove', index)"/> {{ referency }}
                </span>
            </div>
        </div>
        <div v-if=" origin_data.ArrayReferences">
            <div class="row">
                <label class="col-md-2">Asunto:</label>
                <div class="col-md-10">
                    <span style="font-size: small" v-text="origin_data.ArrayReferences.affair"/>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2">Unidad Orgánica:</label>
                <div class="col-md-10">
                    <span style="font-size: x-small">
                        {{ origin_data.ArrayReferences.entity }} | {{ origin_data.ArrayReferences.description }} | {{ origin_data.ArrayReferences.fullName }}
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2 mb-0">Fecha:</label>
                <div class="col-md-4">
                    <span style="font-size: small" v-text="origin_data.ArrayReferences.shipping_date"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
    export default {
        components: {
            VueBootstrapTypeahead
        },
        props: {
            origin_data: {
                type: Object,
                required: true
            },
        }
    }
</script>
<style scoped>
    .badge {
        display: inline-block;
        padding: .25em .4em;
    }
</style>


