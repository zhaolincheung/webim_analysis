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
	            alert('请求超时，请稍后再试吧！');
	        }
        });
    }

    //清空图表
    function clearChart(){
        var chartDiv = $('#chart_div');
        chartDiv.empty();
    }

    function submit(){
        var category = $("#category").val();
        var url = '../php/getTalkData.php?category=' + category;
        var urlMsgs = '../php/getMsgs.php?category=' + category;
        var urlPageNums = '../php/getMsgsPageNums.php?category=' + category;

        var major_category = $('#majorCategory').val();
        url += '&majorCategory=' + major_category;
        urlMsgs += '&majorCategory=' + major_category;
        urlPageNums += '&majorCategory=' + major_category;

        var dt = $('#dt').attr('value');  
        if(dt == ''){
	        alert("日期不能为空!");
	        return;
        }else{
	        url += '&dt=' + dt;
	        urlMsgs += '&dt=' + dt;
	        urlPageNums += '&dt=' + dt;
        }
     
        //消息数分段统计
        $.ajax({
            type:"get",
            url:url,
            dataType:"json",
            success:function(data){
	            createTable(data["tableData"]); //创建表格
	            drawChart(data["chartData"]);   //创建图表
            },
            error:function(XMLHttpRequest,textStatus,errorThrown){
	            alert("啊哦，消息数分段统计结果请求超时，请稍后再试吧！");
            }
        });
      
        //消息数统计 
        var pageNums=1;
	var pageSize = 30;
        $(function(){
            //获取总共的页数 
            $.ajax({
                async: false, //同步请求，只有获取到总共的消息页数才能往下执行
                type: "get",
                url: urlPageNums + "&pagesize=" + pageSize,
                dataType: "text",
                success:function(data){
                    pageNums = parseInt(data);
                    
                },
                error:function(XMLHttpRequest,textStatus,errorThrown){
	            alert("啊哦，总页面数请求超时，请稍后再试吧！");
                    return;
                }
            });
            //调用jPaginate插件，设置其属性
            $("#page").paginate({
                count: pageNums, //总页数
                start: 1,        //默认起始页
                display: pageSize,     //每页显示的行数
                border: false,
                border_color: '#BEF8B8',
                text_color: 'black',
                background_color: 'none',	
                border_hover_color: '#68BA64',
                text_hover_color: '#2573AF',
                background_hover_color: 'none', 
                images: false,
                mouse: 'press',
                onChange: function (pageId) {//回调函数
                        $.ajax({
                            type:"get",
                            url:urlMsgs + "&pageid=" + pageId + "&pagesize=" + pageSize,
                            dataType: "json",
                            success: function(data){
                                $('#msgtable_data').empty();//清空table中的内容
                                createMsgTable(data, pageId, pageSize);
                            },
                            error:function(XMLHttpRequest,textStatus,errorThrown){
	                        alert("啊哦，消息数统计结果请求超时，请稍后再试吧！");
                            }
                        });
                      }
            });
        });

        //默认加载第一页
        $.ajax({
            type:"get",
            url:urlMsgs + "&pageid=1&pagesize=" + pageSize,
            dataType:"json",
            success:function(data){
	            createMsgTable(data, 1, pageSize); //创建表格
            },
            error:function(XMLHttpRequest,textStatus,errorThrown){
	            alert("啊哦，消息数统计结果请求超时，请稍后再试吧！");
            }
        });
    }

    function createTable(data){
        var total = 0;
        for(var k in data){
	        total += parseInt(data[k]);
        } 
      
        var table_data = $('#table_data');
        table_data.html("");
        //$('<caption align="top">消息数分段统计</caption>').appendTo(table_data);
        $("<tr><td>20+</td><td>11-20</td><td>6-10</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td></tr>").appendTo(table_data);
      
        var html = "<tr><td>" + data.c20 + "</td><td>" + data.c10 + "</td><td>" + data.c6 + "</td><td>" + data.c5 + "</td><td>" + data.c4 + "</td><td>" + data.c3 + "</td><td>" + data.c2 + "</td><td>" + data.c1 + "</td></tr>";
      
        $(html).appendTo(table_data);
      
        html = "<tr><td>" + (data.c20*100/total).toFixed(2) + "%</td><td>" + (data.c10*100/total).toFixed(2) + "%</td><td>" + (data.c6*100/total).toFixed(2) + "%</td><td>" + (data.c5*100/total).toFixed(2) + "%</td><td>" + (data.c4*100/total).toFixed(2) + "%</td><td>" + (data.c3*100/total).toFixed(2) + "%</td><td>" + (data.c2*100/total).toFixed(2) + "%</td><td>" + (data.c1*100/total).toFixed(2) + "%</td></tr>";
      
        $(html).appendTo(table_data);
    }

    function drawChart(data){
        //clear all graph  由于每次调用ajax后图形都不能清除，所以先清除所有图形
        clearChart();

        chart = new AmCharts.AmPieChart();
        chart.dataProvider = data;
        chart.titleField = "talkratio";
        chart.valueField = "value";
        chart.outlineColor = "#FFFFFF";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 2;
      
        chart.depth3D = 15;
        chart.angle = 30;

        //LEGEND  
        legend = new AmCharts.AmLegend();
        legend.align = "center";
        legend.markerType = "circle";
        chart.addLegend(legend);
     
        chart.write("chart_div");
    }

    function createMsgTable(data, pageId, pageSize){
        var msgtable_data = $('#msgtable_data');
        msgtable_data.html("");
        //$('<caption align="top">消息数统计</caption>').appendTo(msgtable_data);
        $("<tr><td>id</td><td>talkid</td><td>fromuser</td><td>touser</td><td>数量</td></tr>").appendTo(msgtable_data);
       
	var startPage = (pageId - 1) * pageSize;
        for(var i = 0; i < data.length; i ++){
            var num = '<a target="_blank" href="../php/getMsgDetail.php?talkid=' + data[i].talkid + '&fromuserid=' + data[i].fromuserid + '&touserid=' + data[i].touserid + '&postid=' + data[i].postid + '">' + data[i].msgcount + '</a>';
            if(data[i].msgcount == 1) num = data[i].content;
            var row = '<tr><td>' + (startPage + i) + '</td><td>' + data[i].talkid + '</td><td>' + data[i].fromuserid + '</td><td>' + data[i].touserid + '</td><td>' + num + '</td></tr>';
            $(row).appendTo(msgtable_data);
        }
    }
