<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class dcurrency extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/dcurrency.model.xml');
	}
	
	protected function ovget_formated() {
		return str_replace('%',$this->tx_value,$this->format);
	}
	
	protected function ovget_ht_formated() {
		return str_replace(array(' ','-'),array('&nbsp;','&#x2011;'),str_replace('%',$this->ht_value,$this->format));
	}
	
	public function SetBase($value) {
		$this->value=$this->ratio*$value;
	}
}

class col_dcurrencies extends mcollection {
    public function SetBase($value) {
		foreach ($this->_array as $fld) {
			$fld->SetBase($value);
		}
	}
	
	public function OutputBaseValue() {
		$val=null;
		foreach ($this->_array as $fld) {
			if ($fld->num!=0) continue;
			$val=$fld->formated;
			break;
		}
		return $val;
	}
	
	public function OutputBaseValueHt() {
		$val=null;
		foreach ($this->_array as $fld) {
			if ($fld->num!=0) continue;
			$val=$fld->ht_formated;
			break;
		}
		return $val;
	}
	
	public function OutputValues() {
		$arr=array();
		foreach ($this->_array as $fld) {
			$arr[]=$fld->formated;
		}
		return $arr;
	}
	
	public function OutputValuesHt() {
		$arr=array();
		foreach ($this->_array as $fld) {
			$arr[]=$fld->ht_formated;
		}
		return $arr;
	}
	
	public function Load(){
		$items=&$this->_array;
		
		DBP::Exec('call get_disp_currencies()',null,
			function ($arr) use (&$items) {
				$items[]=new dcurrency($arr,'db');
			});

		//$this->_isFilled=1;
	}
	
	/*public function LoadPrice($price){
		$items=&$this->_array;
		
		DBP::Exec('call get_disp_currencies(:pr)',null,
			function ($arr) use (&$items) {
				$items[]=new dcurrency($arr,'db');
			});

		//$this->_isFilled=1;
	}*/
}

?>