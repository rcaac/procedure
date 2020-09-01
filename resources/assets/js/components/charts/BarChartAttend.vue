<script>
    import { Bar } from 'vue-chartjs'
    export default {
        extends: Bar,
        data () {
            return {
                datacollection: {
                    labels: [],
                    datasets: [
                        {
                            label: 'DOCUMENTOS POR ATENDER',
                            backgroundColor: '#f87979',
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            pointBorderColor: '#249EBF',
                            data: []
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                        xAxes: [ {
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontSize: 8,

                            }
                        }]
                    },
                    legend: {
                        display: true
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            }
        },
        props: ['ruta'],
        methods: {
            getDocumentState() {
                let me=this;
                var url= this.ruta + '/documentCharts';
                axios.get(url).then(function (response) {
                    let data = response.data.attend;
                    if(data) {
                        data.forEach(element => {
                            me.datacollection.labels.push(element.description);
                            me.datacollection.datasets[0].data.push(element.state);
                        });
                    }
                    me.renderChart(me.datacollection, me.options)
                })
                    .catch(function (error) {
                        console.log(error);
                    })
            }
        },
        mounted () {
            this.getDocumentState();
        }
    }

</script>

