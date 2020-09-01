<<template>
    <main class="main">
        <ol/>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-bar-chart"/> GRÁFICOS ESTADÍSTICOS
                </div>
                <div class="card-body">
                    <bar-chart-pending :ruta="ruta"/>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-bar-chart"/> GRÁFICOS ESTADÍSTICOS
                </div>
                <div class="card-body">
                    <bar-chart-attend :ruta="ruta"/>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    import BarChartPending from '../../components/charts/BarChartPending'
    import BarChartAttend from '../../components/charts/BarChartAttend'
    export default {
        components: {
            BarChartPending,
            BarChartAttend
        },
        props : ['ruta'],
    }
</script>
