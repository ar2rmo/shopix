<?
require_once LIBRARIES_PATH.'import.php';
require_once LIBRARIES_PATH.'writer_excel.php';
require_once LIBRARIES_PATH.'3rdparty/Spout/Autoloader/autoload.php';

class import_eupd extends ImportExcel {
	public static $xls_fields=array(
		'sheet'=>null,
		'title'=>null,
		'cols'=>21,
		'skip'=>1,
		'fields'=>array(
			array('name'=>'id',          'trim'=>true,                     'type'=>'I',   'caption'=>'ID'),
			array('name'=>'name',        'trim'=>true,                     'type'=>'S',   'caption'=>'Наименование товара'),
			array('name'=>'fullname',    'trim'=>true,                     'type'=>'S',   'caption'=>'Полное наименование'),
			array('name'=>'title',       'trim'=>true,                     'type'=>'S',   'caption'=>'Заголовок (для meta)'),
			array('name'=>'brand',                       'skip'=>true,     'type'=>'S',   'caption'=>'Бренд'),
			array('name'=>'catalog',                     'skip'=>true,     'type'=>'S',   'caption'=>'Раздел'),
			array('name'=>'code',        'trim'=>true,                     'type'=>'S',   'caption'=>'Код товара'),
			array('name'=>'barcode',     'trim'=>true,                     'type'=>'S',   'caption'=>'Штрихкод'),
			array('name'=>'measure',     'trim'=>true,                     'type'=>'S',   'caption'=>'Единица измерения'),
			array('name'=>'size',        'trim'=>true,                     'type'=>'S',   'caption'=>'Размер (Вес)'),
			array('name'=>'price',       'trim'=>true,                     'type'=>'C',   'caption'=>'Цена'),
			array('name'=>'oprice',      'trim'=>true,                     'type'=>'C',   'caption'=>'Старая цена'),
			array('name'=>'avail',                       'skip'=>true,     'type'=>'B',   'caption'=>'Наличие товара'),
			array('name'=>'fnew',        'trim'=>true,                     'type'=>'B',   'caption'=>'Новинка'),
			array('name'=>'fshow',       'trim'=>true,                     'type'=>'B',   'caption'=>'Показывать'),
			array('name'=>'frecomend',   'trim'=>true,                     'type'=>'B',   'caption'=>'Рекомендуем'),
			array('name'=>'fspecial',    'trim'=>true,                     'type'=>'B',   'caption'=>'Акция'),
			array('name'=>'keywords',    'trim'=>true,                     'type'=>'S',   'caption'=>'Ключевые слова (для meta)'),
			array('name'=>'description', 'trim'=>true,                     'type'=>'S',   'caption'=>'Описание (для meta)'),
			array('name'=>'tags',                        'skip'=>true,     'type'=>'S',   'caption'=>'Теги'),
			array('name'=>'criterias',                   'skip'=>true,     'type'=>'S',   'caption'=>'Критерии')
		)
	);
	
	public static $db_fields=array(
		'P_ID'=>         array('field'=>'id',          'check'=>2),
		'P_NAME'=>       array('field'=>'name',        'check'=>0),
		'P_FULLNAME'=>   array('field'=>'fullname',    'check'=>0),
		'P_TITLE'=>      array('field'=>'title',       'check'=>0),
		'P_CODE'=>       array('field'=>'code',        'check'=>0),
		'P_BARCODE'=>    array('field'=>'barcode',     'check'=>0),
		'P_MEASURE'=>    array('field'=>'measure',     'check'=>0),
		'P_SIZE'=>       array('field'=>'size',        'check'=>0),
		'P_PRICE'=>      array('field'=>'price',       'check'=>2),
		'P_PRICE_OLD'=>  array('field'=>'oprice',      'check'=>2),
		'IS_NEW'=>       array('field'=>'fnew',        'check'=>2),
		'P_SHOW'=>       array('field'=>'fshow',       'check'=>2),
		'IS_RECOMEND'=>  array('field'=>'frecomend',   'check'=>2),
		'IS_SPECIAL'=>   array('field'=>'fspecial',    'check'=>2),
		'P_KEYWORDS'=>   array('field'=>'keywords',    'check'=>0),
		'P_DESCR'=>      array('field'=>'description', 'check'=>0)
	);
	
	public function __construct() {
		parent::__construct(static::$xls_fields);
		$this->fset=new fieldset(descriptors::xml_file(CLASSES_PATH.'descriptors/import_eupd.model.xml'));
	}
	
	protected $fset;
	
	protected function Init() {
		DBP::Exec('call IMP_EUPD_INIT()');
	}
	
	protected function RowProcess($tpid, &$data) {
		$valid=true;
		$dbvs=array();
		foreach (static::$db_fields as $key=>$fld) {
			$val=$data[$fld['field']];
			if ($fld['check']==0) {
				$dbvs[$key]=DBP::getLink()->Quote($val,PDO::PARAM_STR);
			} else {
				$field=$this->fset->get_field($fld['field']);
				$field->assign($val,$fld['check']==1);
				if ($field->is_def()) {
					$dbvs[$key]=$field->value('db');
				} else {
					$valid=false;
				}
			}
		}
		if ($valid) {
			$inserts[]='('.implode(',',$dbvs).')';
		}
		
		if ($this->partial && $this->n%$this->num==0) {
			$flds=array();
			foreach (static::$db_fields as $key=>$fld) {
				$flds[]='`'.$key.'`';
			}
			
			DBP::Exec('insert into IMP_EUPD_PRODUCTS ('.implode(',',$flds).') values '.implode(',',$inserts));
		}
	}
	
	protected function StateHandler_READ_DONE() {
		DBP::Exec('call IMP_EUPD_MERGE()');
		return 'DONE';
	}
}

class export_eupd {
	protected $wr;
	
	public function __construct($File) {
		$this->wr = new xl_writer2($File, import_eupd::$xls_fields);
		
		$head = array();
		foreach (import_eupd::$xls_fields['fields'] as $fld) {
			$head[$fld['name']]=isset($fld['caption'])?$fld['caption']:$fld['name'];
		}
		$this->wr->write_head($head);
	}
	
	public function Export() {
		$st=microtime(true);
		DBP::Exec('call import_eupd_get',null,
			function ($row) {
				$this->wr->write($row);
			}
		);
		$et=microtime(true);
		
		$this->wr->save();
		$et2=microtime(true);
		
		//echo('Reading: '.($et-$st).'s.</br>');
		//echo('Writing: '.($et2-$et).'s.</br>');
		//echo('Total: '.($et2-$st).'s.</br>');
	}
	
	public function clear() {
		$this->wr->clear();
	}
}
?>