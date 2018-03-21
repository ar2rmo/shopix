<?
require_once LIBRARIES_PATH.'3rdparty/PHPExcel/PHPExcel.php';
require_once LIBRARIES_PATH.'3rdparty/Spout/Autoloader/autoload.php';

class FieldsValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder {
	protected $fields;
	
	public function __construct ($fields) {
		$this->fields = $fields;
	}
	
	public function bindValue(PHPExcel_Cell $cell, $value = NULL) {
		$ci=PHPExcel_Cell::columnIndexFromString($cell->getColumn())-1;
		if (!is_null($value) && isset($this->fields[$ci])) {
			$c = &$this->fields[$ci];
			if (isset($c['type'])) {
				$cell->setValueExplicit(static::castToT($value,$c['type']), static::dataTypeForT($c['type']));
				return true;
			}
		}
		return parent::bindValue($cell, $value);
	}
	
	public static function castToT($v,$T) {
		switch ($T) {
			case 'B':
				return (bool)$v;
			default:
				return $v;
		}
	}
	
	public static function dataTypeForT($T) {
		switch ($T) {
			case 'I':
			case 'C':
				return PHPExcel_Cell_DataType::TYPE_NUMERIC;
			case 'S':
				return PHPExcel_Cell_DataType::TYPE_STRING;
			case 'B':
				return PHPExcel_Cell_DataType::TYPE_BOOL;
		}
	}
}

class xl_writer {
	protected $xl;
	
	protected $fname;
	
	protected $fields;
	
	protected $n;
	
	function __construct($fname, $fields) {
		$this->fname=$fname;
		
		$this->xl = new PHPExcel();
		
		$props=$this->xl->getProperties();
		if (isset($fields['title'])) $props->setTitle(isset($fields['title']));
		
		$this->n=isset($fields['skip'])?(int)$fields['skip']:0;
		
		$this->xl->setActiveSheetIndex(0);
		$this->xl->getActiveSheet()->setTitle(isset($fields['title'])?$fields['title']:'Data');
		
		if (is_array($fields)) {
			$this->set_fields($fields['fields']);
		}
	}
	
	function set_fields($fields) {
		$this->fields=array_values($fields);
		//PHPExcel_Cell::setValueBinder( new FieldsValueBinder($this->fields) );
	}
	
	public function write($row) {
		//$this->xl->getActiveSheet()->fromArray($row, null, 'A'+($this->n++));
		$this->n++;
		foreach ($this->fields as $c=>$f) {
			if (isset($row[$f['name']])) {
				$this->xl->getActiveSheet()->setCellValueByColumnAndRow($c,$this->n,$row[$f['name']]);
			}
		}
	}
	
	public function save() {
		$objWriter = PHPExcel_IOFactory::createWriter($this->xl, 'Excel5');
		$objWriter->save($this->fname);
	}
	
	public function clear() {
		//PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_DefaultValueBinder() );
	}
}

class xl_writer2 {
	protected $xl;
	
	protected $fname;
	
	protected $fields;
	
	protected $n;
	
	function __construct($fname, $fields) {
		$this->fname=$fname;
		
		$this->xl = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX);
		$this->xl->openToFile($this->fname);
		
		if (is_array($fields)) {
			$this->set_fields($fields['fields']);
		}
	}
	
	function set_fields($fields) {
		$this->fields=array_values($fields);
	}
	
	public function write_head(&$row) {
		$style = (new Box\Spout\Writer\Style\StyleBuilder())
           ->setFontBold()
           ->build();
		$this->xl->addRowWithStyle($row,$style);
	}
	
	public function write(&$row) {
		$this->xl->addRow($this->makerow($row));
	}
	
	protected function makerow(&$row) {
		$r2=array();
		foreach ($this->fields as $c=>$f) {
			if (isset($row[$f['name']])) {
				if (isset($f['type']))
					$r2[]=xl_writer2::castToT($row[$f['name']],$f['type']);
				else
					$r2[]=$row[$f['name']];
			} else {
				$r2[]=null;
			}
		}
		return $r2;
	}
	
	public static function castToT($v,$T) {
		if (is_null($v)) return null;
		switch ($T) {
			case 'I':
				return (int)$v;
			case 'C':
				return (float)$v;
			case 'S':
				return (string)$v;
			case 'B':
				return (bool)$v;
			default:
				return $v;
		}
	}
	
	public function save() {
		$this->xl->Close();
	}
	
	public function clear() {
		
	}
}
?>