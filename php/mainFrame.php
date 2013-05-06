<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <title>高性能组数据统计</title>
  <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
  <link href="/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
  <script type="text/javascript" src="/js/jquery-1.8.0.min.js"></script>
  <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
<!-- container -->
  <div>
  <!--Body content -->
    <form class="well form-search"  method="get" action="#">
      机房:
      <select class="input-medium" name="city_name" id="city_name_id">
        <option value="bj">北京</option>
      </select>
      来源:
      <select class="input-medium" name="source" id="source_id">
        <option value="1" selected="selected">WEB</option>
        <option value="2">WAP</option> 
      </select>
      起止日期:
      <div id="datetimepicker1" class="input-append date">
        <input class="input-medium" type="text" id="date_start_id" name="date_start">
        <span class="add-on">
          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
        </span>
      </div>-
      <div id="datetimepicker2" class="input-append date">
        <input class="input-medium search-query" type="text" id="date_end_id" name="date_end">
        <span class="add-on">
          <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
        </span>
      </div>
      <button class="btn btn-primary" type="submit">查询</button>
    </form>
    <p>XX统计</p>
    <div id="XX_chart_div" style="height:400px;padding:0px;" class="well"></div>
    <p>YY统计</p>
    <div id="YY_chart_div" style="height:400px;padding:0px;" class="well"></div>
  </div>
<hr/>
<script type="text/javascript">
  $(function(){
    $('#datetimepicker1').datetimepicker({
      format: 'yyyy-MM-dd',
      language: 'en',
      pickDate: true,
      pickTime: false
    });
    $('#datetimepicker2').datetimepicker({
      format: 'yyyy-MM-dd',
      language: 'en',
      pickDate: true,
      pickTime: false
    });
  });
</script>
</body>
</html>
