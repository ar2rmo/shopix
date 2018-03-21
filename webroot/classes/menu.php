<?
class menu {
	protected $struct,$descr;
	protected $plain;
	protected $current;

	function __construct($struct, $descr) {
		$this->struct=$struct;
		$this->descr=$descr;

        $this->rebuilt();
	}

	function rebuilt() {
		$this->plain=array();
		$this->recurse($this->struct);
	}

	private function recurse(&$node,$addr=array()) {
		foreach ($node as $key=>&$val) {
			$iaddr=$addr;
			$iaddr[]=$key;
			$path=implode('/',$iaddr);

			$data['addr']=$iaddr;
			$data['descr']=&$this->descr[$path];
			$data['submnu']=is_array($val);

			$this->plain[$path]=$data;

			if (is_array($val)) {
				$this->recurse($val,$iaddr);
			}
		}
	}

	private function check_addr($itm,$addr) {
		foreach ($itm['addr'] as $key=>$sitm) {
			if (!isset($addr[$key]) || ($addr[$key]<>$sitm)) return false;
		}

		return true;
	}

	private function check_itm($addr,$level,$par) {
		if ($level!=count($addr)) return false;

		foreach ($par as $key=>$itm) {
			if ($addr[$key]<>$itm) return false;
		}

		return true;
	}

	function set_current($addr) {
		$this->current=$addr;
	}

	function get_level($level,$addr=array()) {
		$data=array();
		foreach ($this->plain as $path=>$itm) {
			if ($this->check_itm($itm['addr'],$level,$addr)) {
				$data[$path]=$itm;
			}
		}
		$frst=true;
		foreach ($data as &$itm) {
			$itm['first']=false;
			$itm['last']=false;
			$itm['current']=false;
			if ($this->check_addr($itm,$this->current)) {
				$itm['current']=true;
			}
			if ($frst) {
				$itm['first']=true;
				$frst=false;
			}
		}
		$itm['last']=true;
		return $data;
	}
}
?>