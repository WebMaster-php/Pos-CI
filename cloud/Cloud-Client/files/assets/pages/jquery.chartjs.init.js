/**
Template Name: Highdmin - Responsive Bootstrap 4 Admin Dashboard
Author: CoderThemes
Email: coderthemes@gmail.com
File: Chartjs
*/


!function($) {
    "use strict";

    var ChartJs = function() {};

    ChartJs.prototype.respChart = function(selector,type,data, options) {
        // get selector by context
        var ctx = selector.get(0).getContext("2d");
        // pointing parent container to make chart js inherit its width
        var container = $(selector).parent();

        // enable resizing matter
        $(window).resize( generateChart );

        // this function produce the responsive Chart JS
        function generateChart(){
            // make chart width fit with its container
            var ww = selector.attr('width', $(container).width() );
            switch(type){
                
                case 'Doughnut':
                    new Chart(ctx, {type: 'doughnut', data: data, options: options});
                    break;
                case 'Pie':
                    new Chart(ctx, {type: 'pie', data: data, options: options});
                    break;
                case 'Bar':
                    new Chart(ctx, {type: 'bar', data: data, options: options});
                    break;
                case 'Radar':
                    new Chart(ctx, {type: 'radar', data: data, options: options});
                    break;
                case 'PolarArea':
                    new Chart(ctx, {data: data, type: 'polarArea', options: options});
                    break;
            }
            // Initiate new chart or Redraw

        };
        // run function - render chart at first load
        generateChart();
    },

    //init
    ChartJs.prototype.init = function() {
        //creating lineChart
        var lineChart = {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October"],
            datasets: [{
                label: "Conversion Rate",
                fill: false,
                backgroundColor: '#4eb7eb',
                borderColor: '#4eb7eb',
                data: [44,60,-33,58,-4,57,-89,60,-33,58]
            }, {
                label: "Average Sale Value",
                fill: false,
                backgroundColor: '#e3eaef',
                borderColor: "#e3eaef",
                borderDash: [5, 5],
                data: [-68,41,86,-49,2,65,-64,86,-49,2]
            }]
        };

        var lineOpts = {
            responsive: true,
            // title:{
            //     display:true,
            //     text:'Chart.js Line Chart'
            // },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    // scaleLabel: {
                    //     display: true,
                    //     labelString: 'Month'
                    // },
                    gridLines: {
                        color: "rgba(0,0,0,0.1)"
                    }
                }],
                yAxes: [{
                    gridLines: {
                        color: "rgba(255,255,255,0.05)",
                        fontColor: '#fff'
                    },
                    ticks: {
                        max: 100,
                        min: -100,
                        stepSize: 20
                    }
                }]
            }
        };

        

      


        //Pie chart
        var pieChart = {
            labels: [
                "Steak Fajita Platter",
                "Marinated Chicken Breast Platter",
                "Single Chicken Kabob Platter",
                "Gyro Platter",
                "Chicken Fajita Platter"
            ],
            datasets: [
                {
                    data: [80, 50, 100,121,77],
                    backgroundColor: [
                        "#02c0ce",
                        "#4eb7eb",
                        "#e3eaef",
                        "#2d7bf4",
                        "#98a6ad"
                    ],
                    hoverBackgroundColor: [
                        "#02c0ce",
                        "#4eb7eb",
                        "#e3eaef",
                        "#2d7bf4",
                        "#98a6ad"
                    ],
                    hoverBorderColor: "#fff"
                }]
        };
        this.respChart($("#pie"),'Pie',pieChart);
    },
    $.ChartJs = new ChartJs, $.ChartJs.Constructor = ChartJs

}(window.jQuery),

//initializing
function($) {
    "use strict";
    $.ChartJs.init()
}(window.jQuery);

