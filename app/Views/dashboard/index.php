<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    
    <!-- Include Cards Earning -->
    <?= $this->include('dashboard/_cards_earning') ?>

    <!-- Content Row -->
    <div class="row">
    <!-- Include Graph Area Chart and Pie Chart -->
    <?= $this->include('dashboard/_graph_pie_chart') ?>

    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format_rupiah(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return 'Rp ' + s.join(dec);
}

var myAreaChart = null;
var myPieChart = null;

function fetchChartData(year) {
    $.ajax({
        url: '<?= base_url('dashboard/chart_data') ?>',
        type: 'GET',
        data: { year: year },
        success: function(response) {
            updateAreaChart(response.area);
            updatePieChart(response.pie);
            
            // Update Cards
            $('#card-income-label').text('Total Pemasukan (' + response.year + ')');
            $('#card-income-value').text(number_format_rupiah(response.cards.income));
            
            $('#card-expense-label').text('Total Pengeluaran (' + response.year + ')');
            $('#card-expense-value').text(number_format_rupiah(response.cards.expense));
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function updateAreaChart(data) {
    var ctx = document.getElementById("myAreaChart");
    
    if(myAreaChart !== null){
        myAreaChart.destroy();
    }

    myAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Pendapatan",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: data, 
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        // Include a dollar sign in the ticks
                        callback: function(value, index, values) {
                            return number_format_rupiah(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + number_format_rupiah(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
}

function updatePieChart(data) {
    var ctx = document.getElementById("myPieChart");
    
    if(myPieChart !== null){
        myPieChart.destroy();
    }

    myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Shopee", "Tiktok", "Zefatex"],
            datasets: [{
                data: [data.shopee, data.tiktok, data.zefatex],
                backgroundColor: ['#fd7e14', '#000000', '#4e73df'],
                hoverBackgroundColor: ['#e36e0d', '#2c2c2c', '#2e59d9'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var index = tooltipItem.index;
                        var currentValue = dataset.data[index];
                        var label = data.labels[index];
                        return label + ': ' + number_format_rupiah(currentValue);
                    }
                }
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
}

$(document).ready(function() {
    // Load default year (current year)
    fetchChartData(new Date().getFullYear());

    // Handle year selection
    $('.chart-year-select').click(function(e) {
        e.preventDefault();
        var year = $(this).data('year');
        fetchChartData(year);
    });
});
</script>
<?= $this->endSection() ?>