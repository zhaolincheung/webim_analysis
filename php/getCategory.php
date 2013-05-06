<?php
  define('CODE_BASE2','/server/www/code_base');
  require_once CODE_BASE . '/app/category/CategoryNamespace.class.php';
  
  //var_dump(CategoryNamespace::getAllCategory());
  $cid = $_GET['category'];
  
  //$category = CategoryNamespace::getCategoryById($cid);
  $category = CategoryNamespace::getCategoryByScriptIndex($cid);
  
  $majorCategory = CategoryNamespace::getChildByUrl($category['source_name']);

  //var_dump($majorCategory);
  echo json_encode($majorCategory);
?>
