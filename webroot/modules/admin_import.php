<?
require_once CLASSES_PATH.'import_eupd.php';

class mod_admin_import extends module_admin {
	function body() {
		if (!(defined('IMPEXP_ENABLED') && IMPEXP_ENABLED)) {
			$this->tpl->content='Module is disabled';
			return;
		}
		
		$this->mnu->setcurrent('catalog');
		$this->mnu->setcurrentsub('import');
		
		if ($this->src->get->def('stop')) {
			//echo 'STOP!';
			Import::ClearStoredSession();
		}
		
		$iu = new import_eupd();
		if ($iu->IsStoredSession())
		{
			$iu->RestoreSession();
			if ($iu->IsStopped())
			{
				$this->tpl->sub='import_done';
				//var_dump($iu->GetSessionData());
				//echo 'Done!';
			}
			else
			{
				$this->tpl->sub='import_process';
				//var_dump($iu->GetSessionData());
				$this->tpl->before=$iu->GetSessionData();
				$iu->ExecuteStep();
				//var_dump($iu->GetLastStepTime());
				//var_dump($iu->GetSessionData());
				$this->tpl->after=$iu->GetSessionData();
				$iu->StoreSession();
				$this->Refresh();
			}
		}
		else
		{
			$this->tpl->sub='import_form';
			$fld_d=descriptors::xml_file(CLASSES_PATH.'descriptors/import.model.xml');
			$fld_d['file']->moveto='./data/import--'.date('Y-m-d--H-i-s').'.xlsx';
			$inf=new fieldset($fld_d);
			$frm_d=descriptors::xml_file(MODULES_PATH.'descriptors/form_import.xml');
			$frm=new cform($frm_d,$inf);
			$errs=null;
			if ($this->src->post->def('submt')) {
				if ($frm->validate_post($errs)) {
					$iu->SetFile($inf->file['path']);
					$iu->Reset();
					//var_dump($iu->GetSessionData());
					$iu->StoreSession();
					$this->Refresh();
				}
			}
			$this->tpl->frm=$frm->html_data();
		}
	}
	
	private function Refresh() {
		header('Refresh: 0');
	}
}
?>