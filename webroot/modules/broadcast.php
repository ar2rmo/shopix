<?
require_once CLASSES_PATH.'subscriber.model.php';
require_once CLASSES_PATH.'article.model.php';

class mod_broadcast extends module {
	const mod_cli=true;
	
	function body() {
		if ($this->data=='CLI') {
			$this->broadcast();
		} else {
			if ($this->src->post->def('doit')) {
				echo '<h3>Broadcasting</h3>'."\n";
				
				list($num_articles,$num_news,$num_specials,$num_sbs)=$this->broadcast();
				
				if ($num_articles+$num_news+$num_specials==0) {
					echo '<p>Nothing to broadcast</p>'."\n";
				} else {
					echo '<p>There are '.$num_articles.' articles, '.$num_news.' news and '.$num_specials.' specials to broadcast</p>'."\n";
					
					echo '<p>Sending to '.$num_sbs.' subscribers</p>'."\n";
				}
			} else {
			   echo '<h3>Mail Broadcast</h3>'."\n";
			   echo '<form method="POST"><input type="submit" name="doit" value="Broadcast"></form>';
			}
		}
	}
	
	private function broadcast() {
		$sbs=new col_subscribers;
		$sbs->loadBroadcast(100);
		$num_sbs=count($sbs);
		
		$articles=new col_article;
		$articles->loadBroadcast('ARTI',50);
		$num_articles=count($articles);
		
		$news=new col_article;
		$news->loadBroadcast('NEWS',50);
		$num_news=count($news);
		
		$specials=new col_article;
		$specials->loadBroadcast('SPEC',50);
		$num_specials=count($specials);
		
		if ($num_articles+$num_news+$num_specials>0) {
			foreach ($sbs as $sb) {
				$sb->broadcast($articles,$news,$specials);
			}
		}
		
		return array('num_articles'=>$num_articles,'num_news'=>$num_news,'num_specials'=>$num_specials,'num_sbs'=>$num_sbs);
	}
}
?>