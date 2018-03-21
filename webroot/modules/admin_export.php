<?
require_once CLASSES_PATH.'import_eupd.php';

class mod_admin_export extends module_admin {
	function body() {
		if (!(defined('IMPEXP_ENABLED') && IMPEXP_ENABLED)) {
			$this->tpl->content='Module is disabled';
			return;
		}
		
		if ($this->src->get->def('getfile')) {
			$fif = new SplFileInfo('./data/'.$this->src->get->getfile);
			if ($fif->IsFile()) {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$fif->getBasename());
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: '.$fif->getSize());
				copy($fif->getPathname(),'php://output');
			}
		}
		
		if ($this->src->post->def('submt')) {
			$fname='./data/export--'.date('Y-m-d--H-i-s').'.xlsx';
			$w = new export_eupd($fname);
			$w->Export();
			$w->Clear();
		}
		
		$this->tpl->sub='export';
		
		$dr = new DirectoryIterator('./data');
		$rows=array();
		foreach ($dr as $fl) {
			if ($fl->isFile()) {
				$bname=$fl->getBasename();
				if (preg_match('/^export--([0-9]+)-([0-9]+)-([0-9]+)--([0-9]+)-([0-9]+)-([0-9]+)\.xlsx$/',$bname,$m)) {
					$dt=new DateTime();
					$dt->setDate($m[1],$m[2],$m[3]);
					$dt->setTime($m[4],$m[5],$m[6]);
					$rows[]=array('dt'=>$dt,'filename'=>$bname);
				}
			}
		}
		usort($rows, function ($i1, $i2) {
			if ($i1['dt'] == $i2['dt']) return 0;
			return ($i1['dt'] < $i2['dt']) ? -1 : 1;
		});
		$this->tpl->rows=$rows;
	}
}
?>