    //测试字符串是否为数字
    function isNumber(str){
        var reg = /^(-|\+)?\d+(\.)?\d*$/;
        return reg.test(str);
    }

    function revert(text){
        if(text == "analysisdate"){
            return "分析日期";
        }
        if(text == "c20"){
            return "20+";
        }
        if(text == "c10"){
            return "11-20";
        }
        if(text == "c6"){
            return "6-10";
        }
        if(text == "c5"){
            return "5";
        }
        if(text == "c4"){
            return "4";
        }
        if(text == "c3"){
            return "3";
        }
        if(text == "c2"){
            return "2";
        }
        if(text == "c1"){
            return "1";
        }

        return text;
    }

    //反选'复选框'
    function changeCol(button){
        $("input[name='col']:checkbox").each(function(){
            $(this).attr("checked", button.checked);
        })
    }

    function selectMajor(){
        var category = $("#category");
        var v_cat = category.val();
        var major_category = $("#majorCategory");
      
        major_category.empty();
        $("<option value='-1'>请选择</option>").appendTo(major_category);
      
        if(v_cat ==0 || v_cat == 1001 || v_cat == 1002)
            return;

        var url = "../php/getCategory.php?category=" + v_cat;
        //alert(url);
        $.ajax({
            type:"get",
            url:url,
            dataType:"json",
            success: function(rData){
                //alert('ajax suc');
                for(var i = 0; i< rData.length; i++){
                    var row = '<option value="' + rData[i].script_index + '">' + rData[i].name + '</option>';
                    $(row).appendTo(major_category);
                }
            },
            error:function(XMLHttpRequest,textStatus,errorThrown){
                alert('ajax err')
            }
        });
    }

    //清空图表
    function clearChart(){
        var chartDiv = $('#chart_div');
        chartDiv.empty();
    }

    //格式化图表数据中的日期
    function formatData(chartData){
        var data = [];
        for(var i = 0; i < chartData.length; i++){
            var row = {};
            for(var key in chartData[i]){
                if(key == 'analysisdate'){
                    var tempDate = chartData[i][key].replace(/-/g, "/");//将YYYY-MM-DD 改成YYYY/MM/DD的形式
                    row[key] = new Date(tempDate + " 00:00:00");
                }else{
                    row[key] = chartData[i][key];
                }
            }   
            data.push(row);
        } 
        return data;
    }

    function submit()
    {
        var category = $("#category").val();
        var url = '../php/getTalkPeriodData.php?category=' + category;

        var major_category = $('#majorCategory').val();
        url += '&majorCategory=' + major_category;

        var cols = '';
        var count = 0;

        $("input[name='col']:checkbox").each(function(){
            if($(this).attr("checked")){
                if(count != 0){
                    cols += ',';
                }
                cols += $(this).val();
                count++;
            }
        });

        if(cols == ''){
            alert("显示会话数不能为空");
            return;
        }

        url += '&cols=' + cols;
        var date_start = $('#date_start').attr('value');
        var date_end = $('#date_end').attr('value');
      
        if(date_start == '' || date_end == ''){
            alert("日期不能为空");
            return;
        }
        url += '&date_start=' + date_start + "&date_end=" + date_end;
      
        $.ajax({
            type:"get",
            url:url,
            dataType:"json",
            success:function(data){
                //alert("ajax suc");
                createTable(data["tableData"]); //创建表格
        
                var chartData = formatData(data["chartData"]);  //将图表数据中的日期格式化为日期对象Date类型
                drawChart(cols,chartData); //绘制图表
            },
            error:function(XMLHttpRequest,textStatus,errorThrown){
                alert("ajax err");
            }
        });
    }

    function createTable(data){ 
        var table = document.createElement("table");
        table.border = 1;
        table.cellspacing = 0;

        for(var i = 0; i < data.length; i++){
            var aRow = table.insertRow(i);
            for(j = 0; j < data[i].length; j++){
                var aCell = aRow.insertCell(j);
                var ele = data[i][j];
                if(isNumber(ele)){
                    ele += "%";
                }
                aCell.innerHTML = revert(ele);
            }
        }

        var div = document.getElementById("table_div");
        div.innerHTML = '';
        div.appendChild(table);
    }

    function drawChart(cols,data){
        //clear all graph  由于每次调用ajax后图形都不能清除，所以先清除所有图形
        clearChart();

        chart = new AmCharts.AmSerialChart();
        chart.pathToImages = "/amcharts/images/";
        chart.dataProvider = data;
        chart.categoryField = "analysisdate";
        chart.zoomOutButton = {
            backgroundColor: '#000000',
            backgroundAlpha: 0.15
        };

        // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
        chart.addListener("dataUpdated", zoomChart);

        //AXES  x轴
        var categoryAxis = chart.categoryAxis;
        categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
        categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
        categoryAxis.dashLength = 2;
        categoryAxis.gridAlpha = 0.15;
        categoryAxis.dateFormats = [{period:'DD',format:'MM-DD'},{period:'MM',format:'M月'},{period:'YYYY',format:'YYYY年'}];
        categoryAxis.axisColor = "#DADADA";


        //value y轴
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisAlpha = 0;
        valueAxis.inside = true;
        valueAxis.axisThickness = 0;
        valueAxis.unit = "%";
        chart.addValueAxis(valueAxis);

        var colArr = cols.split(",");    
        for( var col in colArr){
            var graph = new AmCharts.AmGraph();
            graph.valueAxis = valueAxis;
            graph.valueField = colArr[col];
            //graph.balloonText = "[[category]]:[[value]]";
            graph.balloonText = "[[value]]";
            graph.type = "smoothedLine";
            graph.title = revert(colArr[col]);
            graph.lineThickness = 2;
            graph.bullet = "round";

            chart.addGraph(graph);
        }

        //CURSOR 
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorPosition = "mouse";
        chartCursor.categoryBalloonDateFormat = "YYYY-MM-DD";
        chart.addChartCursor(chartCursor);

        //SCROLLBAR
        var chartScrollbar = new AmCharts.ChartScrollbar();
        chart.addChartScrollbar(chartScrollbar);

        var legend = new AmCharts.AmLegend();
        legend.marginLeft = 110;
        chart.addLegend(legend);

        chart.write("chart_div");

        //zoomChart是缩放表格
        // this method is called when chart is first inited as we listen for "dataUpdated" event
        function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            chart.zoomToIndexes(parseInt(data.length/3), data.length-1);
        }
    }
