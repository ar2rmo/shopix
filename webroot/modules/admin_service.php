<?
class mod_admin_service extends module_admin {
	function body() {
		//$this->mnu->setcurrent('settings');
		//$this->mnu->setcurrentsub('settings');
		$this->tpl->sub='service';
		
		if ($this->src->post->def('wipe_pict')) {
			$this->wipe_pict();	
		}
		
	}
	
	private function wipe_pict() {
		$cat_puris=array();
		$prod_puris=array();
		
		$err=false;
		DBP::MExec('call get_picts_uris',null,
			array(
				function ($row) use (&$cat_puris) {
					$cat_puris[]=$row['CAT_PICT_URI'];
				},
				function ($row) use (&$prod_puris) {
					$prod_puris[]=$row['P_PICT_URI'];
				}
			),
			null,
			function ($e) use (&$err) {
				$err=true;
				return false;
			}
		);
		
		if (!$err) {
			$cat_paths=array(
				MEDIA_PATH.'categories/big',
				MEDIA_PATH.'categories/medium',
				MEDIA_PATH.'categories/small'
			);
			
			$prod_paths=array(
				MEDIA_PATH.'products/big',
				MEDIA_PATH.'products/medium',
				MEDIA_PATH.'products/small'
			);
			
			$fli=new SplFileObject(ROOT_PATH.'logs/wipe_pics.txt','w');
			
			$wiped=0;
			
			foreach ($cat_paths as $p) {
				$di=new DirectoryIterator($p);
				foreach ($di as $fi) {
					if($fi->isFile()) {
						if (!$this->IsFilenameMatchURIs($cat_puris,$fi->getBasename())) {
							$fli->fwrite($fi->getPathname()."\n");
							unlink($fi->getPathname());
							$wiped++;
						}
					}
				}
			}
			
			foreach ($prod_paths as $p) {
				$di=new DirectoryIterator($p);
				foreach ($di as $fi) {
					if($fi->isFile()) {
						if (!$this->IsFilenameMatchURIs($prod_puris,$fi->getBasename())) {
							$fli->fwrite($fi->getPathname()."\n");
							unlink($fi->getPathname());
							$wiped++;
						}
					}
				}
			}
			
			$this->tpl->sv_msg='PW';
			$this->tpl->sv_cnt=$wiped;
		}
	}
	
	private $exts=array('jpg','jpeg','gif','png');
	
	private function IsFilenameMatchURIs(&$uris, $bn) {
		if (preg_match('/^(.*?)(?:_[0-9]{3})?\.([^\.]+)/',$bn,$m)) {
			if (!in_array($m[2],$this->exts,true)) return false;
			return in_array($m[1],$uris,true);
		} else {
			return false;
		}
	}
}
?>