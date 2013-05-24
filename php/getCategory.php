<?php
  define('CODE_BASE2','/server/www/ganji/ganji_online/code_base2');
  require_once CODE_BASE2 . '/app/category/CategoryNamespace.class.php';
  
  //var_dump(CategoryNamespace::getAllCategory());
  $cid = $_GET['category'];
  
  //$category = CategoryNamespace::getCategoryById($cid);
  $category = CategoryNamespace::getCategoryByScriptIndex($cid);
  
  $majorCategory = CategoryNamespace::getChildByUrl($category['source_name']);

  //var_dump($majorCategory);
  echo json_encode($majorCategory);
?>
