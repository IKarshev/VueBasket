<?
ob_start();
print_r($arResult);
$debug = ob_get_contents();
ob_end_clean();
$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/lk-params.log', 'w+');
fwrite($fp, $debug);
fclose($fp); 
?>