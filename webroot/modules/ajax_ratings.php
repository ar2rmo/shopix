<?
require_once CLASSES_PATH.'rating.model.php';

class mod_ajax_ratings extends module {
	function body() {
		header('Content-type: application/json; charset=utf-8');
		
		$prod_id = (int)$this->src->post->prodId;
		$rating = (int)$this->src->post->rating;
		
		$err=null;
		if (!is_null($prod_id) && !is_null($rating) && ($rating>=1) && ($rating<=5)) {
			$v = new Vote();
			$v->pid=$prod_id;
			$v->Rate=$rating;
			$v->IP=$_SERVER['REMOTE_ADDR'];
			if (!$v->dbAdd()) {
				$err="Вы уже не можете голосовать.";
			}
		} else {
			$err="Неверная оценка.";
		}
		
		if (is_null($err))
			$arr = array('ok' => 'ok');
		else
			$arr = array('ok' => $err);
		
		echo json_encode($arr);
	}
}
?>