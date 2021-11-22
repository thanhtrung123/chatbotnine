var chartDashboard = function () {
    var periodChart = new Chart(document.getElementById('periodChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data_date,
            datasets: [
            {
                label: '利用者数',
                data: data_result,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        //max: 500,
                        min: 0,
                        //stepSize: 5
                    },
                }],
            },
        }
    });
    var periodChart1 = new Chart(document.getElementById('periodChart1').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data_date,
            datasets: [
            {
                label: '利用者数',
                data: data_result,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        fontColor : "#000",
                        fontSize : 14,
                        //max: 500,
                        min: 0,
                        // stepSize: 1,
                        // beginAtZero: true
                    },
                    // gridLines:{
                    // 	color: "#000",
                    // 	lineWidth:3,
                    // 	zeroLineColor :"#000",
                    // 	zeroLineWidth : 2
                    // },
                    stacked: true
                }],
                xAxes: [{
                    ticks:{
                    fontColor : "#000",
                    beginAtZero: true,
                    fontSize : 14
                    },
                    // gridLines:{
                    // 	color: "#fff",
                    // 	lineWidth:2
                    // }
                }],
                responsive:false
            },
        },
        plugins: [{
            beforeDraw: function(chartInstance, easing) {
                var ctx = chartInstance.chart.ctx;
                ctx.fillStyle = '#FFFFFF'; // your color here
    
                var chartArea = chartInstance.chartArea;
                ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            }
        }]
    });
    var timeChart = new Chart(document.getElementById('timeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: hour,
            datasets: [
            {
                label: '利用者数',
                data: data_hour,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk_hour,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        // max: 40,
                        min: 0,
                        // stepSize: 5
                    },
                }],
            },
        }
    });
    var timeChart1 = new Chart(document.getElementById('timeChart1').getContext('2d'), {
        type: 'bar',
        data: {
            labels: hour,
            datasets: [
            {
                label: '利用者数',
                data: data_hour,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk_hour,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        fontColor : "#000",
                        fontSize : 14,
                        //max: 500,
                        min: 0,
                        // stepSize: 1,
                        // beginAtZero: true
                    },
                    // gridLines:{
                    // 	color: "#000",
                    // 	lineWidth:3,
                    // 	zeroLineColor :"#000",
                    // 	zeroLineWidth : 2
                    // },
                    stacked: true
                }],
                xAxes: [{
                    ticks:{
                    fontColor : "#000",
                    beginAtZero: true,
                    fontSize : 14
                    },
                    // gridLines:{
                    // 	color: "#fff",
                    // 	lineWidth:2
                    // }
                }],
                responsive:false
            },
        },
        plugins: [{
            beforeDraw: function(chartInstance, easing) {
                var ctx = chartInstance.chart.ctx;
                ctx.fillStyle = '#FFFFFF'; // your color here
    
                var chartArea = chartInstance.chartArea;
                ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            }
        }]
    });
    var weekChart = new Chart(document.getElementById('weekChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: day_of_week,
            datasets: [
            {
                label: '利用者数',
                data: data_day_of_week,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk_day_of_week,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                       // max: 100,
                        min: 0,
                        // stepSize: 5
                    },
                }],
            },
        }
    });
    var weekChart1 = new Chart(document.getElementById('weekChart1').getContext('2d'), {
        type: 'bar',
        data: {
            labels: day_of_week,
            datasets: [
            {
                label: '利用者数',
                data: data_day_of_week,
                borderColor: "rgb(100, 120, 230)",
                backgroundColor: "rgba(100, 120, 230, 0.2)",
                yAxisID: "y-axis-1",
            },
            {
                label: 'トーク数',
                type: "line",
                fill: false,
                data: data_talk_day_of_week,
                borderColor: "rgb(230, 120, 100)",
                yAxisID: "y-axis-1",
            }
            ]
        },
        options: {
            tooltips: {
                mode: 'nearest',
                intersect: false,
            },
            responsive: true,
            scales: {
                yAxes: [{
                    id: "y-axis-1",
                    type: "linear",
                    position: "left",
                    ticks: {
                        fontColor : "#000",
                        fontSize : 14,
                        //max: 500,
                        min: 0,
                        // stepSize: 1,
                        // beginAtZero: true
                    },
                    // gridLines:{
                    // 	color: "#000",
                    // 	lineWidth:3,
                    // 	zeroLineColor :"#000",
                    // 	zeroLineWidth : 2
                    // },
                    stacked: true
                }],
                xAxes: [{
                    ticks:{
                    fontColor : "#000",
                    beginAtZero: true,
                    fontSize : 14
                    },
                    // gridLines:{
                    // 	color: "#fff",
                    // 	lineWidth:2
                    // }
                }],
                responsive:false
            },
        },
        plugins: [{
            beforeDraw: function(chartInstance, easing) {
                var ctx = chartInstance.chart.ctx;
                ctx.fillStyle = '#FFFFFF'; // your color here
    
                var chartArea = chartInstance.chartArea;
                ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            }
        }]
    });
    if (flag_answer == 1) {
        var responseRateChart = new Chart(document.getElementById('responseRateChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['回答できた', '回答できなかった'],
                datasets: [{
                    backgroundColor: [
                        '#4169e1',
                        '#ff6347',
                    ],
                    data: data_answer
                }]
            },
            options: {
                title: {
                    display: false,
                    text: '回答率'
                },
                maintainAspectRatio: false,
            },
            plugins: [{
                afterDatasetsDraw: function(chart, easing) {
                    var ctx = chart.ctx;
                    // タイトルの取得
                    var value = chart.config.options.title.text;
                    // フォントの指定
                    ctx.fillStyle = 'rgb(99, 107, 111)';
                    var fontSize = 24;
                    var fontStyle = 'normal';
                    var fontFamily = 'Helvetica Neue';
                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                    // 描画位置の指定
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var position = {
                        x: chart.width / 2,
                        y: chart.height / 2,
                    };
                    // 描画
                    ctx.fillText(value, position.x, position.y + (fontSize / 2));
                    // ラベルの描画
                    chart.data.datasets.forEach(function (dataset, i) {
                        var dataSum = 0;
                        dataset.data.forEach(function (element){
                            dataSum += element;
                        });
                        var meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach(function (element, index) {
                                // フォントの指定
                                ctx.fillStyle = 'rgb(255, 255, 255)';
                                var fontSize = 12;
                                var fontStyle = 'normal';
                                var fontFamily = 'Helvetica Neue';
                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                                // パーセンテージの計算
                                var dataString = (Math.round(dataset.data[index] / dataSum * 1000)/10).toString() + "%";
                                // 描画位置の指定
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                var padding = 5;
                                var position = element.tooltipPosition();
                                // 描画
                                ctx.fillText(dataString, position.x, position.y + (fontSize / 2) - padding);
                            });
                        }
                    });
                }
            }],
        });
    }
    if (flag_no_answer == 1) {
        var resolutionRateChart = new Chart(document.getElementById('resolutionRateChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['解決した', '解決しなかった', '未回答'],
                datasets: [{
                    backgroundColor: [
                        '#4169e1',
                        '#ff6347',
                        '#a9a9a9',
                    ],
                    data: data_handle
                }]
            },
            options: {
                title: {
                    display: false,
                    text: '解決率'
                },
                maintainAspectRatio: false,
            },
            plugins: [{
                afterDatasetsDraw: function(chart, easing) {
                    var ctx = chart.ctx;
                    // タイトルの取得
                    var value = chart.config.options.title.text;
                    // フォントの指定
                    ctx.fillStyle = 'rgb(99, 107, 111)';
                    var fontSize = 24;
                    var fontStyle = 'normal';
                    var fontFamily = 'Helvetica Neue';
                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                    // 描画位置の指定
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var position = {
                        x: chart.width / 2,
                        y: chart.height / 2,
                    };
                    // 描画
                    ctx.fillText(value, position.x, position.y + (fontSize / 2));
                    // ラベルの描画
                    chart.data.datasets.forEach(function (dataset, i) {
                        var dataSum = 0;
                        dataset.data.forEach(function (element){
                            dataSum += element;
                        });
                        var meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach(function (element, index) {
                                // フォントの指定
                                ctx.fillStyle = 'rgb(255, 255, 255)';
                                var fontSize = 12;
                                var fontStyle = 'normal';
                                var fontFamily = 'Helvetica Neue';
                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                                // パーセンテージの計算
                                var dataString = (Math.round(dataset.data[index] / dataSum * 1000)/10).toString() + "%";
                                // 描画位置の指定
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                var padding = 5;
                                var position = element.tooltipPosition();
                                // 描画
                                ctx.fillText(dataString, position.x, position.y + (fontSize / 2) - padding);
                            });
                        }
                    });
                }
            }],
        });
    }
    if (flag_enquete == 1) {
        var num = 0;
        Object.keys(enquete_combine).forEach(function(key) {
            var num = parseInt(key) + 1;
            // var itemData = Object.values(enqueteItem[key].itemData);
            // var itemValue = Object.values(enqueteItem[key].itemValue);
            var itemData =  Object.keys(enquete_combine[key].item_data).map(function(e) {
                                return enquete_combine[key].item_data[e];
                            });
            var itemValue =  Object.keys(enquete_combine[key].item_value).map(function(e) {
                                return enquete_combine[key].item_value[e];
                            });
            var itemBackground =  Object.keys(enquete_combine[key].background_color).map(function(e) {
                return enquete_combine[key].background_color[e];
            });
            let myChart =   " enquete" + num + "Chart";
            
            myChart =  new Chart(document.getElementById('enquete'+num+'Chart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: itemData,
                    datasets: [{
                        backgroundColor: itemBackground,
                        data: itemValue,
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: false,
                        text: enquete_combine[key].question_name
                    },
                    maintainAspectRatio: false,
                    tooltips: {
                        enabled: false,
                        callbacks: {
                            label: function (tooltipItem, data){
                                totalValue = data.datasets[0].data.reduce(function(a, x){return a + x;});
                                return data.labels[tooltipItem.index] + ': ' + (Math.round(data.datasets[0].data[tooltipItem.index] / totalValue * 1000) / 10).toFixed(1) + ' % (' + data.datasets[0].data[tooltipItem.index] + ')';
                            }
                        },
                        custom: function(tooltipModel) {
                            // Tooltip Element
                            var tooltipEl = $('#enquete'+num+'tooltip');

                            // Create element on first render
                            if (!tooltipEl.length) {
                                tooltipEl = $('<div id="enquete'+num+'tooltip" class="tooltip_chart"><table></table><div class="caret_html"></div></div>');
                                $('body').append(tooltipEl);
                            }

                            // Hide if no tooltip
                            if (tooltipModel.opacity === 0) {
                                tooltipEl.css({
                                    opacity: 0
                                }); 
                                return;
                            }

                            // Set caret Position
                            tooltipEl.removeClass(['top', 'center', 'bottom','left','right']);
                            if (tooltipModel.yAlign) {
                                tooltipEl.addClass(tooltipModel.yAlign);
                            }
                            if (tooltipModel.xAlign) {
                                tooltipEl.addClass(tooltipModel.xAlign);
                            }
                            function getBody(bodyItem) {
                                return bodyItem.lines;
                            }

                            // Set Text
                            if (tooltipModel.body) {
                                var titleLines = tooltipModel.title || [];
                                var bodyLines = tooltipModel.body.map(getBody);

                                var innerHtml = '<thead>';

                                titleLines.forEach(function(title) {
                                    innerHtml += '<tr><th>' + title + '</th></tr>';
                                });
                                innerHtml += '</thead><tbody>';

                                bodyLines.forEach(function(body, i) {
                                    var colors = tooltipModel.labelColors[i];
                                    var style = 'background:' + colors.backgroundColor;
                                    style += '; border-color:' + colors.borderColor;
                                    style += ';border-width: 1px;border-style: solid;display: inline-block;width: 12px;height: 12px;margin-right: 7px;vertical-align: text-top; position: relative; top: 1px;';
                                    var span = '<span style="' + style + '"></span>';
                                    innerHtml += '<tr><td style="color: #ffffff;background-color: #000000;background-color: rgba(0,0,0,0.8);border-radius: 7px; padding: 3px 6px 2px;">' + span + body + '</td></tr>';
                                });
                                innerHtml += '</tbody>';

                                var tableRoot = tooltipEl.find('table');
                                tableRoot.html(innerHtml);
                            }
                            var caret = tooltipEl.find('.caret_html');
                            caret.css({
                                position: 'absolute',
                                left: tooltipModel.caretX - tooltipModel.x,
                                top: tooltipModel.caretY - tooltipModel.y
                            });
                            if (tooltipModel.caretX > tooltipModel.x){
                                
                            }

                            // `this` will be the overall tooltip
                            var position = this._chart.canvas.getBoundingClientRect();

                            // Display, position, and set styles for font
                            tooltipEl.css({
                                opacity: 1,
                                position:'absolute',
                                left: position.left + window.pageXOffset + tooltipModel.x,
                                top: position.top + window.pageYOffset + tooltipModel.y,
                                fontFamily: tooltipModel._bodyFontFamily,
                                fontSize: tooltipModel.bodyFontSize + 'px',
                                fontStyle: tooltipModel._bodyFontStyle,
                                pointerEvents: 'none'
                            });
                        }
                    },
                    legend: {
                        display: false,
                    },
                    legendCallback: function(chart){
                        var ul = document.createElement('ul');
                        $(ul).addClass(chart.id + '-legend');
                        var canvas = document.createElement('canvas'),
                                ctx = canvas.getContext("2d");
                        canvas.width = 10;
                        canvas.height = 10;
                        chart.legend.legendItems.forEach(function(legend) {
                            ctx.moveTo(0,0);
                            ctx.fillStyle = legend.fillStyle;
                            ctx.fillRect(0,0,10,10);
                            $(ul).append('<li><span style="background-color: '+ legend.fillStyle +'"><img style="vertical-align: top;" src="' + canvas.toDataURL() + '" width="10" height="10" /></span>' + legend.text + '</li>');
                        });
                        return ul.outerHTML;
                    }
                },
            });
            let myLegendContainer = document.getElementById('js-legend'+num);
            myLegendContainer.innerHTML = myChart.generateLegend();
            var legendItems = myLegendContainer.getElementsByTagName('li');
            for (var i = 0; i < legendItems.length; i += 1) {
                legendItems[i].addEventListener("click", legendClickCallback, false);
            }
            
        });
    }
};

var legendClickCallback = function(event) {
    event = event || window.event;
    var target = event.target || event.srcElement;
    while (target.nodeName !== 'LI') {
        target = target.parentElement;
    }
    var parent = target.parentElement;
    var chartId = parseInt(parent.classList[0].split("-")[0], 10);
    var chart = Chart.instances[chartId];
    var index = Array.prototype.slice.call(parent.children).indexOf(target);
    var meta = chart.getDatasetMeta(0);
    var item = meta.data[index];

    if (item.hidden === null || item.hidden === false) {
        item.hidden = true;
        target.classList.add('strike');
    } else {
        target.classList.remove('strike');
        item.hidden = null;
    }
    chart.update();
}

var downloadImage = function(upload_url, formData) {
    $.ajax({
        type: 'POST',
        url: upload_url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $("#dashboard").prop('disabled', false);
            if (data.succsess == 'succsess') {
                $("#form_dashboard").attr('action', url_export).submit();
            } else {
                alert('ファイルのエクスポートエラー');
            }
        },
        error: function (xhr, textStatus) {
            $("#dashboard").prop('disabled', false);
            alert('ファイルのエクスポートエラー');
        }
    });
}

$(function () {
    chartDashboard();
    $('.surveyResults').hide();
    /**
     * Export dashboard
     */
    function wrapText(context, text, x, y, maxWidth, lineHeight,strike) {
        var line = '';
        var testWidth;

        for(var n = 0; n < text.length; n++) {
            var testLine = line + text[n];
            var metrics = context.measureText(testLine);
            testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                if (strike){
                    context.save();
                    context.beginPath();
                    context.moveTo(x,y - 3 + lineHeight / 2);
                    context.lineTo(x + testWidth,y - 3 + lineHeight / 2);
                    context.stroke();
                    context.restore();
                }
                line = text[n];
                y += lineHeight;
            }
            else {
                line = testLine;
            }
        }
        context.fillText(line, x, y);
        if (strike){
            context.save();
            context.beginPath();
            context.moveTo(x,y - 3 + lineHeight / 2);
            context.lineTo(x + testWidth,y - 3 + lineHeight / 2);
            context.stroke();
            context.restore();
        }
    }
    if( !HTMLCanvasElement.prototype.toBlob ) {
        Object.defineProperty( HTMLCanvasElement.prototype, 'toBlob',
        { 
            value: function( callback, type, quality )
            {
                var bin = atob( this.toDataURL( type, quality ).split(',')[1] ),
                len = bin.length,
                len32 = len >> 2,
                a8 = new Uint8Array( len ),
                a32 = new Uint32Array( a8.buffer, 0, len32 );

                for( var i=0, j=0; i < len32; i++ )
                {
                    a32[i] = bin.charCodeAt(j++) |
                        bin.charCodeAt(j++) << 8 |
                        bin.charCodeAt(j++) << 16 |
                        bin.charCodeAt(j++) << 24;
                }

                var tailLength = len & 3;

                while( tailLength-- )
                {
                    a8[ j ] = bin.charCodeAt(j++);
                }

                callback( new Blob( [a8], {'type': type || 'image/png'} ) );
            }
        });
    }
    $(document).ready(function() {
        $(document).on("click", "#dashboard", function (e) {
            e.preventDefault();
            if (flag_answer == 1) {
                var formData = new FormData();
                var blobAry = [];
                $(this).prop('disabled', true);
                $("#periodChart1").get(0).toBlob(function(blob) {
                    formData.append('fileName[]','chart_period.png');
                    formData.append('dataImage[]', blob);
                    $("#timeChart1").get(0).toBlob(function(blob1) {
                        formData.append('fileName[]','chart_hour.png');
                        formData.append('dataImage[]', blob1);
                        $("#weekChart1").get(0).toBlob(function(blob2) {
                            formData.append('fileName[]','chart_week.png');
                            formData.append('dataImage[]', blob2);
                            $("#responseRateChart").get(0).toBlob(function(blob3) {
                                formData.append('fileName[]','chart_answer.png');
                                formData.append('dataImage[]', blob3);
                                if (flag_no_answer == 1) {
                                    $("#resolutionRateChart").get(0).toBlob(function(blob4) {
                                        formData.append('fileName[]','chart_resolve.png');
                                        formData.append('dataImage[]', blob4);
                                        if (flag_enquete == 1) {
                                            Object.keys(enquete_combine).forEach(function(key) {
                                                var num = parseInt(key) + 1;
                                                /* html2canvas(document.getElementById("enquete"+num+"ChartWrapper")).then(function(canvas){
                                                    canvas.toBlob(function(blob){
                                                        formData.append('fileName[]','chartEnquete'+num+'.png');
                                                        formData.append('dataImage[]', blob);
                                                    })
                                                }); */
                                                // var num  = 1;
                                                var canvas = $("#enquete"+num+"Chart"),
                                                parent = $("#enquete"+num+"ChartWrapper"),
                                                print_canvas = document.createElement('CANVAS');
                                                print_canvas.width = parent.width();
                                                print_canvas.height = parent.height();
                                                var ctx = print_canvas.getContext('2d');
                                                ctx.clearRect(0,0,print_canvas.width,print_canvas.height);
                                                if (/MSIE|Trident/.test(window.navigator.userAgent)){
                                                    var img  = document.createElement('IMG');
                                                    img.src = canvas[0].toDataURL();
                                                    ctx.drawImage(img,0,0,canvas[0].width,canvas[0].height,canvas.position().left,canvas.position().top,canvas.width(),canvas.height());
                                                }else{
                                                    ctx.drawImage(canvas[0],0,0,canvas.attr('width'),canvas.attr('height'),canvas.position().left,canvas.position().top,canvas.width(),canvas.height());
                                                }
                                                parent.find('li').each(function(){
                                                    var computed_style = window.getComputedStyle($(this)[0])
                                                    ctx.font = 'normal normal normal 14px/22px Meiryo';
                                                    ctx.textBaseline = 'top';
                                                    ctx.fillStyle = computed_style.color;
                                                    ctx.strokeStyle = computed_style.color;
                                                    wrapText(
                                                        ctx,
                                                        $(this).text(),
                                                        $(this).position().left + Math.round(parseFloat(computed_style.paddingLeft)),
                                                        $(this).position().top + Math.round(parseFloat(computed_style.paddingTop)) + 3,
                                                        $(this).width(),
                                                        Math.round(parseFloat(computed_style.lineHeight)),
                                                        $(this).hasClass('strike')
                                                    );
                                                    //Draw circle
                                                    var circle_style = window.getComputedStyle($(this).find('span')[0]);
                                                    ctx.fillStyle = circle_style.backgroundColor;
                                                    ctx.beginPath();
                                                    ctx.arc(
                                                        $(this).position().left + Math.round(parseFloat(circle_style.left)),
                                                        $(this).position().top + Math.round(parseFloat(circle_style.top)) + Math.round(parseFloat(circle_style.width) / 2),
                                                        Math.round(parseFloat(circle_style.width) / 2),
                                                        0, 2 * Math.PI
                                                    );
                                                    ctx.fill();
                                                });
                                                print_canvas.toBlob(function(blob5){
                                                    formData.append('fileName[]','chart_enquete'+num+'.png');
                                                    formData.append('dataImage[]', blob5);
                                                    if (enquete_combine.length == num) {
                                                        downloadImage(upload_url, formData);
                                                    }
                                                });
                                                //parent.after(print_canvas);
                                            });
                                        } else {
                                            downloadImage(upload_url, formData);
                                        }
                                    });
                                } else {
                                    downloadImage(upload_url, formData);
                                }
                            });
                        });
                    });
                });
            } else {
                alert('利用可能なエクスポートデータはありません');
            }
        });

        $(document).on("click", "#search_dashboard", function (e) {
            e.preventDefault();
            $("#form_dashboard").attr('action', url_search).submit();
        });
    });
});