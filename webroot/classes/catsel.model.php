<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class catsel extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="string" dbname="uri" name="uri" />
				<field dtype="string" dbname="name" name="name">
					<flag name="html_inside" />
				</field>
			</fieldset>');
	}
}

?>