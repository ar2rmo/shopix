<?
require_once CLASSES_PATH.'article.model.php';

class mod_news extends module {
	function body() {
		if ($this->src->uri->uri_2) {
			$this->app->run('read_item','news');
		} else {
			$this->app->run('read_list','news');
		}
	}
}
?>