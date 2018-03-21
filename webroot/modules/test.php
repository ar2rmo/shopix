<?
require_once CLASSES_PATH.'spec_class.model.php';

class mod_test extends module {
	function body() {
		$col=new col_spec_classes();
		$col->loadByProduct(2);
		$frm=$col->get_form($fieldset);
		
		
		if ($this->src->post->def('ppp')) {
			if ($frm->validate_post($errs)) {
				$col->storeValues();
				echo '<p>Stored</p>';
			} else {
				echo '<p>Error!</p>';
				var_dump($errs);
			}
		}
		
		$htd=$frm->html_data();
		
		echo '<form method="POST">';
		foreach ($htd as $fld) {
			echo $fld['label'];
			echo $fld['input'];
			echo '<br/>';
		}
		echo '<input type="submit" name="ppp" value="Save" />';
		echo '</form>';
	}
}
?>