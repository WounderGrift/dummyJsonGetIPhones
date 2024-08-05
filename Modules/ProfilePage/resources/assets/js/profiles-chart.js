import {alert} from "../../../../../public/js/helpers/alert.js";

$(document).ready(function () {
    loadTemplate();
    filterTable();

    $('.get-data-profiles-chart').on('click', function(event) {
        event.preventDefault()
        getDataProfilesChart($(this).text());
    });

    if ($('#chartContainer').length)
        getDataProfilesChart();
});

function loadTemplate()
{
    let options = {
        exportEnabled: true,
        animationEnabled: true,
        title: {
            text: "Динамика пользователей"
        },
        axisY: {
            title: "Зарегистрировано",
            titleFontColor: "#4F81BC",
            lineColor: "#4F81BC",
            labelFontColor: "#4F81BC",
            tickColor: "#4F81BC"
        },
        toolTip: {
            shared: true
        },
        legend: {
            cursor: "pointer",
            itemclick: toggleDataSeries
        }
    };

    $("#chartContainer").CanvasJSChart(options);
    $('.canvasjs-chart-credit').hide();

}

function filterTable() {
    $('#filter').on('submit', function (event) {
        event.preventDefault();
        let searchValue = $('#filter input').val().trim();
        window.location.href = `/chart/profiles/${searchValue}`;
    });
}

let isChooseSubmitting = false;
function getDataProfilesChart(startDate = '1МЕС') {
    if (isChooseSubmitting)
        return;
    isChooseSubmitting = true;

    $.ajax({
        url: '/chart/profiles/range',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            startDate: startDate
        },
        success: function (response) {
            if (response.data) {
                response.data.forEach(function(item) {
                    item.x = new Date(item.x + "T00:00:00");
                });

                if(response.data.length) {
                    $('#chartContainer').show();
                    $('.corner-box-6').hide();
                    multipleAxes(response.data);
                } else {
                    $('#chartContainer').hide();
                    $('.corner-box-6').show();
                }

                isChooseSubmitting = false;
            } else {
                $('#chartContainer').hide();
                $('.corner-box-6').show();
                isChooseSubmitting = false;
            }
        },
        error: function (error) {
            if (error && error.responseJSON && error.responseJSON.message)
                new alert().errorWindowShow($('.error_profiles'), error.responseJSON.message);
            $('#main-loader').removeClass('show');
            isChooseSubmitting = false;
        }
    });
}

function multipleAxes(dataChart) {
    let options = {
        exportEnabled: true,
        animationEnabled: true,
        title: {
            text: "Динамика пользователей"
        },
        axisY: {
            title: "Зарегистрировано",
            titleFontColor: "#4F81BC",
            lineColor: "#4F81BC",
            labelFontColor: "#4F81BC",
            tickColor: "#4F81BC"
        },
        toolTip: {
            shared: true
        },
        legend: {
            cursor: "pointer",
            itemclick: toggleDataSeries
        },
        data: [{
            type: "spline",
            name: "Пользователи",
            showInLegend: true,
            xValueFormatString: "MMM DD YYYY",
            yValueFormatString: "#,##0",
            dataPoints: dataChart
        }]
    };

    $("#chartContainer").CanvasJSChart(options);
    $('.canvasjs-chart-credit').hide();
}

function toggleDataSeries(e) {
    e.dataSeries.visible = !(typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible);
    e.chart.render();
}
