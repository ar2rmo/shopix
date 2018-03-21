<?
require_once CLASSES_PATH.'article.model.php';

class mod_admin_artedit extends module_admin {
	private $akind;
	private $auri;
	
	function body() {
		$this->mnu->setcurrent('articles');
		
		switch ($this->src->uri->uri_3) {
			case 'articles':
				$this->mnu->setcurrentsub('articles');
				$this->akind='ARTI';
				$this->auri='articles';
			break;
			case 'news':
				$this->mnu->setcurrentsub('news');
				$this->akind='NEWS';
				$this->auri='news';
			break;
			case 'specials':
				$this->mnu->setcurrentsub('specials');
				$this->akind='SPEC';
				$this->auri='specials';
			break;
			default:
				$this->app->err404();
				$this->abort();
		}
		$this->tpl->auri=$this->auri;
		
		if (is_null($this->src->uri->uri_4)) {
			if (!is_null($this->src->get->create)) {
				$this->show_form_create();
			} else {
				$this->show_list();
			}
		} else {
			$art=article::getByID($this->akind,$this->src->uri->uri_4,false);
			if (is_null($art)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$art->db_delete($this->akind);
						$this->app->http->relocate('/admin/artedit/'.$this->auri);
						$this->abort();
					} else {
						$this->show_list($art);
					}
				} else {
					$this->show_form_edit($art);
				}
			}
		}
	}
	
	private function show_list($del=null) {
		$pg=new paginator($this->src,50);
		
		$col=new col_article;
		$col->loadAll($pg,$this->akind,false);
		
		$this->tpl->sub='articles';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->del=$del;
	}
	
	private function show_form_edit(article $art) {
		$this->show_form($art);
	}
	
	private function show_form_create() {
		$itm=new article();
		$itm->set_value('fshow',true,true);
		$itm->set_value('date',new DateTime,true);
		$itm->set_value('id',0,true);
		$this->show_form($itm);
	}
	
	private function show_form(article $art) {
		$this->tpl->set('jquery',true);
		$this->tpl->set('ckeditor',true);
		$this->tpl->set('jscripts',array('jquery-ui.js','jquery.ui.datepicker-ru.js'));
		$this->tpl->set('styles',array('jquery-ui.css'));
		
		$ed=descriptors::xml_file(MODULES_PATH.'descriptors/messages_article.xml');
		$art->make_edescrs($ed);
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_article.xml'),$art);

		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$art->db_update($this->akind,$dupl);
				if ($dupl) {
					$this->tpl->sub='form_article';
					
					$this->tpl->errs=array('_comm'=>array('msg'=>array($ed['_comm']->dupl)));
					
					$this->tpl->art=$art;
					$this->tpl->frm=$frm->html_data();
				} else {
					$this->app->http->relocate('/admin/artedit/'.$this->auri.'/'.$art->id);
					$this->abort();
				}
			} else {
				$this->tpl->sub='form_article';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->art=$art;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_article';
			$this->tpl->art=$art;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>