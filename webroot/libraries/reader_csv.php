<?
class csv_reader {
	protected $fl;
	protected $fields;

	protected $record;
	protected $recordn;

	function __construct($fname,$fields=false) {
		if (is_array($fields)) {
			$this->set_fields($fields);
		}

		$this->fl=new SplFileObject('php://filter/read=convert.iconv.Windows-1251.UTF-8/resource='.$fname);
		$this->fl->setFlags(SplFileObject::READ_CSV);
		$this->fl->setCsvControl(';');

		$this->recordn=0;
	}

	function __get($key) {
		if ($key=='record') {
			return $this->record;
		} elseif ($key=='recordn') {
			return $this->recordn;
		} else {
			trigger_error('Trying to get an undefined property "'.$key.'"',E_USER_WARNING);
		}
	}

	function set_fields($fields) {
		$this->fields=array_values($fields);
	}

	static function naminize($data,$fields=null) {
		$data=array_values($data);

		if (is_array($fields)) {
			$ndata=array();

			foreach ($fields as $i=>$fld) {
				if ($fld===false) continue;
				if (isset($fld['name'])) $nm=$fld['name'];
				else $nm='field_'.$i;
				$ndata[$nm]=isset($data[$i])?$data[$i]:null;
			}

			return $ndata;
		} else {
			foreach ($data as $i=>$dt) {
				$ndata['field_'.$i]=$dt;
			}

			return $ndata;
		}
	}

	static function row_process(array &$data,$fields=null) {
		foreach ($data as $i=>&$itm) {
			if (isset($fields[$i])) {
				$f=&$fields[$i];
				
				if (isset($f['trim']) && $f['trim']) {
					$itm=trim($itm);
				}
				
				if (isset($f['int']) && $f['int']) {
					$itm=is_numeric($itm)?(int)$itm:null;
				}
				
				if (isset($f['real']) && $f['real']) {
					$itm=is_numeric($itm)?(float)$itm:null;
				}
				
				if (isset($f['csvs']) && $f['csvs']) {
					if ($itm=='') $itm=array();
					else $itm=explode($f['csvs'],$itm);
					if (!isset($f['csvt']) || $f['csvt']) foreach ($itm as &$s) $s=trim($s);
				}
			}
		}
	}

	protected function set_names($data) {
        return $this->naminize($data,$this->fields);
	}
	
	protected function make_process(&$data) {
        $this->row_process($data,$this->fields);
	}

	function pos_to($rec) {
		$this->fl->seek($rec);
	}

	function pos_next() {
		return $this->fl->key();
	}

	function get_record() {
		if ($this->fl->eof()) return false;

		$dt=$this->fl->fgetcsv();
		if (count($dt)==1 && is_null($dt[0])) return $this->get_record();
		$this->make_process($dt);
		$this->recordn++;
		$this->record=$this->set_names($dt);
		return $this->record;
	}
}
?>