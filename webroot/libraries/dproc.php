<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'paginator.php';

class obj{
	public function __toString(){
		return implode('::',array_reverse(class_parents($this))).'.'.get_class($this);
	}
}

abstract class objarr extends obj implements Iterator, ArrayAccess, Countable, Serializable {
	protected $_array=array();
	
	public function clear() {
		$this->_array=array();
	}
	
	public function __toString(){
		return parent::__toString().'['.count($this).']';
	}	
	
	public function __get($name) {
		return $this->_array[$name];
	}
	
	public function __set($name,$value) {
		return $this->_array[$name]=$value;
	}
	
	public function __isset($name) {
		return isset($this->_array[$name]);
	}
	
	public function __unset($name) {
		unset($this->_array[$name]);
	}

	public function offsetExists($offset) {
		return isset($this->_array[$offset]);
	}
	
	public function offsetGet($offset) {
		return isset($this->_array[$offset])?$this->_array[$offset]:null;
	}
	
	public function offsetSet($offset, $value) {
		if (is_null($offset)) $this->_array[]=$value;
		else $this->_array[$offset]=$value;
	}
	
	public function offsetUnset($offset) {
		unset($this->_array[$offset]);
	}
	
	public function current() {
		return current($this->_array);
	}
	
	public function key() {
		return key($this->_array);
	}
	
	public function next() {
		return next($this->_array);
	}
	
	public function rewind() {
		return reset($this->_array);
	}
	
	public function valid() {
		return key($this->_array)!==null;
	}
	
	public function count() {
		return count($this->_array);
	}
	
	public function serialize () {
		return serialize($this->_array);
	}
	
	public function unserialize($serialized) {
		$this->_array=unserialize($serialized);
	}
}
class smartarr extends objarr{
	private function __construct(array &$data) {
		$this->_array=&$data;
	}
	public function getArray(){
		return $this->_array;
	}	
	static public function arrkey(array &$array,$key,$ro=false) {
		if (isset($array[$key])) {
			if ($ro) return self::ro($array[$key]);
			else return self::val($array[$key]);
		}
		else return null;
	}

	static public function val(&$value) {
		if (is_array($value)) return new smartarr($value);
		else return $value;
	}

	static public function ro($value) {
		return self::val($value);
	}
}

class descriptor {
	private $param=array();
	private $defaults=array();
	private $actual=array();
	private $init=false;
	
	public function __get($name) {
		//return smartarr::arrkey($this->actual,$name);
		if (isset($this->actual[$name])) return $this->actual[$name];
		else return null;
	}

	public function get_array() {
		return $this->actual;
	}

	protected function actualize() {
		$defs=array_diff_key($this->defaults,$this->param);
		$this->actual=array_merge($this->param,$defs);
	}

	public function set_defaults(array $defs) {
		$this->defaults=$defs;
		$this->actualize();
		$this->set_init();
	}

	public function set_def($name,$dval) {
		$this->defaults[$name]=$dval;
		if (!isset($this->param[$name])) $this->param[$name]=$dval;
	}

	public function set_val($name,$val) {
		$this->param[$name]=$val;
		$this->actual[$name]=$val;
	}
	
	public function set_init() {
		$this->init=true;
	}
	
	public function is_init() {
		return $this->init;
	}
}

class descriptors {
	public static function xml_file($fname) {
		return self::parse(simplexml_load_file($fname));
	}

	public static function xml_str($xml) {
		return self::parse(simplexml_load_string($xml));
	}

	protected static function parse(SimpleXMLElement $sxml) {
		switch ($sxml->getName()) {
			case 'fieldset':
				return self::parse_fields($sxml);
			break;
			case 'form':
				return self::parse_form($sxml);
			break;
			case 'messages':
				return self::parse_messages($sxml);
			break;
			default:
				trigger_error('Unknown type of XML descriptor',E_USER_WARNING);
				exit;
		}
	}

	protected static function parse_fields(SimpleXMLElement $sxml) {
		$descrs=array();
		foreach ($sxml->field as $field) {
			$descr=new descriptor;
			if (is_null($field['name']) || is_null($field['dtype'])) {
				if (is_null($field['name'])) {
					trigger_error('Name parameter is mandatory for Field Descriptor',E_USER_WARNING);
				}
				if (is_null($field['dtype'])) {
					trigger_error('dType parameter is mandatory for Field Descriptor',E_USER_WARNING);
				}
				continue;
			}
			foreach ($field->attributes() as $key => $val) {
				$descr->set_val($key,(string)$val);
			}
			/*foreach ($field->flags as $flags) {
				foreach ($flags->attributes() as $key => $val) {
					$descr->set_val($key,true);
				}
			}*/
			foreach ($field->flag as $flag) {
				if (is_null($flag['name'])) continue;
				$descr->set_val((string)$flag['name'],true);
			}
			foreach ($field->param as $param) {
				if (is_null($param['name'])) continue;
				if (is_null($param['null'])) $val=is_null($param['val'])?(string)$param:(string)$param['val'];
				else $val=null;
				$descr->set_val((string)$param['name'],$val);
			}
			foreach ($field->list as $array) {
				if (is_null($array['name'])) continue;
				$arr=array();
				foreach ($array->param as $param) {
					if (is_null($param['name'])) {
						if (is_null($param['null'])) $arr[]=is_null($param['val'])?(string)$param:(string)$param['val'];
						else $arr[]=null;
					} else {
						if (is_null($param['null'])) $arr[(string)$param['name']]=is_null($param['val'])?(string)$param:(string)$param['val'];
						else $arr[(string)$param['name']]=null;
					}
					
				}
				$descr->set_val((string)$array['name'],$arr);
			}

			$descrs[(string)$field['name']]=$descr;
		}
        return $descrs;
	}

	protected static function parse_form(SimpleXMLElement $sxml) {
		$descrs=array();
		foreach ($sxml->control as $control) {
			$descr=new descriptor;
			if (is_null($control['name']) || is_null($control['type'])) {
				if (is_null($control['name'])) {
					trigger_error('Name parameter is mandatory for Control Descriptor',E_USER_WARNING);
				}
				if (is_null($control['type'])) {
					trigger_error('Type parameter is mandatory for Control Descriptor',E_USER_WARNING);
				}
				continue;
			}
			foreach ($control->attributes() as $key => $val) {
				$descr->set_val($key,(string)$val);
			}
			/*foreach ($control->flags as $flags) {
				foreach ($flags->attributes() as $key => $val) {
					$descr->set_val($key,true);
				}
			}*/
			foreach ($control->flag as $flag) {
				if (is_null($flag['name'])) continue;
				$descr->set_val((string)$flag['name'],true);
			}
			foreach ($control->param as $param) {
				if (is_null($param['name'])) continue;
				if (is_null($param['null'])) $val=is_null($param['val'])?(string)$param:(string)$param['val'];
				else $val=null;
				$descr->set_val((string)$param['name'],$val);
			}
			foreach ($control->list as $array) {
				if (is_null($array['name'])) continue;
				$arr=array();
				foreach ($array->param as $param) {
					if (is_null($param['name'])) {
						if (is_null($param['null'])) $arr[]=is_null($param['val'])?(string)$param:(string)$param['val'];
						else $arr[]=null;
					} else {
						if (is_null($param['null'])) $arr[(string)$param['name']]=is_null($param['val'])?(string)$param:(string)$param['val'];
						else $arr[(string)$param['name']]=null;
					}
					
				}
				$descr->set_val((string)$array['name'],$arr);
			}

			$descrs[(string)$control['name']]=$descr;
		}
        return $descrs;
	}
	
	protected static function parse_messages(SimpleXMLElement $sxml) {
		$descrs=array();
		foreach ($sxml->field as $field) {
			$descr=new descriptor;
			if (is_null($field['name'])) {
				trigger_error('Name parameter is mandatory for Error Messages Descriptor',E_USER_WARNING);
				continue;
			}
			foreach ($field->error as $error) {
				if (is_null($error['spec'])) continue;
				$descr->set_val((string)$error['spec'],(string)$error);
			}

			$descrs[(string)$field['name']]=$descr;
		}
        return $descrs;
	}
}

interface i_field {
	public function __construct(descriptor $descr);
	public function is_def();
	public function is_null();
	public function setnull();
	public function get_err();
	public function get_errmsg();
	public function get_invalid();
	public function assign($value,$trusted=false,$format='plain');
	public function value($format='plain');
}

abstract class pdo_field {
	protected function get_db() {
		$type=null;
		$val=$this->pdo_val($type);
		
		if ($type===PDO::PARAM_BOOL) {
			return $val?'1':'0';
		} elseif ($type===PDO::PARAM_NULL) {
			return 'null';
		} elseif ($type===PDO::PARAM_INT) {
			return $val;
		} elseif ($type===PDO::PARAM_STR) {
			$ln=DBP::getLink();
			return $ln->Quote($val,PDO::PARAM_STR);
		} elseif ($type===PDO::PARAM_LOB) {
			$ln=DBP::getLink();
			return $ln->Quote($val,PDO::PARAM_LOB);
		} else {
			if (preg_match('/^[0-9]*(?:\.[0-9]+)?$/',$val)) {
				return $val;
			} else {
				$ln=DBP::getLink();
				return $ln->Quote($val);
			}
		}
	}
	
	protected function get_pdo() {
		if (!$this->is_def() || $this->is_null()) return array('val'=>null,'type'=>PDO::PARAM_NULL);
		$val=$this->pdo_val($type);
		return array('val'=>$val,'type'=>$type);
	}
	
	abstract protected function pdo_val(&$type);
	
	public function pdo_bind(PDOStatement $stmt) {
		$val=$this->pdo_val($type);
		$stmt->bindParam(':'.$this->descr->dbname,$val,$type);
	}

}

abstract class field extends pdo_field implements i_field {
	private $def;
	private $null;
	protected $val;
	protected $invalid;

	protected $err;
	protected $especs;

	public $descr;
	public $edescr;

	const DT_ERR_UND = 0;
	const DT_ERR_TYP = 1;
	const DT_ERR_VAL = 2;
	const DT_ERR_REF = 3;
	const DT_ERR_LOG = 4;
 
	protected $baseerr=array(
		field::DT_ERR_UND => 'Undefined value',
		field::DT_ERR_TYP => 'Type missmatch',
		field::DT_ERR_VAL => 'Unacceptable value',
		field::DT_ERR_REF => 'Unreferenced value',
		field::DT_ERR_LOG => 'Logical error'
	);
	
	final public static function create(descriptor $descr,$edescr=null) {
		$dtype=$descr->dtype;
		/*if (mb_substr($dtype,0,7)=='legacy_') {
			$ftype='legacy_field';
		} else {*/
			$ftype='field_'.$dtype;
			if (class_exists($ftype)) {
				return new $ftype($descr,$edescr);
			} else {
				trigger_error('Bad data type: '.$ftype,E_USER_ERROR);
				return false;
			}
		//}
        return new $ftype($descr,$edescr);
	}
	
	final public static function any_descr_init(descriptor $descr) {
		$dtype=$descr->dtype;
		$ftype='field_'.$dtype;
		if (class_exists($ftype)) {
			if (!$descr->is_init()) $ftype::descr_init($descr);
		} else {
			trigger_error('Bad data type: '.$ftype,E_USER_ERROR);
			return false;
		}
	}

	final public function __construct(descriptor $descr,$edescr=null) {
		if (!$descr->is_init()) $this->descr_init($descr);
		$this->descr=$descr;
		
		if ($edescr===false) {
			$this->edescr=null;
		} else {
			$this->make_edescr($edescr);
		}
		
		$this->reset();
	}
	
	//abstract protected static function descr_init(descriptor $descr);
	
	final public function make_edescr(descriptor $edescr=null) {
		if (is_null($edescr)) $edescr=new descriptor;
		if (method_exists($this,'edescr_init')) $this->edescr_init($edescr);
		$this->edescr=$edescr;		
	}

	final public function is_def() {
		return $this->def;
	}

	final public function is_null() {
		return $this->null;
	}

	final public function get_err() {
		return array('base'=>$this->err,'specs'=>$this->especs);
	}

	final public function get_invalid() {
		return $this->invalid;
	}

	final public function setnull() {
		$this->def=true;
		$this->val=null;
		$this->null=true;
		$this->err=null;
		$this->espec=null;
	}

	final protected function makeset() {
		$this->def=true;
		$this->null=false;
		$this->err=null;
		$this->espec=null;
	}

	final protected function setval($val) {
		$this->val=$val;
		$this->makeset();
	}
	
	final protected function reset() {
		$this->def=false;
		$this->val=null;
		$this->null=false;
		
		$this->err=field::DT_ERR_UND;
		$this->espec=array();
	}

	final protected function seterr($err,$espec=null) {
		if (!is_null($this->err) && ($err < $this->err)) return;
		
		$this->def=false;
		$this->val=null;
		$this->null=false;
		
		if (is_null($this->err) || ($err > $this->err)) {
			$this->err=$err;
			if (is_null($espec)) $this->especs=array();
			else $this->especs=array($espec);
		} elseif ($err == $this->err) {
			if (!is_null($espec)) $this->especs[]=$espec;
		}
	}

	public function get_errmsg() {
		if (is_null($this->especs)) return null;
		$msgs=array();
		if (is_null($this->edescr)) {
			$this->make_edescr();
		}
		foreach ($this->especs as $espec) {
			if (is_null($this->edescr->$espec)) { 
				$msgs[]=$this->descr->name.': '.$espec;
			} else {
				$msgs[]=str_replace(array('%FIELD%'),array($this->descr->name),$this->edescr->$espec);
			}
		}
		return $msgs;
	}
	
	//abstract protected static function edescr_init(descriptor $edescr);

	public function assign($value,$trusted=false,$format='plain') {
		if ($trusted) {
			$mformat='unformat_'.$format;
        	if (method_exists($this,$mformat)) {
        		$val=$this->$mformat($value);
				if (is_null($val)) $this->setnull();
				else $this->setval($val);
        	} else {
        		trigger_error('Invalid format while assigning to '.get_class($this),E_USER_WARNING);
        	}
        } else {
        	$this->validate($value,$format);
        }
	}

	protected function unformat_plain($value) {
		return $value;
	}
	
	protected function unformat_db($value) {
		return $value;
	}
	
	abstract protected function validate($value,$format='plain');

	public function value($format='plain') {
		if (!$this->is_def()) return null;
		
		$mformat='get_'.$format;
		if (method_exists($this,$mformat)) {
       		return $this->$mformat($this->val);
       	} else {
       		trigger_error('Invalid format while getting value from '.get_class($this),E_USER_WARNING);
       	}
	}

	protected function get_plain() {
		return $this->val;
	}
	
	protected function get_txt() {
		return (string)$this->val;
	}
	
	protected function get_nq() {
		return str_replace(array('"','\''),'',$this->get_txt());
	}
	
	protected function get_html() {
		return htmlspecialchars($this->get_txt());
	}
}

/*LEGACY_FIELDS
*/ 

/*class legacy_field extends pdo_field implements i_field {
	public $ltype;
	public $descr;

	public function __construct(descriptor $descr) {
		$this->descr=$descr;
		$dtype=$this->descr->dtype;
		if (mb_substr($dtype,0,7)=='legacy_') {
			$ltype=mb_substr($dtype,7);
			$lcstype='type_'.$ltype;
		} else {
			trigger_error('Bad legacy data type',E_USER_ERROR);
			return false;
		}

		$old_descr=$this->descr->get_array();
		unset($old_descr['dtype']);
		$old_descr['type']=$ltype;
        $this->ltype = new $lcstype($old_descr);

   		if ($this->ltype instanceof typedb) {
   			$this->ltype->setdb(db::$obj);
   		}
	}

	public function is_def() {
		return $this->ltype->isdef();
	}

	public function is_null() {
		return $this->ltype->isnull();;
	}

	public function get_err() {
		return array('base'=>$this->ltype->geterr(),'spec'=>null);
	}

	public function setnull() {
		$this->ltype->setnull();
	}

	public function get_errmsg() {
		return $this->ltype->geterrmsg();
	}

	public function get_invalid() {
		return $this->ltype->raw();
	}

	public function assign($value,$trusted=false,$format='plain') {
        if ($trusted) {
        	$this->ltype->setval($value);
        } else {
        	$this->ltype->assign($value);
        }
	}

	public function value($format='plain') {
       	switch ($format) {
			case 'plain':
				return $this->ltype->plain();
			break;
			case 'html':
				return $this->ltype->html();
			break;
			case 'txt':
				return htmlspecialchars_decode($this->ltype->html());
			break;
			case 'db':
				return $this->get_db();
			break;
			case 'pdo':
				return $this->get_pdo();
			break;
			default:
				trigger_error('Invalid format while getting value from legacy datatype '.get_class($this->ltype),E_USER_WARNING);
		}
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		}
		
		$mval=$this->ltype->mysql();
		if (is_string($mval)&&substr($mval,0,1)=='"'&&substr($mval,-1)=='"') {
			echo 'Quoted';
			$type=PDO::PARAM_STR;
			return substr($mval,1,-1);
		} elseif (is_numeric($mval)) {
			$type=PDO::PARAM_INT;
			return $mval;
		} elseif ($mval=='NULL') {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_STR;
			return $mval;
		}
	}
} */

class fieldset extends objarr {
	function __construct(array $descrs,array $edescrs=null) {
		foreach ($descrs as $fid=>$fdescr) {
	    	$fld=&$this->_array[$fid];
       		$fld=field::create($fdescr,false);
        }
		
		if (!is_null($edescrs)) $this->make_edescrs($edescrs);
	}
	
	final function make_edescrs(array $edescrs) {
		foreach ($edescrs as $fid=>$fdescr) {
	    	if (isset($this->_array[$fid])) {
				$fld=&$this->_array[$fid];
				$fld->make_edescr($fdescr);
			}
        }
	}

    function __get($key) {
		$f4=mb_substr($key,0,4);
		$f3=mb_substr($f4,0,3);
		if ($f4=='fld_') {
    		$ts=microtime(true);
			return $this->get_field(mb_substr($key,4));
    	} elseif ($f4=='def_') {
    		return $this->is_def(mb_substr($key,4));
    	} elseif ($f3=='ht_') {
    		return $this->get_value(mb_substr($key,3),'html');
    	} elseif ($f3=='tx_') {
    		return $this->get_value(mb_substr($key,3),'txt');
		} elseif ($f3=='nq_') {
    		return $this->get_value(mb_substr($key,3),'nq');
    	} elseif ($f3=='db_') {
    		return $this->get_value(mb_substr($key,3),'db');
    	} elseif ($f4=='pdo_') {
    		return $this->get_value(mb_substr($key,4),'pdo');
    	} else {
    		return $this->get_value($key);
    	}
    }
	
	function __set($key,$val) {
		$this->set_value($key,$val,true);
    }

    final function is_def($key) {
    	if (isset($this->_array[$key])) {
    		return $this->_array[$key]->is_def();
    	} else {
    		trigger_error('There\'s no field "'.$key.'" in fieldset',E_USER_NOTICE);
    		return null;
    	}
    }

    final function get_field($key) {
    	if (isset($this->_array[$key])) {
    		return $this->_array[$key];
    	} else {
    		//trigger_error('There\'s no field "'.$key.'" in fieldset',E_USER_NOTICE);
    		return null;
    	}
    }
	
	final function get_fields() {
    	return $this->_array;
    }

    final function get_value($key,$format='plain') {
		if (isset($this->_array[$key])) {
			return $this->_array[$key]->value($format);
    	} else {
    		trigger_error('There\'s no field "'.$key.'" in fieldset "'.get_class($this).'"',E_USER_NOTICE);
    		return null;
    	}
    }

	final function get_values($format='plain',$invalid=false,&$errs=null) {
		$vals=array();
		$errs=array();
		foreach ($this->_array as $fid=>$field) {
        	if ($field->is_def()) {
        		$vals[$fid]=$field->value($format);
        	} else {
        		$errs[$fid]=$field->get_err();
        		if ($invalid && $errs[$fid]!=field::DT_ERR_UND)
        			$vals[$fid]=$field->get_invalid();
        	}
        }
        return $vals;
	}

	final function set_value($key,$value,$trusted=false,$format='plain') {
		if (isset($this->_array[$key])) {
			$this->_array[$key]->assign($value,$trusted,$format);
			
			$m_onchange='onchange_'.$key;
			if (method_exists($this,$m_onchange)) {
				$this->$m_onchange($this->_array[$key]);
			}
    	} else {
    		trigger_error('There\'s no field "'.$key.'" in fieldset',E_USER_NOTICE);
    	}
    }

	final function set_values($values,$trusted=false,$format='plain') {
		$vals=array();
		$errs=array();
		foreach ($values as $key=>$value) {
        	$this->set_value($key,$value,$trusted,$format);
        }
	}

	final function get_errors() {
		$errs=array();
		foreach ($this->_array as $fid=>$field) {
        	if (!$field->is_def()) {
        		$errs[$fid]['typ']=$field->get_err();
        		$errs[$fid]['msg']=$field->get_errmsg();
        	}
        }
        return $errs;
	}
}

abstract class fieldset_singleton {
	//abstract protected static function gen_obj();
	
	public static function get_obj() {
		if (is_null(static::$obj)) static::$obj=static::gen_obj();
		return static::$obj;
	}
	
	public static function get($name) {
		static::get_obj()->get_value($name,'plain');
	}
	
	public static function get_ht($name) {
		static::get_obj()->get_value($name,'html');
	}
}

interface i_control {
	public function __construct(descriptor $descr);
	public function html_label();
	public function html_input(i_field $fld=null);
	public function data_post(i_field $fld);
}

abstract class control implements i_control {
	public $field;
	public $descr;
	
	public $post_prefix=null;
	public $post_suffix=null;

	final public static function create(descriptor $descr) {
		$type=$descr->type;
		/*if (mb_substr($type,0,7)=='legacy_') {
			$ctype='legacy_control';
		} else {*/
			$ctype='control_'.$type;
			if (class_exists($ctype)) {
				return new $ctype($descr);
			} else {
				trigger_error('Bad control type "'.$ctype.'"',E_USER_ERROR);
				return false;
			}
		//}
        return new $ctype($descr);
	}

	public function __construct(descriptor $descr) {
		$this->descr=$descr;
	}

	public function html_label() {
		$name=$this->descr->name;
		$capt=is_null($this->descr->caption)?$this->descr->name:$this->descr->caption;
		return "<label for=\"$name\">$capt</label>";
	}
	
	protected function html_tag($tag,$attribs,$closed=true) {
		$atts=array();
		foreach ($attribs as $key=>$val) {
			if (is_null($val)) {
				$atts[]=$key;
			} else {
				$atts[]=$key.'="'.$val.'"';
			}
		}
		
		$ats=(count($atts)>0)?(' '.implode(' ',$atts)):'';
		$cls=$closed?' /':'';
		
		return '<'.$tag.$ats.$cls.'>';
	}

	protected function tgattribs_t() {
		$ex='';
        if (!is_null($this->descr->css_class)) $ex.=' class="'.$this->descr->css_class.'"';
        if (!is_null($this->descr->css_style)) $ex.=' style="'.$this->descr->css_style.'"';
		if (!is_null($this->descr->tgattribs) && is_array($this->descr->tgattribs)) {
			foreach ($this->descr->tgattribs as $tgk=>$tgv) {
				$ex.=' '.$tgk.'="'.$tgv.'"';
			}
		}
        return $ex;
	}
	
	protected function tgattribs_a() {
		$ex=array();
		if (!is_null($this->descr->css_class)) $ex['class']=$this->descr->css_class;
        if (!is_null($this->descr->css_style)) $ex['style']=$this->descr->css_style;
		if (!is_null($this->descr->tgattribs) && is_array($this->descr->tgattribs)) {
			$ex=array_merge($ex,$this->descr->tgattribs);
		}
        return $ex;
	}
	
	protected function post_name() {
		$post=$this->descr->name;
		if (!is_null($this->post_prefix)) $post=$this->post_prefix.$post;
		if (!is_null($this->post_suffix)) $post.=$this->post_suffix;
		return $post;
	}

	abstract public function html_input(i_field $fld=null);

	public function data_post(i_field $fld=null) {
        $pn=$this->post_name();
		if (src_post::def($pn)) {
			$fld->assign(src_post::get($pn));
        }
	}
}

/* class legacy_control implements i_control {
	public $descr;

	protected $linput;

	public function __construct(descriptor $descr) {
		$this->descr=$descr;
		$type=$this->descr->type;
		if (mb_substr($type,0,7)=='legacy_') {
			$ltype=mb_substr($type,7);
			$lcstype='input_'.$ltype;
		} else {
			trigger_error('Bad legacy data type',E_USER_ERROR);
			return false;
		}

		$old_descr=$this->descr->get_array();
		unset($old_descr['type']);
		if (isset($old_descr['css_class'])) {$old_descr['class']=$old_descr['css_class']; unset($old_descr['css_class']);}
		if (isset($old_descr['css_style'])) {$old_descr['style']=$old_descr['css_style']; unset($old_descr['css_style']);}
		$caption=isset($old_descr['caption'])?$old_descr['caption']:$old_descr['name']; unset($old_descr['caption']);
		$name=$old_descr['name']; unset($old_descr['name']);
		$old_descr['cont']=$ltype;
        $this->linput = new $lcstype($name,array('name'=>$caption,'type'=>array(),'form'=>$old_descr),db::$obj);
	}

	public function html_label() {
		return $this->linput->label();
	}

	public function html_input(i_field $fld=null) {
		$this->linput->data=$fld->ltype;
		return $this->linput->output();
	}

	public function data_post(i_field $fld) {
        $this->linput->data=$fld->ltype;
        $this->linput->validate('post');
        //var_dump($this->linput->data->isdef(),$this->linput->data->output('plain'),$this->linput->data->output('raw'));
	}
} */

class cform {
	protected $fieldset;
	protected $controls;
	
	protected $logerrs=array();
	protected $validators=array();
	
	public $post_prefix=null;
	public $post_suffix=null;

	function __construct(array $descrs,$fieldset=null) {
		foreach ($descrs as $fid=>$fdescr) {
	    	$cnt=&$this->controls[$fid];
       		$cnt=control::create($fdescr);
        }
        $this->bind_fieldset($fieldset);
		
	}
	
	function bind_fieldset($fieldset) {
		if ($fieldset instanceof fieldset) {
        	$this->fieldset=$fieldset;
        } elseif (is_array($fieldset)) {
        	$this->fieldset=new fieldset($fieldset);
        } else {
			$this->fieldset=new fieldset(array());
		}
	}
	
	function __get($k){
		if(isset($this->controls[$k])){
			return $this->controls[$k];
		}
		else return null;
	}
	
	function html_data() {
        $controls=array();
        foreach ($this->controls as $key=>$cont) {
        	$field=$this->fieldset->get_field($key);
			$cont->post_prefix=$this->post_prefix;
			$cont->post_suffix=$this->post_suffix;
			$controls[$key]=array('label'=>$cont->html_label(),'input'=>$cont->html_input($field));
        }
        return $controls;
	}
	
	function validate_post(&$errs=null) {
        $valid=true;
        foreach ($this->controls as $key=>$cont) {
        	$field=$this->fieldset->get_field($key);
			
			$cont->post_prefix=$this->post_prefix;
			$cont->post_suffix=$this->post_suffix;

			if (!is_null($field)) {
				$cont->data_post($field);
				if (!$field->is_def()) {
					$err=$field->get_err();
					if ($err['base']!=field::DT_ERR_UND) $valid=false;
				}
			}
        }
        if (!$valid) {
			$errs=$this->get_errors();
		} else {
			foreach ($this->validators as $cls) {
				if (!$cls($this->fieldset,$err)) {
					$valid=false;
					if (is_array($err)) {
						foreach ($err as $fid=>$msg) {
							$this->add_error($msg,$fid);
						}
					} else {
						$this->add_error($err);
					}
				}
			}
		}
		if (!$valid) $errs=$this->get_errors();
        return $valid;
	}
	
	function get_errors() {
		$errs=array();
		foreach ($this->controls as $fid=>$cnt) {
			$fld=$this->fieldset->get_field($fid);
			if (!is_null($fld)) {
				if (!$fld->is_def()) {
					$err=$fld->get_err();
					if ($err['base']!=field::DT_ERR_UND) {
						$errs[$fid]['typ']=$err;
						$errs[$fid]['msg']=$fld->get_errmsg();
					}
				}
				if (isset($this->logerrs[$fid])) {
					foreach ($this->logerrs[$fid] as $lerr) {
						if (isset($errs[$fid]) && ($errs[$fid]['typ']['base']==field::DT_ERR_LOG)) {
							$errs[$fid]['msg'][]=$lerr;
						} else {
							$errs[$fid]['typ']['base']=field::DT_ERR_LOG;
							$errs[$fid]['msg']=array($lerr);
						}
					}
				}
			}
		}
		if (isset($this->logerrs['_'])) {
			$errs['_']['typ']['base']=field::DT_ERR_LOG;
			$errs['_']['msg']=$this->logerrs['_'];
		}
		return $errs;
	}
	
	function add_error($msg,$fld=null) {
		if (is_null($fld)) {
			$this->logerrs['_'][]=$msg;
		} else {
			$this->logerrs[$fld][]=$msg;
		}
	}
	
	function add_validator(closure $cls) {
		$this->validators[]=$cls;
	}
}

class mform extends objarr {
	public $post_prefix=null;
	
	protected $descrs;
	protected $validity=array();
	
	function __construct(array $descrs) {
		$this->descrs=$descrs;
	}
	
	function validate_post() {
		$all=true;
		foreach ($this as $id=>$frm) {
			if ($frm->validate_post()) {
				$this->validity[$id]=true;
			} else {
				$this->validity[$id]=false;
				$all=false;
			}
		}
		return $all;
	}
	
	function is_valid($id) {
		return $this->validity[$id];
	}
	
	protected function append($id,mobject $itm) {
		$frm=new cform($this->descrs,$itm);
		$frm->post_prefix=$this->post_prefix;
		$frm->post_suffix='_'.$id;
		$this[$id]=$frm;
	}
	
	function from_collection(mcollection $col, $idfld='id') {
		foreach ($col as $itm) {
			$this->append($itm->$idfld,$itm);
		}
	}
	
	function from_post_regex(callable $obj,$regex,$regnum=1) {
		foreach (src_post::get_keys() as $pkey) {
			if (preg_match($regex,$pkey,$mchs)) {
				$fid=$mchs[$regnum];
				$this->append($fid,$obj($fid));
			}
		}
	}
	
	function from_post_numid(callable $obj,$idfld='id') {
		if (!is_null($this->post_prefix)) $idfld=$this->post_prefix.$idfld;
		$this->from_post_regex($obj,'/^'.preg_quote($idfld).'_([0-9]+)/',1);
	}
	
	function html_data() {
		$frms_ht=array();
		foreach ($this as $id=>$frm) {
			$frms_ht[$id]=$frm->html_data();
		}
		return $frms_ht;
	}
}
abstract class mobject extends fieldset {
	//abstract protected static function load_descrs();
	
	protected static function init_descrs() {
		$descrs=static::load_descrs();
		
		foreach ($descrs as $descr) {
			field::any_descr_init($descr);
		}
		
		return $descrs;
	}
	
	public static function get_descrs() {
		if (is_null(static::$descrs)) static::$descrs=static::init_descrs();
		return static::$descrs;
	}

	function __get($key){
		$m_onget='ovget_'.$key;
		if (method_exists($this,$m_onget)) {
			return $this->$m_onget();
		}
		return parent::__get($key);
	}
	
	function __set($key,$val) {
		$m_onset='ovset_'.$key;
		if (method_exists($this,$m_onset)) {
			$this->$m_onset($val);
		} else {
			parent::__set($key,$val);
		}
    }

	public function __construct($arr=null,$format='plain',$prefix=null){
		parent::__construct(static::get_descrs());
		if (is_null($arr)) return;
		foreach($this as $val){
			if ($format=='db') {
				$fn=$val->descr->dbname;
			} else {
				$fn=$val->descr->name;
			}
			if (!is_null($prefix)) $fn=$prefix.$fn;
			if (isset($arr[$fn])){
				$this->set_value($val->descr->name,$arr[$fn],true,$format);
			}
		}
	}
	
	public function __toString(){
		return parent::__toString().'('.$this->Id.')';
	}
}

class mcollection extends objarr {
	function __get($key){
		$m_onget='ovget_'.$key;
		if (method_exists($this,$m_onget)) {
			return $this->$m_onget();
		}
		return parent::__get($key);
	}
	
	function __set($key,$val) {
		$m_onset='ovset_'.$key;
		if (method_exists($this,$m_onset)) {
			$this->$m_onset($val);
		} else {
			parent::__set($key,$val);
		}
    }
	
	public function __toString(){
		return parent::__toString().'['.count($this).']';
	}
}

require_once LIBRARIES_PATH.'dproc_fields.php';
require_once LIBRARIES_PATH.'dproc_controls.php';

?>