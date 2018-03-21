<?
require_once CLASSES_PATH.'page.model.php';
require_once CLASSES_PATH.'feedback.model.php';

class mod_feedback extends module_page {
	const mod_tpl='feedback';
	
	function body() {
		$page=page::getByUri('feedback');
		$this->tpl->page=$page;
		
		if (!is_null($page) && !is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
		if (!is_null($page) && !is_null($page->d_description)) $this->tpl->description=$page->tx_d_description;
		
		$this->tpl->conf=false;
		
		$fbck=new feedback;
		$fbck->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_feedback.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_feedback.xml'),$fbck);
		
		if ($this->src->post->def('feedback')) {
        	if ($frm->validate_post($errs)) {
				$fbck->send();
				$this->tpl->conf=true;
			} else {
				$this->tpl->errs=$errs;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>