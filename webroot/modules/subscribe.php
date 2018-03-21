<?
require_once CLASSES_PATH.'subscriber.model.php';

class mod_subscribe extends module_page {
	const mod_tpl='subscribe';
	
	function body() {
		$os=new subscriber();
		$os->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_subscribe.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_subscribe.xml'),$os);
		
		if ($this->src->post->def('sa')||($this->src->post->def('sa_x')&&$this->src->post->def('sa_y'))) {
			if ($frm->validate_post($errs)) {
				$os->ip=$_SERVER['REMOTE_ADDR'];
				$os->db_subscribe();
				$this->tpl->success=true;
			} else {
				$this->tpl->errs=$errs;
				$this->tpl->frm=$frm->html_data();
			}
		} elseif ($this->src->get->def('unsubscr')&&$this->src->get->def('code')) {
			$os->email=$this->src->get->unsubscr;
			$os->unsuc=$this->src->get->code;
			$os->db_unsubscribe();
			$this->tpl->unsuc=true;
		} else {
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>