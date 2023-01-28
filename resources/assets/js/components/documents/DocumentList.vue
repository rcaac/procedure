<template>
    <tr>
        <td v-text="pending.records"></td>
        <td>
            {{ pending.registry }}<br>
            <small><b>Referencia:</b> {{ pending.reference }}</small>
        </td>
        <td>
            <div v-if="pending.Dependency === 'USUARIO EXTERNO'">
                <small><b>Usuario:</b> {{ pending.fullName }}<br></small>
                <small><b>Dni:</b> {{ pending.dni }}</small>
            </div>
            <div v-else>
                <small>{{ pending.Dependency }}<br></small>
                <small><b>Entidad:</b> {{ pending.description }}</small>
            </div>
        </td>
        <td><small>{{ pending.code }}</small></td>
        <td v-text="pending.affair"></td>
        <td><small>{{ pending.provided }}</small></td>
        <td v-text="pending.folio"></td>
        <td><small>{{ pending.priority }}</small></td>
        <td v-text="pending.shipping_date"></td>
        <td v-if="pending.state ===1"><span  class="badge badge-pill badge-success">Pendiente</span></td>
        <td style="width: 100px;">
            <div v-if="viewPending">
                <div v-if="pending.tupa === 1">
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm"
                        @click="$emit('openModalRequirement', pending.records)"
                    >
                        <i class="fa fa-eye"></i> Requisitos
                    </button>
                </div>
                <div v-if="pending.annexes === '1'">
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm"
                        @click="$emit('modalShowAnnexe', pending.records)"
                    >
                        <i class="fa fa-eye"></i> Anexos
                    </button>
                </div>
                <div v-if="current > 20">
                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled @click="$emit('receive', pending.documentary_procedure_id)">
                        <i class="icon-check"></i> Recepcionar
                    </button>
                </div>
                <div v-if="current <= 20 && total < 20">
                  <button type="button" class="btn btn-outline-warning btn-sm" @click="$emit('receive', pending.documentary_procedure_id)">
                    <i class="icon-check"></i> Recepcionar
                  </button>
                </div>
                <div v-if="current <= 20 && total > 20">
                  <button type="button" class="btn btn-danger btn-sm" @click="$emit('receive', pending.documentary_procedure_id)">
                    <i class="icon-check"></i> Recepcionar
                  </button>
                </div>
            </div>
            <div v-if="viewProcess">
                <div v-if="pending.tupa === 1">
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm"
                        @click="$emit('openModalRequirement', pending.records)"
                    >
                        <i class="fa fa-eye"></i> Requisitos
                    </button>
                </div>
                <div v-if="pending.annexes === '1'">
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm"
                        @click="$emit('showModalAnnexes', pending.records)"
                    >
                        <i class="fa fa-eye"></i> Anexos
                    </button>
                </div>
                <button type="button" class="btn btn-outline-warning btn-sm" @click="$emit('openModal', 'pending', 'derive', pending)">
                    <i class="fa fa-paper-plane-o"></i> Derivar
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="$emit('archiveDocument',pending.documentary_procedure_id)">
                    <i class="fa fa-archive"></i> Archivar
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" @click="$emit('archiveReturn',pending.documentary_procedure_id)">
                    <i class="fa fa-reply"></i> Devolver
                </button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {
        props: ['pending', 'viewProcess', 'viewPending', 'current', 'total'],
      data () {
        return {
          cantidad: 0
        }
      },
      mounted() {
        this.verify();
      },
      methods : {
          verify(){
            console.log("CANTIDAD DE REGISTROS " + this.current);
          }
      }
    }
</script>
