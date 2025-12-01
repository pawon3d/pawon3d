@props([
    'topData' => ['labels' => [], 'data' => []],
    'pieData' => ['labels' => [], 'data' => []],
])

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
@endassets

<script>
    (function() {
        let topProductionsChart = null;
        let paymentChart = null;

        function initTopProductsChart(labels, data) {
            try {
                if (topProductionsChart && topProductionsChart.destroy) topProductionsChart.destroy();
            } catch (e) {
                // ignore
            }

            const canvas = document.getElementById('topProductionsChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            topProductionsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Produksi (Pcs)',
                        data: data,
                        backgroundColor: '#74512D',
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function initPieChart(canvasId, labels, data, chartRef) {
            try {
                if (chartRef && chartRef.destroy) chartRef.destroy();
            } catch (e) {
                // ignore
            }

            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            const ctx = canvas.getContext('2d');

            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#FFB100', '#74512D', '#FF6969', '#82A0D8', '#B0D9B1',
                            '#E1AFD1', '#E5E483'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        window.addEventListener('update-charts', event => {
            const payload = event.detail && event.detail[0] ? event.detail[0] : {};
            const top = payload.topProductionsChartData || {};
            const pie = payload.productionChartData || {};

            if (top && top.labels && top.data) {
                initTopProductsChart(top.labels, top.data);
            }
            if (pie && pie.labels && pie.data) {
                paymentChart = initPieChart('productionMethodChart', pie.labels, pie.data, paymentChart);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            initTopProductsChart(@json($topData['labels']), @json($topData['data']));
            paymentChart = initPieChart('productionMethodChart', @json($pieData['labels']),
                @json($pieData['data']), null);
        });
    })();
</script>
