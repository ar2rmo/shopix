<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'sources.php';
require_once LIBRARIES_PATH.'templator.php';

abstract class module {
	protected $app;
	protected $data;

	function __construct(application $app,$data=null) {
		$this->app=$app;
		$this->data=$data;
	}
	
	public function get_param($param) {
		$const='static::mod_'.$param;
		if (defined($const)) {
			return constant($const);
		} else {
			return null;
		}
	}

	public function __get($key) {
		return $this->app->$key;
	}

	abstract function body();

	public function execute() {
		$ts=microtime(true);
		$this->body();
		$te=microtime(true);
		if (defined('DEBUG_MODLOG')) {
			$ls=date('Y-m-d H:i:s  ');
			$ls.='      '.str_pad(number_format($te-$ts,3),8,' ',STR_PAD_LEFT).' s   ';
			$ls.=str_pad('"'.get_class($this).'"',22).' > ';
			$ls.='PIP  ';
			$ls.='"'.$_SERVER['REQUEST_URI'].'"';
			//$ls.=' - "'.str_replace(array("\r\n","\n"),'\ ',print_r($this->data,true)).'"';
			$ls.="\n";
			file_put_contents(DEBUG_MODLOG,$ls,FILE_APPEND | LOCK_EX);
		}
	}

	public function output() {
		ob_start();
		$ts=microtime(true);
		$this->body();
		$te=microtime(true);
		$ob=ob_get_contents();
		ob_end_clean();
		if (defined('DEBUG_MODLOG')) {
			$ls=date('Y-m-d H:i:s  ');
			$ls.='      '.str_pad(number_format($te-$ts,3),8,' ',STR_PAD_LEFT).' s   ';
			$ls.=str_pad('"'.get_class($this).'"',22).' > ';
			$ls.='VAR  ';
			$ls.='"'.$_SERVER['REQUEST_URI'].'"';
			//$ls.=' - "'.str_replace(array("\r\n","\n"),'\ ',print_r($this->data,true)).'"';
			$ls.="\n";
			file_put_contents(DEBUG_MODLOG,$ls,FILE_APPEND | LOCK_EX);
		}
		return $ob;
	}
}

abstract class module_tpl extends module {
	protected $tpl;
	
	protected $abort=false;

	function __construct(application $app,$data=null) {
		parent::__construct($app,$data);

		$templ=$this->get_param('tpl');
		if (is_null($templ)) {
			$cls=get_class($this);
			if (substr($cls,0,4)=='mod_') $templ=substr($cls,4);
			else $templ='main';
		}
		$this->tpl=new template($templ);
		$this->init();
	}
	
	protected function abort() {
		$this->abort=true;
	}

	abstract protected function init();
	abstract protected function finite();

	abstract protected function priv();
	
	protected function add_sub_pc($module,$data=null) {
		$this->tpl->set('sub_'.$module,$this->app->drun($module,$data));
	}

	protected function add_sub($module,$data=null,$placeholder=null) {
		$this->tpl->set(is_null($placeholder)?('sub_'.$module):$placeholder,new tplclsr(function() use ($module,$data) {return $this->app->drun($module,$data);}));
	}

	protected function add_sub_stub($module) {
		$this->tpl->set('sub_'.$module,null);
	}

	public function execute() {
        $ts=microtime(true);
		if ($this->priv()) {
			$tp=microtime(true);
			$this->init();
			$ti=microtime(true);
			ob_start();
			$this->body();
			$tb=microtime(true);
			$ob=ob_get_contents();
			ob_end_clean();
			$this->ob_process($ob);
			if ($this->abort) return;
			$this->finite();
			$tf=microtime(true);
			$this->tpl->output();
			$tt=microtime(true);
			if (defined('DEBUG_MODLOG')) {
				$ls=date('Y-m-d H:i:s  ');
				$ls.='PRIV: '.str_pad(number_format($tp-$ts,3),8,' ',STR_PAD_LEFT).' s,'."\n                     ";
				$ls.='INIT: '.str_pad(number_format($ti-$tp,3),8,' ',STR_PAD_LEFT).' s,'."\n                     ";
				$ls.='BODY: '.str_pad(number_format($tb-$ti,3),8,' ',STR_PAD_LEFT).' s,'."\n                     ";
				$ls.='FINT: '.str_pad(number_format($tf-$tb,3),8,' ',STR_PAD_LEFT).' s,'."\n                     ";
				$ls.='TMPL: '.str_pad(number_format($tt-$tf,3),8,' ',STR_PAD_LEFT).' s   ';
				$ls.=str_pad('"'.get_class($this).'"',22).' > ';
				$ls.='VAR  ';
				$ls.='"'.$_SERVER['REQUEST_URI'].'"';
				//$ls.=' - "'.str_replace(array("\r\n","\n"),'\ ',print_r($this->data,true)).'"';
				$ls.="\n";
				file_put_contents(DEBUG_MODLOG,$ls,FILE_APPEND | LOCK_EX);
			}
        } else {
        	$this->auth_failed();
        }
	}
	
	protected function ob_process($ob) {
		//$this->tpl->cont=$ob;
		echo $ob;
	}

	protected function auth_failed() {
		$this->app->err403();
	}
}

class http {
	static function relocate($uri, $perm=false) {
		if (!$perm&&defined('DEBUG_RELOC')&&DEBUG_RELOC) {
			echo 'Relocating to <a href="'.$uri.'">'.$uri.'</a>';
		} else {
			header('Location: '.$uri,true,$perm?301:302);
		}
	}
	
	static function refresh(int $delay=null) {
		if (is_null($delay)) $delay=0;
		
		if (defined('DEBUG_RELOC')&&DEBUG_RELOC) {
			echo 'REFRESH IN '.$delay.'s.';
		} else {
			header('Refresh: '.$delay);
		}
	}

	static function err404() {
		header("HTTP/1.0 404 Not Found");
?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html><?
	}

	static function err403() {
		header("HTTP/1.0 403 Forbidden");
?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
</body></html><?
	}
	
	static function is_secure() {
		$isSecure = false;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$isSecure = true;
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
			$isSecure = true;
		}
		return $isSecure;
	}
	
	static function request_host() {
		return $_SERVER['HTTP_HOST'];
	}
}

class configs {
	protected $conf=array();
}

class application_base {
	protected $objs=array();
	protected $tokens;

	function __construct() {
		$this->phpsetup();
        $this->objinit();
	}

	protected function phpsetup() {
		setlocale(LC_ALL,'ru_UA.utf8');
		date_default_timezone_set('Europe/Kiev');
		mb_internal_encoding('UTF-8');
	}

	protected function objinit() {
		$this->objs['dbp']=new DBP;
		$this->objs['src']=new sourceset(array('post','get','uri'));
		$this->objs['http']=new http;
	}

	public function __get($key) {
		if (isset($this->objs[$key])) {
			$o=&$this->objs[$key];
			if ($o instanceof Closure) $this->objs[$key]=$o();
			return $this->objs[$key];
		}
		trigger_error('Application knows nothing about object "'.$key.'".',E_USER_WARNING);
	}

	protected function mod_create($module,$data=null) {
		$cmod='mod_'.$module;
		if (preg_match('/^[-_A-Za-z0-9]*$/',$module)) {
			if (file_exists(MODULES_PATH.$module.'.php')) {
				require_once(MODULES_PATH.$module.'.php');
				if (class_exists($cmod)) {
					if ($data) $mod=new $cmod($this,$data);
					$mod=new $cmod($this,$data);
					if (($mod instanceof module)) {
						return $mod;
					}
				}
			}
		}
		return null;
	}

	protected function check_type(module $module,$type) {
		if (is_null($type)) return true;
		foreach ($type as $param=>$cond) {
            $pval=$module->get_param($param);
            if (!is_null($pval)) {
            	if ($pval!=$cond) return false;
            }
		}
		return true;
	}

	public function run($module,$data=null,array $type=null) {
		$mod=$this->mod_create($module,$data);
		if (is_null($mod)) return false;
		if (!$this->check_type($mod,$type)) return false;
		if (!is_null($mod)) {
			$mod->execute();
			return true;
		}
		return false;
	}

	public function drun($module,$data=null,array $type=null) {
		$mod=$this->mod_create($module,$data);
		if (is_null($mod))  return null;
		if (!$this->check_type($mod,$type)) return null;
		if (!is_null($mod)) {
			return $mod->output();
		}
		return null;
	}

	public function err404() {
		$this->http->err404();
	}

	public function err403() {
		$this->http->err403();
	}
	
	public function is_secure() {
		$this->http->is_secure();
	}
	
	public function relocate_to_canonical($host) {
		if (preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',$host,$m)) {
			if ($m[1]=='') $m[1]='http';
			if ($m[1]=='https' && !$this->http->is_secure() || $m[2]!=$this->http->request_host()) {
				$this->http->relocate($m[1].'://'.$m[2].$_SERVER['REQUEST_URI'],true);
				return true;
			}
		}
		return false;
	}
	
	public function relocate_to_trimmed() {
		if ($this->src->uri->trim($turi)) {
			$this->http->relocate($turi,true);
			return true;
		}
		return false;
	}
	
	public function relocate_to_cleared() {
		if ($this->src->uri->clear($turi)) {
			$this->http->relocate($turi,true);
			return true;
		}
		return false;
	}
}
?>