document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Gráfico de Crecimiento (Line Chart)
    const optionsGrowth = {
        series: [{
            name: 'Usuarios',
            data: [245, 280, 310, 340, 385, 420]
        }, {
            name: 'Tests',
            data: [320, 380, 450, 520, 600, 680]
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#0F766E', '#0EA5E9'], // Teal y Blue
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        fill: { opacity: 0.1 },
        grid: { strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#chart-growth"), optionsGrowth).render();

    // 2. Gráfico de Estado (Donut/Pie Chart)
    const optionsStatus = {
        series: [68, 22, 10],
        labels: ['Completados', 'Pendientes', 'Vencidos'],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'inherit'
        },
        colors: ['#10B981', '#F59E0B', '#DC2626'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%'
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#chart-status"), optionsStatus).render();
    
    // 3. Gráfico de Categorías (Bar Chart)
    const optionsCategories = {
        series: [{
            name: 'Cantidad',
            data: [920, 1050, 880, 830]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#0F766E'], // Teal Principal
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '50%'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['Depresión', 'Ansiedad', 'Int. Emocional', 'Autoestima'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        grid: { strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#chart-categories"), optionsCategories).render();
});