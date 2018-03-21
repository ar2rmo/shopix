<?
$conf = parse_ini_file ( './config/main.ini', true ) ;
if($conf['define']){
	foreach ( $conf['define'] as $k => $v){
		define($k,$v);
	}
}
if (!defined('ROOT_PATH')) define('ROOT_PATH','./');

require_once CLASSES_PATH.'application.php';

$app=new application();
if ($app->relocate_to_trimmed()) exit;

if ($app->src->uri->neq('uri_1','')) {
	$mod=str_replace('-','_',$app->src->uri->uri_1);
} else {
	$mod='main';
}

if ($mod!='admin') {
	if ($app->relocate_to_canonical()) exit;
}

if (!$app->run($mod,null,array('hidden'=>false))) {
	if (!$app->run('default',$mod)) {
		$app->err404();
	}
}
?>