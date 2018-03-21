<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

require_once CLASSES_PATH.'mailer.php';

class feedback extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/feedback.model.xml');
	}
	
	public function send() {
		mailer::feedback($this);
	}
}

?>