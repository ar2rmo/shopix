<?
class field_array extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'inv_skip'=>true,
			'str_delimiter'=>', ',
			'db_tail_delim'=>false,
			'db_delimiter'=>',',
			'val_type'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'inv_itm'=>'%FIELD%: Invalid item type',
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (!is_array($value)) {
			$value=explode($this->descr->str_delimiter,$value);
		}
		
		switch ($this->descr->val_type) {
			case 'integer':
				$fdsc=new descriptor;
				$fdsc->set_val('dtype','integer');
				$fdsc->set_val('null',true);
			break;
			case 'real':
				$fdsc=new descriptor;
				$fdsc->set_val('dtype','real');
				$fdsc->set_val('null',true);
			break;
			case 'string':
				$fdsc=new descriptor;
				$fdsc->set_val('dtype','string');
				$fdsc->set_val('null',true);
			default:
				$fdsc=null;
		}
		
		$fld=null;
		if (!is_null($fdsc)) $fld=field::create($fdsc);
		
		$err=false;
		$itms=array();
		foreach ($value as $val) {
			if (is_null($fld)) {
				$itms[]=$val;
			} else {
				$fld->assign($val,false);
				if ($fld->is_def()) {
					$itms[]=$fld->value();
				} else {
					$err=true;
				}
			}
		}
		
		if ($err && !$this->descr->inv_skip) {
			$this->seterr(field::DT_ERR_VAL,'inv_itm');
			return false;
		}
		
		$this->setval($itms);
		return true;
	}
	
	protected function unformat_db($value) {
		return explode($this->descr->db_delimiter,$value);
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_STR;
			return implode($this->descr->db_delimiter,$this->val).($this->descr->db_tail_delim?$this->descr->db_delimiter:'');
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return '';
		return implode($this->descr->str_delimiter,$this->val);
	}
	
	protected function get_html() {
		return htmlspecialchars($this->get_txt());
	}
}

class field_string extends field {
	protected static function descr_init(descriptor $descr){
		$descr->set_defaults(array(
			'null'=>false,
			'not_blank'=>false,
			'blank_null'=>false,
			'not_whitespace'=>false,
			'string_null'=>false,
			'not_numeric'=>false,
			'valid_email'=>false,
			'html_inside'=>false,
			'max_len'=>null,
			'max_chars'=>null,
			'min_chars'=>null,
			'exact_chars'=>null,
			'charset'=>null,
			'regexp'=>null
		));	
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'blank'=>'%FIELD%: String can not be blank',
			'numeric'=>'%FIELD%: String value can not be numeric',
			'max_len'=>'%FIELD%: Byte length',
			'exact_chars'=>'%FIELD%: String length',
			'max_chars'=>'%FIELD%: Maximal string length',
			'min_chars'=>'%FIELD%: Minimal string length',
			'inval_email'=>'%FIELD%: String must contain a valid email address',
			'inval_charset'=>'%FIELD%: String contains unacceptable characters',
			'inval_regexp'=>'%FIELD%: String does not match regexp'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		$valid=true;
		
		if (is_null($value)) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$value='';
			}
		} elseif (is_array($value)) {
			$value=implode(', ',$value);
		}

		if ($this->descr->null && $this->descr->blank_null && ($value=='')) {
			$this->setnull();
			return true;
		}
		
		if ($this->descr->null && $this->descr->string_null && ($value=='NULL')) {
			$this->setnull();
			return true;
		}
		
		$chars=mb_strlen($value);
		$len=strlen($value);
		$chkmin=true;
	
		if ($this->descr->not_blank && ($value=='')) {
			$chkmin=false;
			$this->seterr(field::DT_ERR_VAL,'blank');
			$valid=false;
		}
		if ($this->descr->not_whitespace && (trim($value)=='')) {
			$this->seterr(field::DT_ERR_VAL,'whitespace');
			$valid=false;
		}
		if ($this->descr->not_numeric && is_numeric($value)) {
			$this->seterr(field::DT_ERR_VAL,'numeric');
			$valid=false;
		}
		if (!is_null($this->descr->max_len) && ($len > $this->descr->max_len)) {
			$this->seterr(field::DT_ERR_VAL,'max_len');
			$valid=false;
		}
		if (!is_null($this->descr->exact_chars) && ($chars != $this->descr->exact_chars)) {
			$this->seterr(field::DT_ERR_VAL,'exact_chars');
			$valid=false;
		}
		if (!is_null($this->descr->max_chars) && ($chars > $this->descr->max_chars)) {
			$this->seterr(field::DT_ERR_VAL,'max_chars');
			$valid=false;
		}
		if ($chkmin && !is_null($this->descr->min_chars) && ($chars < $this->descr->min_chars)) {
			$this->seterr(field::DT_ERR_VAL,'min_chars');
			$valid=false;
		}
		
		if ($this->descr->valid_email && !filter_var($value,FILTER_VALIDATE_EMAIL)) {
			$this->seterr(field::DT_ERR_VAL,'inval_email');
			$valid=false;
		}
		
		if (!is_null($this->descr->charset)) {
			switch ($this->descr->charset) {
				case 'numbers':
					$re='/^[0-9]*$/';
				break;
				case 'latin':
					$re='/^[A-Za-z]*$/';
				break;
				case 'numlat':
					$re='/^[A-Za-z0-9]*$/';
				break;
				case 'numlatsp':
					$re='/^[-A-Za-z0-9\._@]*$/';
				break;
				case 'uri':
					$re='/^[-a-z0-9]*$/';
				break;
			}
			if (!preg_match($re,$value)) {
				$this->seterr(field::DT_ERR_VAL,'inval_charset');
				$valid=false;
			}
		}
		
		if (!is_null($this->descr->regexp)) {
			if (!preg_match($this->descr->regexp,$value)) {
				$this->seterr(field::DT_ERR_VAL,'inval_regexp');
				$valid=false;
			}
		}
		
		if ($valid) {
			$this->setval($value);
			return true;
		} else {
			return false;
		}
	}
	
	protected function unformat_db($value) {
		return (string)$value;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_STR;
			return $this->val;
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return '';
		if ($this->descr->html_inside) {
			return strip_tags($this->val);
		} else {
			return (string)$this->val;
		}
	}
	
	protected function get_html() {
		if ($this->is_null()) return '';
		if ($this->descr->html_inside) {
			return (string)$this->val;
		} else {
			return htmlspecialchars($this->val);
		}
	}
}

class field_integer extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'inv_null'=>false,
			'not_zero'=>false,
			'zero_null'=>false,
			'positive'=>false,
			'min'=>null,
			'max'=>null,
			'onfract'=>null,
			'format'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'inval'=>'%FIELD%: Value is not a number',
			'fract'=>'%FIELD%: Value is not an integer number',
			'zero'=>'%FIELD%: Zero is not accepted',
			'negative'=>'%FIELD%: Negative is not accepted',
			'max'=>'%FIELD%: Maximal value',
			'min'=>'%FIELD%: Minimal value'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value=='null')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			}
		}
		
		if ($this->descr->null) {
			if (($value===false) || ($value==='')) {
				$this->setnull();
				return true;
			}
			if (($this->descr->zero_null) && ($value==0)) {
				$this->setnull();
				return true;
			}
		}
		
		$val=false;
		if (is_numeric($value)) {
			if (floor($value)==$value) {
				$val=$value;
			} else {
				if ($this->descr->onfract) {
					switch ($this->descr->onfract) {
						case 'deny':
							$this->seterr(field::DT_ERR_TYP,'fract');
						break;
						case 'round':
							$val=round($value,0);
						break;
						case 'ceil':
							$val=ceil($value);
						break;
						case 'floor':
						default:
							$val=floor($value);
					}
				} else {
                    $val=floor($value);
				}
			}
			if ($val===false) {
				return false;
			} else {
				$valid=true;
				
				if ($this->descr->not_zero && ($val == 0)) {
					$this->seterr(field::DT_ERR_VAL,'zero');
					$valid=false;
				}
				if ($this->descr->positive && ($val < 0)) {
					$this->seterr(field::DT_ERR_VAL,'negative');
					$valid=false;
				}

				if (!is_null($this->descr->max) && ($val > $this->descr->max)) {
					$this->seterr(field::DT_ERR_VAL,'max');
					$valid=false;
				}
				if (!is_null($this->descr->min) && ($val < $this->descr->min)) {
					$this->seterr(field::DT_ERR_VAL,'min');
					$valid=false;
				}
				
				if ($valid) {
					if ($val>PHP_INT_MAX) $val=(float)$val;
					else $val=(int)$val;
					$this->setval($val);
					
					return true;
				} else {
					return false;
				}				
			}
		} else {
			if ($this->descr->inv_null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			}
		}
	}
	
	protected function unformat_db($value) {
		if ($value>PHP_INT_MAX) return (float)$value;
		return (int)$value;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_INT;
			return $this->val;
		}
	}
}

class field_real extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'inv_null'=>false,
			'not_zero'=>false,
			'zero_null'=>false,
			'positive'=>false,
			'min'=>null,
			'less'=>null,
			'max'=>null,
			'more'=>null,
			'accept_sep'=>null,
			'format'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'inval'=>'%FIELD%: Value is not a number',
			'zero'=>'%FIELD%: Zero is not accepted',
			'negative'=>'%FIELD%: Negative is not accepted',
			'max'=>'%FIELD%: Maximal value',
			'min'=>'%FIELD%: Minimal value'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value=='null')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			}
		}
		
		if ($this->descr->null) {
			if (($value===false)) {
				$this->setnull();
				return true;
			}
		}
		
		$val=str_replace(array(' ',"\xC2\xA0"),'',$value);
		$co=($this->descr->accept_sep=='comma');
		if ($this->descr->accept_sep!='point') {
			$comma=str_replace(',','.',$val,$cn);
			//var_dump($comma,$cn);
			if (($cn>1)&&($co)) {
				//more than one comma
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			} else {
				str_replace('.','',$val,$pn);
				if (($pn>0) && $co) {
					//only comma is allowed, but point is used
					$this->seterr(field::DT_ERR_TYP,'inval');
					return false;
				}
				if (($cn==1)&&($pn==0)) {
					//one comma, no points
					$val=$comma;
				}
			}
		}
		
		
		if (is_numeric($val)) {
			if (($this->descr->zero_null) && ($val==0)) {
				$this->setnull();
				return true;
			}
		
			$valid=true;
			
			if ($this->descr->not_zero && ($val == 0)) {
				$this->seterr(field::DT_ERR_VAL,'zero');
				$valid=false;
			}
			if ($this->descr->positive && ($val < 0)) {
				$this->seterr(field::DT_ERR_VAL,'negative');
				$valid=false;
			}

			if (!is_null($this->descr->max) && ($val > $this->descr->max)) {
				$this->seterr(field::DT_ERR_VAL,'max');
				$valid=false;
			}
			if (!is_null($this->descr->more) && ($val <= $this->descr->more)) {
				$this->seterr(field::DT_ERR_VAL,'min');
				$valid=false;
			}
			if (!is_null($this->descr->min) && ($val < $this->descr->min)) {
				$this->seterr(field::DT_ERR_VAL,'min');
				$valid=false;
			}
			if (!is_null($this->descr->less) && ($val >= $this->descr->less)) {
				$this->seterr(field::DT_ERR_VAL,'max');
				$valid=false;
			}
			
			if ($valid) {
				$this->setval((float)$val);
				return true;
			} else {
				return false;
			}				
		} else {
			if ($this->descr->inv_null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			}
		}
	}
	
	protected function unformat_db($value) {
		return (float)$value;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_STR;
			return sprintf('%0.20F',$this->val);
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return '';		
		$format=$this->descr->format;
		if (!is_null($format) && preg_match('/^(?:0([ ,\t]?)00)?0([\.,]?)(0{0,8})(-{0,8})(#{0,8})$/',$format,$mch)) {
			$zeros=mb_strlen($mch[3]);
			$dashes=mb_strlen($mch[4]);
			$hashes=mb_strlen($mch[5]);
			
			$fn=number_format($this->val,$zeros+$dashes+$hashes,$mch[2],$mch[1]);
			
			if ($hashes>0) $fn=preg_replace('/[\.,]?0{0,'.$hashes.'}$/','',$fn,1);
			if ($dashes>0) $fn=preg_replace('/[\.,]?0{'.$dashes.'}$/','',$fn,1);
			
			return $fn;
		} else {
			return (string)$this->val;
		}
	}
	
	protected function get_html() {
		return str_replace(array(' ','-'),array('&nbsp;','&#x2011;'),$this->get_txt());
	}
}

class field_datetime extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'inv_null'=>false,
			'future'=>false,
			'past'=>false,
			'makeval'=>true,
			'min'=>null,
			'max'=>null,
			'format'=>null,
			'onlytime'=>false
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'inval'=>'%FIELD%: Value is not a datetime',
			'max'=>'%FIELD%: Maximal value',
			'min'=>'%FIELD%: Minimal value'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value===false) || ($value=='null') || ($value=='')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'inval');
				return false;
			}
		}
		
		if (is_object($value) && ($value instanceof DateTime)) {
			$obj=$value;
		} else {
			if (is_numeric($value)) {
				$obj=new DateTime;
				$obj->setTimestamp($value);
			} else {
				$notime=true;
				$nodate=false;
				if (!$this->descr->onlytime && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$value)) $format='Y-m-d';
				elseif (!$this->descr->onlytime && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/',$value)) $format='d.m.Y';
				elseif (!$this->descr->onlytime && preg_match('/^[0-9]{1}\.[0-9]{2}\.[0-9]{4}$/',$value)) $format='j.m.Y';
				elseif (!$this->descr->onlytime && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/',$value)) {$format='Y-m-d H:i'; $notime=false;}
				elseif (!$this->descr->onlytime && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$value)) {$format='Y-m-d H:i:s'; $notime=false;}
				elseif (preg_match('/^[0-9]{2}:[0-9]{2}$/',$value)) {$format='H:i'; $nodate=true;}
				elseif (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/',$value)) {$format='H:i:s'; $nodate=true;}
				else $format=null;
				
				if (is_null($format)) {
					$this->seterr(field::DT_ERR_TYP,'inval');
					return false;
				}
				
				$obj=DateTime::createFromFormat($format,$value);
				
				if ($obj===false) {
					$this->seterr(field::DT_ERR_TYP,'inval');
					return false;
				}
				
				if ($notime) $obj->setTime(0,0,0);
				if ($nodate) $obj->setDate(0,0,0);
				
				if (!$this->descr->makeval) {
					$ng=$obj->format($format);
					
					if ($ng!=$value) {
						$this->seterr(field::DT_ERR_TYP,'inval');
						return false;
					}
				}
			}
		}
		
		$this->setval($obj);
		return true;
	}
	
	protected function unformat_plain($value) {
		if ($value instanceof DateTime) {
			return $value;
		} elseif (is_numeric($value)) {
			$d=new DateTime;
			$d->setTimestamp($value);
			return $d;
		}
		return null;
	}
	
	protected function unformat_db($value) {
		if ($this->descr->onlytime) {
			$d=DateTime::createFromFormat('H:i:s',$value);
		} else {
			$d=DateTime::createFromFormat('Y-m-d H:i:s',$value);
			if (is_null($d) || $d===false) $d=DateTime::createFromFormat('Y-m-d',$value);
			if (is_null($d) || $d===false) $d=DateTime::createFromFormat('H:i:s',$value);
			if (is_null($d) || $d===false) return null;
		}
		return $d;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			if ($this->descr->onlytime) {
				$type=PDO::PARAM_STR;
				return $this->val->format('H:i:s');
			} else {
				$type=PDO::PARAM_STR;
				return $this->val->format('Y-m-d H:i:s');
			}
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return '';
		$format=$this->descr->format;
		if (is_null($format)) {
			if ($this->descr->onlytime) $format='H:i:s';
			else $format='Y-m-d H:i:s';
		}
		return (string)$this->val->format($format);
	}
	
	protected function get_html() {
		return str_replace(array(' ','-'),array('&nbsp;','&#x2011;'),$this->get_txt());
	}
}

class field_boolean extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'str_true'=>'TRUE',
			'str_false'=>'FALSE'
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(

		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value==='null')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->setval(false);
				return true;
			}			
		}
		
		if ($value) $this->setval(true);
		else $this->setval(false);
	}
	
	protected function unformat_db($value) {
		return (bool)$value;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_BOOL;
			return $this->val?1:0;
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return '';
		return $this->val?$this->descr->str_true:$this->descr->str_false;
	}
	
	protected function get_html() {
		return $this->get_txt();
	}
}

class field_fsfile extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'max_size'=>null,
			'types'=>null,
			'moveto'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'nofile'=>'%FIELD%: No file uploaded',
			'empfile'=>'%FIELD%: Empty file',
			'toobig'=>'%FIELD%: File size is more than \'max_size\'',
			'types'=>'%FIELD%: Unacceptable file type'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value=='null')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_VAL,'nofile');
				return false;
			}			
		}
		
		if ($value['size']==0) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_VAL,'empfile');
				return false;
			}
		}
		
		if (!is_null($this->descr->max_size)) {
			if ($value['size'] > $this->descr->max_size){
				$this->seterr(field::DT_ERR_VAL,'toobig');
				return false;
			}
		}
		
		if (!is_null($this->descr->types) && is_array($this->descr->types)) {
			if (!in_array($value['type'],$this->descr->types)) {
				$this->seterr(field::DT_ERR_VAL,'types');
				return false;
			}
		}
		
		if (!is_null($this->descr->moveto)) {
			$mto=$this->descr->moveto;
			if (mb_substr($mto,-4)=='.***') {
				$mto=mb_substr($mto,0,-4).'.'.pathinfo($value['name'],PATHINFO_EXTENSION);
			}
			if (!is_writable(pathinfo($mto,PATHINFO_DIRNAME))) {
				trigger_error('Cannot move file to "'.$mto.'"',E_USER_WARNING);
				return false;
			}
			
			move_uploaded_file($value['tmp_name'], $mto);

			$this->setval(array('path'=>$mto,'type'=>$value['type'],'ext'=>pathinfo($mto,PATHINFO_EXTENSION)));
		} else {
			$this->setval(array('path'=>$value['tmp_name'],'type'=>$value['type'],'name'=>$value['name'],'ext'=>pathinfo($value['name'],PATHINFO_EXTENSION)));
		}
	}
	
	protected function unformat_db($value) {
		return null;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_BOOL;
			return $this->val?1:0;
		}
	}
	
	protected function get_txt() {
		if ($this->is_null()) return 'null';
		return $this->val['type'];
	}
	
	protected function get_html() {
		return $this->get_txt();
	}
}

class field_dbfile extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'max_size'=>null,
			'types'=>null,
			'dbmime'=>null,
			'dbfname'=>null,
			'dbfext'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'nofile'=>'%FIELD%: No file uploaded',
			'empfile'=>'%FIELD%: Empty file',
			'toobig'=>'%FIELD%: File size is more than \'max_size\'',
			'types'=>'%FIELD%: Unacceptable file type'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		if (is_null($value) || ($value=='null')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_VAL,'nofile');
				return false;
			}			
		}
		
		if ($value['size']==0) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_VAL,'empfile');
				return false;
			}
		}
		
		if (!is_null($this->descr->max_size)) {
			if ($value['size'] > $this->descr->max_size){
				$this->seterr(field::DT_ERR_VAL,'toobig');
				return false;
			}
		}
		
		if (!is_null($this->descr->types) && is_array($this->descr->types)) {
			if (!in_array($value['type'],$this->descr->types)) {
				$this->seterr(field::DT_ERR_VAL,'types');
				return false;
			}
		}
		
		$bin=file_get_contents($value['tmp_name']);
		
		$this->setval(array('binary'=>&$bin,'type'=>$value['type'],'name'=>$value['name'],'ext'=>pathinfo($value['name'],PATHINFO_EXTENSION)));
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_LOB;
			return $this->val['binary'];
		}
	}
	
	public function pdo_bind(PDOStatement $stmt) {
		if ($this->is_null()) {
			$null=null;
			$stmt->bindParam(':'.$this->descr->dbname,$null,PDO::PARAM_NULL);
			if (!is_null($this->descr->dbmime)) $stmt->bindParam(':'.$this->descr->dbmime,$null,PDO::PARAM_NULL);
			if (!is_null($this->descr->dbfname)) $stmt->bindParam(':'.$this->descr->dbfname,$null,PDO::PARAM_NULL);
			if (!is_null($this->descr->dbext)) $stmt->bindParam(':'.$this->descr->dbext,$null,PDO::PARAM_NULL);
		} else {
			$stmt->bindParam(':'.$this->descr->dbname,$this->val['binary'],PDO::PARAM_LOB);
			if (!is_null($this->descr->dbmime)) $stmt->bindParam(':'.$this->descr->dbmime,$this->val['type'],PDO::PARAM_STR);
			if (!is_null($this->descr->dbfname)) $stmt->bindParam(':'.$this->descr->dbfname,$this->val['name'],PDO::PARAM_STR);
			if (!is_null($this->descr->dbext)) $stmt->bindParam(':'.$this->descr->dbext,$this->val['ext'],PDO::PARAM_STR);
		}
	}
	protected function get_txt() {
		if ($this->is_null()) return 'null';
		return $this->val['type'];
	}
	
	protected function get_html() {
		return $this->get_txt();
	}
}

class field_captcha extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'captcha' => 'CAPTCHA failed'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid='';
		
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
		
		if(isset($_SESSION['kcaptcha']) && ($_SESSION['kcaptcha']) && ($_SESSION['kcaptcha'] == $value)){
			unset($_SESSION['kcaptcha']);
			$this->setval(true);
		} else {
			unset($_SESSION['kcaptcha']);
			$this->seterr(field::DT_ERR_VAL,'captcha');
		}
	}
	
	protected function unformat_db($value) {
		return (bool)$value;
	}
	
	protected function pdo_val(&$type) {
		if (!$this->is_def() || $this->is_null()) {
			$type=PDO::PARAM_NULL;
			return null;
		} else {
			$type=PDO::PARAM_BOOL;
			return $this->val?1:0;
		}
	}
	
	protected function get_txt() {
		//if ($this->is_null()) return 'FALSE';
		//return $this->val?'TRUE':'FALSE';
		return '';
	}
	
	protected function get_html() {
		return $this->get_txt();
	}
}
?>