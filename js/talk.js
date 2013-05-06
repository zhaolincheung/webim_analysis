function selectMajor()
{
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
      for(var i = 0; i< rData.length; i++)
      {
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
function clearChart()
{
  var chartDiv = $('#chart_div');
  chartDiv.empty();
}

function submit()
{
  var category = $("#category").val();
  var url = '../php/getTalkData.php?category=' + category;
  
  var major_category = $('#majorCategory').val();
  url += '&majorCategory=' + major_category;

  var dt = $('#dt').attr('value');  
  if(dt == '')
  {
    alert("日期不能为空");
    return;
  }
  else
  {
    url += '&dt=' + dt;
  }
  
  $.ajax({
  type:"get",
  url:url,
  dataType:"json",
  success:function(data){
    createTable(data["tableData"]); //创建表格
    drawChart(data["chartData"]);
  },
  error:function(XMLHttpRequest,textStatus,errorThrown){
    alert("请求超时，请稍后再试吧！");
  }
  });
}

function createTable(data)
{
  var total = 0;
  for(var k in data)
  {
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

function drawChart(data)
{
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
