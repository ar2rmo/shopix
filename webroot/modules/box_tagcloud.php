<?
class mod_box_tagcloud extends module_sub {
	function body() {
		$tags=array();
		
		DBP::Exec('call tagcloud_get()',null,
			function ($row) use (&$tags) {
				$tags[]=array('text'=>$row['tag'],'weight'=>$row['count'],'link'=>'/filter/tag-'.$row['tag']);
			}
		);
		
		$tpl=new template('boxes/tagcloud');
		$tpl->urib=$this->app->urib_f;
		$tpl->wordlist=json_encode($tags,JSON_UNESCAPED_UNICODE);
		$tpl->output();
	}
}
?>