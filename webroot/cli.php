<?
if (PHP_SAPI!='cli') exit;

$d=__DIR__;

define('ROOT_PATH',$d.'/');
define('LIBRARIES_PATH',$d.'/libraries/');
define('CLASSES_PATH',$d.'/classes/');
define('MODULES_PATH',$d.'/modules/');
define('TEMPLATES_PATH',$d.'/templates/');
define('CONFIG_PATH',$d.'/config/');
define('MEDIA_PATH',$d.'/media/');

require_once CLASSES_PATH.'application.php';

$app=new application();

if ($argc>1) {
	$mod=$argv[1];
	$app->run($mod,'CLI',array('cli'=>true));
}
?>