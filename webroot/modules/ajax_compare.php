<?
// #CMP=

require_once CLASSES_PATH.'compare.php';

class mod_ajax_compare extends module {
	private $auth;
	
	function body() {
		switch ($this->src->uri->uri_2) {
			case 'add':
				$this->add();
			break;
			case 'del':
				$this->del();
			break;
			case 'info':
				$this->info();
			break;
		}
	}

	private function add() {
		compare::add_item($this->src->get->product);
	}
	
	private function del() {
		compare::del_item($this->src->get->product);
	}

	private function info() {
		$this->app->run('box_compare');
	}
	
}
?>