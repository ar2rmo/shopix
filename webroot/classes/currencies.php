<?
require_once CLASSES_PATH.'dcurrency.model.php';

class currencies {	
	static $obj=null;
	
	public static function get_obj() {
		if (is_null(static::$obj)) {
			static::$obj=new col_dcurrencies;
			static::$obj->Load();
		}
		return static::$obj;
	}
	
	public static function format($value) {
		static::get_obj()->SetBase($value);
		return static::get_obj()->OutputBaseValue();
	}
	
	public static function ht_format($value) {
		static::get_obj()->SetBase($value);
		return static::get_obj()->OutputBaseValueHt();
	}
	
	public static function convert($value) {
		static::get_obj()->SetBase($value);
		return static::get_obj()->OutputValues();
	}
	
	public static function ht_convert($value) {
		static::get_obj()->SetBase($value);
		return static::get_obj()->OutputValuesHt();
	}
}
?>