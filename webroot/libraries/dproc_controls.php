<?
class control_text extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'text');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->maxlen)) {
			$att['maxlength']=$this->descr->maxlen;
		}

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld->is_def()) {
				$val=is_string($fld->value('plain'))?htmlspecialchars($fld->value('plain')):$fld->value('txt');
			} else {
				$val=$fld->get_invalid();
			}
		}
		$att['value']=$val;
		
		return $this->html_tag('input',$att);
	}
}

class control_mtext extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'text');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->rows)) {
			$att['rows']=$this->descr->rows;
		}

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld->is_def()) {
				$val=htmlspecialchars($fld->value('plain'));
			} else {
				$val=$fld->get_invalid();
			}
		}
		
		return $this->html_tag('textarea',$att,false).$val.'</textarea>';
	}
}

class control_list extends control {
	protected function get_list() {
		switch ($this->descr->source) {
			case 'dbproc':
				return $this->get_list_dbproc();
			case 'const':
				return $this->get_list_const();
			default:
				trigger_error('List Control Descriptor error. Invalid Source.',E_USER_ERROR);
				return array();
		}
	}
	
	protected function get_list_dbproc() {
		$proc_name=$this->descr->proc_name;
		$proc_param=is_null($this->descr->proc_param)?null:$this->descr->proc_param;
		
		if (is_null($proc_name)) {
			trigger_error('List Control Descriptor error. Source Procedure name is required.',E_USER_ERROR);
			return array();
		}

		$arr = array();
		$c=new cmbcol($this->descr->oclass);
		$c->loadByProc($proc_name,$proc_param);
		return $c;
	}
	
	protected function get_list_const() {
		$list=$this->descr->clist;
		
		if (is_null($list)) {
			trigger_error('List Control Descriptor error. List is required.',E_USER_ERROR);
			return array();
		}

		$arr = array();
		$c=new cmbcol;
		$c->bindList($list);
		return $c;
	}
	
	public function set_proc_param($name,$val){
		if(!is_null($name)&&is_string($name)&&$name!==''){
			if (!is_null($this->descr->proc_param->$name))
				$this->descr->proc_param->$name=$val;
		}
	}
	public function html_input(i_field $fld=null) {
		$att=array();
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		
		if (get_class($this)=='control_mlist') {
			$att['multiple']=null;
			$att['name'].='[]';
			if (!is_null($this->descr->size))
				$att['size']=$this->descr->size;
		}
			
		$sarr=array();
		if (!is_null($fld)&&$fld->is_def()) {
			$sarr=$fld->value('plain');
			if (!is_array($sarr)) $sarr=array($sarr);
		}
		
		$att=array_merge($att,$this->tgattribs_a());
		
		$keyfld="key";
		$valfld="val";
		if(!is_null($this->descr->keyfld)){
			$keyfld=$this->descr->keyfld;
		}
		if(!is_null($this->descr->keyfld)){
			$valfld=$this->descr->valfld;
		}
		//var_dump($keyfld,$valfld);
		$html=$this->html_tag('select',$att,false)."\n";
		if (!is_null($this->descr->preamble)) {
			if (is_array($this->descr->preamble)) {
				$frst=true;
				foreach ($this->descr->preamble as $key=>$val) {
					$oatt=array();
					if ($frst && (count($sarr)==0)) $oatt['selected']=null;
					$frst=false;
					$oatt['value']=$key;
					$html.=$this->html_tag('option',$oatt,false).(($val===true)?'&nbsp;':$val)."</option>\n";					
				}
			} else {
				$oatt=array();
				if (count($sarr)==0) $oatt['selected']=null;
				if (is_null($this->descr->preamble_key)) {
					$oatt['value']='null';
				} else {
					$oatt['value']=$this->descr->preamble_key;
				}
				$html.=$this->html_tag('option',$oatt,false).(($this->descr->preamble===true)?'&nbsp;':$this->descr->preamble)."</option>\n";
			}
		}
		foreach ($this->get_list() as $itm) {
			$oatt=array();
			if (in_array($itm->$keyfld,$sarr)) $oatt['selected']=null;
			$oatt['value']=$itm->$keyfld;
			$html.=$this->html_tag('option',$oatt,false).$itm->get_value($valfld,'html')."</option>\n";
		}
        $html.="</select>";

		return $html;
	}
}

class control_mlist extends control_list {
	public function data_post(i_field $fld) {
        $pn=$this->post_name();
		if (src_post::def($pn)) {
			$fld->assign(src_post::get($pn));
        } else {
			$fld->assign(array());
		}
	}
}

class cmbitm extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="integer" dbname="key" name="key"/>
				<field dtype="string" dbname="val" name="val"/>
			</fieldset>');
	}
}

class cmbitmsk extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="string" dbname="key" name="key"/>
				<field dtype="string" dbname="val" name="val"/>
			</fieldset>');
	}
}


class cmbitmht extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="integer" dbname="key" name="key"/>
				<field dtype="string" dbname="val" name="val">
					<flag name="html_inside" />
				</field>
			</fieldset>');
	}
}

class cmbitmskht extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="string" dbname="key" name="key"/>
				<field dtype="string" dbname="val" name="val">
					<flag name="html_inside" />
				</field>
			</fieldset>');
	}
}

class cmbcol extends mcollection {
	protected $otype;
	
	function __construct($otype=null) {
		if (!is_null($otype)) {
			$this->otype=$otype;
		} else {
			$this->otype='cmbitm';
		}
	}
	
	public function loadByProc($proc,array $params=null){
		$items=&$this->_array;
		DBP::Exec('call '.$proc,$params,
			function ($arr) use (&$items){
				$items[]=new $this->otype($arr,'db');
			}
		);
		//$this->_isFilled=1;
	}
	
	public function bindList(array $list){
		$items=&$this->_array;
		foreach ($list as $key=>$val) {
			$items[]=new cmbitm(array('key'=>$key,'val'=>$val));
		}
		//$this->_isFilled=1;
	}
}

class control_check extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'checkbox');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->maxlen)) {
			$att['maxlength']=$this->descr->maxlen;
		}

		if(is_null($fld)) {
			$val=false;
		} else {
			if ($fld->is_def()) {
				$val=$fld->value('plain');
			} else {
				$val=$fld->get_invalid();
			}
		}
		
		if ($val) {
			$att['checked']=null;
		}
		
		return $this->html_tag('input',$att);	
	}
	
	public function data_post(i_field $fld) {
		$pn=$this->post_name();
		if (src_post::def($pn)) {
			$fld->assign(true);
        } else {
			$fld->assign(false);
		}
	}
}

class control_file extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'file');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->types) && is_array($this->descr->types)) {
			$att['accept']=implode(',',$this->descr->types);
		}
		
		return $this->html_tag('input',$att);	
	}
	
	public function data_post(i_field $fld) {
        $pn=$this->post_name();
		if (isset($_FILES[$pn])){
			$file=$_FILES[$pn];
			
			if ($fld instanceof field_fsfile) {
				$fld->assign($file);
			}
			if ($fld instanceof field_dbfile) {
				$fld->assign($file);
			}
   		} else {
			$fld->assign(null);
		}
	}
}

class control_calendar extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'text');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld instanceof field_datetime) {
				if ($fld->is_def()) {
					if ($fld->is_null()) {
						$val='';
					} else {
						$dt=$fld->value('plain');
						$val=$dt->format('d.m.Y');
					}
				} else {
					$val=$fld->get_invalid();
				}
			} else {
				if ($fld->is_def()) {
					$val=$fld->value('html');
				} else {
					$val=$fld->get_invalid();
				}
			}
		}
		$att['value']=$val;
		
		$js='<script language="javascript">$(function(){$("#'.$this->descr->name.'").datepicker();})</script>';
		
		return $js.$this->html_tag('input',$att);
	}
}

class control_time extends control {
	public function html_input(i_field $fld=null) {
		$att=array();
		$att['name']=$this->post_name().'_h';
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		//$att['multiple']='';
		//$att['size']=1;
		
		$html_h=$this->html_tag('select',$att,false)."\n";
		
		$cur_h=null;
		$cur_m=null;
		if ($fld instanceof field_datetime) {
			if ($fld->is_def()) {
				$dt=$fld->value('plain');
				$cur_h=(int)$dt->format('G');
				$cur_m=(int)$dt->format('i');
			}
		}
		
		for ($h=0; $h<=23; $h++) {
			$oatt=array();
			if ($h==$cur_h) $oatt['selected']=null;
			$oatt['value']=$h;
			$html_h.=$this->html_tag('option',$oatt,false).$h."</option>\n";
		}
        $html_h.="</select>";
		
		$att=array();
		$att['name']=$this->post_name().'_m';
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		//$att['multiple']='';
		//$att['size']=1;
		
		$html_m=$this->html_tag('select',$att,false)."\n";
		
		$ms=1;
		if (!is_null($this->descr->mstep)) {
			$ms=$this->descr->mstep;
		}
		$cur=false;
		
		for ($m=0; $m<=59; $m+=$ms) {
			$oatt=array();
			if ($m==$cur_m) {
				$oatt['selected']=null;
				$cur=true;
			}
			if (!is_null($cur_m) && !$cur && $m>$cur_m) {
				$coatt=array();
				$coatt['selected']=null;
				$coatt['value']=$cur_m;
				$html_m.=$this->html_tag('option',$coatt,false).str_pad($cur_m,2,'0',STR_PAD_LEFT)."</option>\n";
				$cur=true;
			}
			$oatt['value']=$m;
			$html_m.=$this->html_tag('option',$oatt,false).str_pad($m,2,'0',STR_PAD_LEFT)."</option>\n";
		}
        $html_m.="</select>";

		return array('h'=>$html_h,'m'=>$html_m);
	}
	
	public function data_post(i_field $fld) {
        $pn=$this->post_name();
		if (src_post::def($pn.'_h')&&src_post::def($pn.'_m')) {
			$h=src_post::get($pn.'_h');
			$m=src_post::get($pn.'_m');
			if (is_numeric($h)&&is_numeric($m)) {
				$h=(int)$h;
				$m=(int)$m;
				if ($h>=0&&$h<=23&&$m>=0&&$m<=59) {
					$dt=new DateTime;
					$dt->setTime($h,$m,0);
					$dt->setDate(0,0,0);
					
					if ($fld instanceof field_datetime) $fld->assign($dt);
					else $fld->assign($dt->format('H:i'));
				}
			}
        }
	}
}

class control_password extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'password');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		$att['value']='';
		
		$ht1=$this->html_tag('input',$att);
		
		if (!is_null($this->descr->confirm) && $this->descr->confirm) {
			$att['name'].='_confirm';
			$att['id'].='_confirm';
			
			$ht2=$this->html_tag('input',$att);
			
			return array($ht1,$ht2);
		}
		
		return $ht1;
	}
	
	public function data_post(i_field $fld) {
        $pn=$this->post_name();
		if (src_post::def($pn)) {
			$p1=src_post::get($pn);
			$conf=true;
			if (!is_null($this->descr->confirm) && $this->descr->confirm) {
				$conf=false;
				if (src_post::def($pn.'_confirm')) {
					$p2=src_post::get($pn.'_confirm');
					if ($p1==$p2) $conf=true;
				}
			}
			if ($conf) {
				if ($p1=='') $fld->assign(null);
				else $fld->assign($p1);
			} else {
				$fld->assign('');
			}
        }
	}
}

class control_captcha extends control {
	public function html_input(i_field $fld=null) {
		$att=array();
		$att['id']=$this->post_name();
		$att=array_merge($att,$this->tgattribs_a());
		if (!is_null($this->descr->caption)) {
			$att['alt']=$this->descr->caption;
			$att['title']=$this->descr->caption;
		}
		
		$att['src']='/captcha';
		
		return $this->html_tag('img',$att);	
	}
	
	public function html_label() {
		$capt=is_null($this->descr->caption)?$this->descr->name:$this->descr->caption;
		return $capt;
	}
	
	public function data_post(i_field $fld) {

	}
}

class control_ckeditor extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'text');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->rows)) {
			$att['rows']=$this->descr->rows;
		}

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld->is_def()) {
				$val=htmlspecialchars($fld->value('plain'));
			} else {
				$val=$fld->get_invalid();
			}
		}
		
		$cfg=array();
		if (!is_null($this->descr->ckheight)) $cfg['height']=$this->descr->ckheight;
		if (!is_null($this->descr->ckwidth)) $cfg['width']=$this->descr->ckwidth;
		
		if (empty($cfg)) $cfs='';
		else $cfs=json_encode($cfg,JSON_UNESCAPED_UNICODE);
		$js='<script>$(document).ready(function(){$("textarea#'.$att['id'].'").ckeditor('.$cfs.');});</script>';
		
		return $js.$this->html_tag('textarea',$att,false).$val.'</textarea>';
	}
}

class control_mlhtml extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'text');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());
		
		if (!is_null($this->descr->rows)) {
			$att['rows']=$this->descr->rows;
		}

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld->is_def()) {
				$val=$fld->value('plain');
			} else {
				$val=$fld->get_invalid();
			}
		}
		
		$js='<div><script>showtextbuttons(\''.$this->descr->name.'\');</script></div>';
		
		return $js.$this->html_tag('textarea',$att,false).$val.'</textarea>';
	}
}

class control_hidden extends control {
	public function html_input(i_field $fld=null) {
		$att=array('type'=>'hidden');
		$att['name']=$this->post_name();
		$att['id']=$att['name'];
		$att=array_merge($att,$this->tgattribs_a());

		if(is_null($fld)) {
			$val='';
		} else {
			if ($fld->is_def()) {
				$val=$fld->value('html');
			} else {
				$val=$fld->get_invalid();
			}
		}
		$att['value']=$val;
		
		return $this->html_tag('input',$att);
	}
}
?>