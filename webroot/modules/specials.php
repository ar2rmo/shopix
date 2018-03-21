<?
require_once CLASSES_PATH.'article.model.php';

class mod_specials extends module {
	function body() {
		if ($this->src->uri->uri_2) {
			$this->app->run('read_item','specials');
		} else {
			$this->app->run('read_list','specials');
		}
	}
}
?>