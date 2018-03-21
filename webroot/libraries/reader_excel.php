<?
require_once LIBRARIES_PATH.'3rdparty/PHPExcel/PHPExcel.php';

class chunkReadFilter implements PHPExcel_Reader_IReadFilter {
	private $_sheets = null;
	
	private $_cols = 0;
	private $_offset = 0;
	private $_num = 0;
	
	public function __construct($cols, $num=1) {
		if (is_null($num)) $num=65000;
		
		$this->_cols=$cols;
		$this->_num=$num;
	}
	
	public function SetSheet($name) {
		if (is_null($this->_sheets)) $this->_sheets=array();
 		$this->_sheets[]=$name;
	}
	
	public function SetOffset($off) {
		$this->_offset=$off;
	}
	
	public function GetOffset() {
		return $this->_offset;
	}

	public function Next() {
		$this->_offset+=$this->_num;
	}

	public function readCell($column, $row, $worksheetName = '') {
		if (!is_null($this->_sheets) && !in_array($worksheetName,$this->_sheets)) return false;
		
		if ($column>$this->_cols) return false;
		
		if ($row<($this->_offset+1)) return false;
		if ($row>($this->_offset+$this->_num)) return false;
		
		return true;
	}
}

class xl_reader {
	protected $fname;
	protected $rdr;
	protected $fltr;
	
	protected $sheet;
	protected $cols;
	protected $skip;
	protected $empchk;
	protected $empterm;

	protected $fields;
	
	protected $eof;
	protected $recorset;

	function __construct($fname,$fields) {
		$this->fname=$fname;
		
		$this->sheet=$fields['sheet'];
		$this->cols=$fields['cols'];
		$this->skip=isset($fields['skip'])?$fields['skip']:0;
		$this->empchk=isset($fields['empchk'])?$fields['empchk']:null;
		$this->empterm=isset($fields['empterm'])?$fields['empterm']:1;
		
		if (is_array($fields)) {
			$this->set_fields($fields['fields']);
		}
		
		$this->rdr = PHPExcel_IOFactory::createReader('Excel2007');
		$this->rdr->setReadDataOnly(true);
		$this->fltr = new chunkReadFilter($this->cols,2000);
		if (!is_null($this->sheet)) $this->fltr->SetSheet($this->sheet);
		$this->fltr->SetOffset($this->skip);

		$this->recordn=0;
	}

	function __get($key) {
		if ($key=='recordset') {
			return $this->recordset;
		} else {
			trigger_error('Trying to get an undefined property "'.$key.'"',E_USER_WARNING);
		}
	}

	function set_fields($fields) {
		$this->fields=array_values($fields);
	}

	function pos_to($off) {
		$this->fltr->SetOffset($off+$this->skip);
	}

	function pos_next() {
		if ($this->eof) return null;
		$this->fltr->Next();
		return $this->fltr->GetOffset()-$this->skip;
	}
	
	function pos_cur() {
		if ($this->eof) return null;
		return $this->fltr->GetOffset()-$this->skip;
	}

	function get_recordset() {
		$this->recordset=array();
		$n=$this->pos_cur();
		$i=0;
	
		$this->rdr->setReadFilter($this->fltr);
		$xls = $this->rdr->load($this->fname);
		
		//$sheets = $xls->getSheetNames();
		
		if (is_null($this->sheet)) {
			/*$sheets = $xls->getSheetNames();
			 
			$this->eof=false;
			$sh=null;
			 
			foreach ($sheets as $snum => $sname) {
				if ($sname==$this->sheet) {
					$sh=$xls->getSheet($snum);
				}
			}*/
			$sh=$xls->getSheet(0);
		} else {
			$sh=$xls->getSheetByName($this->sheet);
		}
		
		if (is_null($sh)) {
			$this->eof=true;
		} else {
			$sd = $sh->toArray(null,true,false,false);
			
			$beg=true;
			$ec=0;
			foreach ($sd as $row) {
				$emp=true;
				if (isset($this->empchk) && $this->empchk) {
					foreach ($this->empchk as $ti) {
						if (isset($row[$ti]) && !is_null($row[$ti]) && (trim($row[$ti])!='')) {
							$emp=false;
							break;
						}
					}
				} else {
					foreach ($row as $tc) {
						if (!is_null($tc) && (trim($tc)!='')) {
							$emp=false;
							break;
						}
					}
				}
				
				if ($beg) {
					if ($emp) continue;
					else $beg=false;
				} else {
					if ($emp) {
						$ec++;
						if ($ec>=$this->empterm) {
							$this->eof=true;
							break;
						} else {
							continue;
						}
					} else {
						$ec=0;
					}
				}
				
				$drow=$this->row_process($row);
				
				if (!$emp) {
					$this->recordset[($n+1)]=$drow;
					$i++;
				}
				
				$n++;
			}
			if ($i==0) $this->eof=true;
		}

		return $this->recordset;
	}
	
	function row_process(&$row) {
		$drow=array();
		foreach ($row as $cid=>$itm) {
			if (!(is_null($this->fields[$cid]) || (isset($this->fields[$cid]['skip']) && $this->fields[$cid]['skip']))) {
				$f=&$this->fields[$cid];
				
				if (isset($f['trim']) && $f['trim']) {
					$itm=trim($itm);
				}
				
				if (isset($f['int']) && $f['int']) {
					$itm=is_numeric($itm)?(float)$itm:null;
				}
				
				if (isset($f['real']) && $f['real']) {
					$itm=is_numeric($itm)?(float)$itm:null;
				}
				
				if (isset($f['csvs']) && $f['csvs']) {
					if ($itm=='') $itm=array();
					else $itm=explode($f['csvs'],$itm);
					if (!isset($f['csvt']) || $f['csvt']) foreach ($itm as &$s) $s=trim($s);
				}
				
				$drow[$f['name']]=$itm;
			}
		}
		return $drow;
	}
}

class xl_reader2 {
	protected $fname;
	protected $rdr;
	protected $sh;
	protected $fltr;
	
	protected $sheet;
	protected $cols;
	protected $skip;
	protected $empchk;
	protected $empterm;

	protected $fields;
	
	protected $eof;
	protected $recorset;

	function __construct($fname,$fields) {
		$this->fname=$fname;
		
		$this->sheet=$fields['sheet'];
		$this->cols=$fields['cols'];
		$this->skip=isset($fields['skip'])?$fields['skip']:0;
		$this->empchk=isset($fields['empchk'])?$fields['empchk']:null;
		$this->empterm=isset($fields['empterm'])?$fields['empterm']:1;
		
		if (is_array($fields)) {
			$this->set_fields($fields['fields']);
		}
		
		$this->rdr = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
		if (!is_null($fname)) {
			$this->rdr->open($fname);
			
			foreach ($this->rdr->getSheetIterator() as $sheet) {
				if (is_null($this->sheet) || ($this->sheet == $sheet->getName())) {
					$this->sh=$sheet;
					break;
				}
			}
		}
		
		//$this->fltr->SetOffset($this->skip);

		$this->recordn=0;
	}

	function set_fields($fields) {
		$this->fields=array_values($fields);
	}

	function read($callback) {
		foreach ($this->sh->getRowIterator() as $row) {
			$this->recordn++;
			$row=$this->row_process($row);
			$callback($row);
			//if ($this->recordn>=5000) break;
		}
	}
	
	function row_process(&$row) {
		$drow=array();
		foreach ($row as $cid=>$itm) {
			if (!(is_null($this->fields[$cid]) || (isset($this->fields[$cid]['skip']) && $this->fields[$cid]['skip']))) {
				$f=&$this->fields[$cid];
				
				if (isset($f['trim']) && $f['trim']) {
					$itm=trim($itm);
				}
				
				if (isset($f['int']) && $f['int']) {
					$itm=is_numeric($itm)?(float)$itm:null;
				}
				
				if (isset($f['real']) && $f['real']) {
					$itm=is_numeric($itm)?(float)$itm:null;
				}
				
				if (isset($f['csvs']) && $f['csvs']) {
					if ($itm=='') $itm=array();
					else $itm=explode($f['csvs'],$itm);
					if (!isset($f['csvt']) || $f['csvt']) foreach ($itm as &$s) $s=trim($s);
				}
				
				$drow[$f['name']]=$itm;
			}
		}
		return $drow;
	}
}
?>