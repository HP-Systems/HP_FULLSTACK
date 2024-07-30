import Chart from 'chart.js/auto';

// Servicios 
async function obtenerDatosServicios() {
    try {
        const response = await fetch('/api/web/dashboard/servicios');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }
}
async function GraficoServicios() {
    try{
    const datos = await obtenerDatosServicios();

    const labels = datos.map(item => item.nombre);
    const cantidad = datos.map(item => item.cantidad);

    const data = {
        labels: labels,
        datasets: [{
            label: 'Servicios más solicitados en el año',
            data: cantidad,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgb(118, 171, 174)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'radar',
        data: data,
        options: {
            responsive: true,
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const dataset = tooltipItem.dataset;
                            const currentValue = dataset.data[tooltipItem.dataIndex];
                            return `Cantidad: ${currentValue}`;
                        }
                    }
                }
            }
        }
    };

    new Chart(
        document.getElementById('myChart'),
        config
    );
} catch (error) {
    console.error('Error al obtener los datos');
    return [];
}
}



// Ventas Por Mes
async function DatosVentasMes() {
    try{
    const response = await fetch('/api/web/dashboard/ventasMes');
  
    const data = await response.json();

    return data;
    }
    catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }
}
async function GraficoVentasMes() {
    try{
    const datos = await DatosVentasMes();

    const labels = datos.map(item => item.mes);
    const totales = datos.map(item => item.total);
    const data = {
        labels: labels,
        datasets: [{
            label: 'Ingreso por Mes',
            data: totales,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill: false,
            backgroundColor: 'rgb(75, 192, 192)',
            borderColor: [
                'rgb(118, 171, 174)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
              ],
              borderWidth: 1
            }]

    };

    const config = {
        type: 'line',
        data: data,
      };
    new Chart(document.getElementById('myChart2'), config);
    } catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }

}

//Usuarios 

async function ObtenerDatosUsuarios() {
    try {
        const response = await fetch('/api/web/dashboard/usuariosMes');
        const data = await response.json();
      
        return data;
    } catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }
}

async function GraficoUsuarios() {
    try{
    const datos = await ObtenerDatosUsuarios();
    const labels = datos.map(item => item.mes);
    const totales = datos.map(item => item.total);
    const data = {
        labels: labels,
        datasets: [{
            label: 'Nuevo Usuarios por Mes' ,
            data: totales,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill: false,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)'
              ],
              borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
              ],
          
              borderWidth: 1
            }]

    };
    const config = {
        type:'bar',
        data: data,
    
    };
    new Chart(document.getElementById('myChart3'), config);
}
catch (error) {
    console.error('Error al obtener los datos');
    return [];
}
}
//Ingresos Por Tipo de Habitacion al Año 
async function DatosIngresosTipoHabitacion() {
    try{
    const response = await fetch('/api/web/dashboard/ingresosPorTipo');
  
    const data = await response.json();

    return data;
    }catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }
}
async function GraficoIngresosTipoHabitacion() {
    try{
const datos = await DatosIngresosTipoHabitacion();
const labels = datos.map(item => item.nombre);
const totales = datos.map(item => item.total);
const data = {
    labels: labels,
    datasets: [{
        label: 'Ingresos por Tipo de Habitacion en el Año',
        data: totales,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: false,
        backgroundColor: ['rgb(118, 171, 174)',
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'],
        borderColor: [
            'rgb(118, 171, 174)',
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 100)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 1
        }]

};
const config = {
    type:'pie',
    data: data,

};
new Chart(document.getElementById('myChart4'), config);
    }catch (error) {
        console.error('Error al obtener los datos');
        return [];
    }
}

// Llamar a la función para actualizar el gráfico
GraficoServicios();
GraficoVentasMes();
GraficoUsuarios();
GraficoIngresosTipoHabitacion();
