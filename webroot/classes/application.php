<?
require_once LIBRARIES_PATH.'application.php';
require_once CLASSES_PATH.'auth.php';
require_once CLASSES_PATH.'settings.php';
require_once CLASSES_PATH.'mailer.php';
require_once CLASSES_PATH.'currencies.php';
require_once CLASSES_PATH.'uribuilder.php';

class application extends application_base {
    protected function objinit() {
		parent::objinit();
		$this->objs['setts']=function(){return setts::get_obj();};
		$this->objs['urib_f']=function(){return new uribuilder_front();};
		$this->objs['urib_b']=function(){return new uribuilder_back();};
		$this->objs['mailer']=function(){return new mailer();};
		$this->objs['currencies']=function(){return new currencies();};
		
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
	}
	
	public function err404() {
		$this->run('err404');
	}
	
	public function relocate_to_canonical($host = null) {
		if (!is_null($host))
			parent::relocate_to_canonical($host);
		elseif (defined("CANONICAL_DOMAIN"))
			parent::relocate_to_canonical(CANONICAL_DOMAIN);
	}
}

abstract class module_sub extends module {
	const mod_hidden=true;
}

abstract class module_page extends module_tpl {
	const mod_tpl='main';
	
	protected function init() {
		$this->tpl->setts=$this->app->setts;
		$this->tpl->urib=$this->app->urib_f;
		
		$this->tpl->title=$this->app->setts->inf_nameintitle ? $this->app->setts->tx_inf_shopname : null;
		$this->tpl->keywords=$this->app->setts->nq_inf_keywords;
		$this->tpl->description=$this->app->setts->nq_inf_description;
		
		$this->tpl->baseurl=$this->app->src->uri->base;
	}

	protected function finite() {
		$this->add_sub('box_catalog');
		$this->add_sub('box_fullcount');
		$this->add_sub('box_brands');
		$this->add_sub('box_filter');
		$this->add_sub('box_menu');
		$this->add_sub('box_menu2');
		$this->add_sub('box_recomend');
		$this->add_sub('box_random');
		$this->add_sub('box_random2');
		$this->add_sub('box_whatsnew');
		$this->add_sub('box_articles');
		$this->add_sub('box_news');
		$this->add_sub('box_specials');
		$this->add_sub('box_cart');
		$this->add_sub('box_subscribe');
		$this->add_sub('box_tagcloud');
		$this->add_sub('box_compare'); // #CMP
		$this->add_sub('box_catalog1'); // #TMN
		$this->add_sub('box_catalog2'); // #TMN
		$this->add_sub('box_catalog_top');
	}
	
	protected function priv() {
		return true;
	}
}

require_once CLASSES_PATH.'admin_menu.php';

abstract class module_admin extends module_tpl {
	const mod_admin=true;
	const mod_hidden=true;
	
	const mod_tpl='admin/main';
	
	protected $auth_adm;
	protected $mnu;
	
	private $logtried;
	private $logout;
	
	protected function init() {
		$this->mnu=new admin_menu($this->tpl);
		
		$this->tpl->setts=$this->app->setts;
		$this->tpl->urib=$this->app->urib_b;
		
		$this->tpl->title='User Title';
		$this->tpl->keywords='';
		$this->tpl->description='';
		
		$this->tpl->baseurl=$this->app->src->uri->base;
	}

	protected function finite() {
		$this->tpl->set('box_user',new tplclsr(function() {return $this->app->drun('box_user');}));
		$this->tpl->set('box_usermenu',new tplclsr(function() {return $this->app->drun('box_usermenu');}));
		
		$this->mnu->apply();
	}
	
	protected function priv() {
		if ($this->get_param('noauth')) {
			return true;
		}
		
		$this->auth_adm=new auth_lp_adm;
		
		$this->logtried=false;
		if ($this->src->post->adm_login) {
			
			if ($this->src->post->adm_passwd) {
				$phash=md5($this->src->post->adm_passwd);
			} else {
		        $phash=false;
			}

			$this->logtried=true;

		    if ($phash) {
		    	$this->auth_adm->login($this->src->post->adm_login,$phash);
		    }
		}
		
		if (!$this->logtried && ($this->src->def('logout')||($this->src->uri->uri_2=='logout'))) {
			$this->auth_adm->logout();
			$this->logout=true;
		}
		
		$priv=$this->auth_adm->check_priv(auth::PL_ADMIN);
		if ($priv) $_SESSION['KCFINDER']=array('disabled'=>false);
		return $priv;
	}
	
	protected function auth_failed() {
		if ($this->logtried) {
			$act='bad';
		} elseif ($this->logout) {
			$act='out';
		} else {
			$act='inv';
		}
		
		$this->app->run('admin_login',$act);
	}
}

if (defined('WYSIWYG_DISABLED')&&WYSIWYG_DISABLED) {
	class control_wysiwyg extends control_mtext {
	}
} else {
	class control_wysiwyg extends control_ckeditor {
	}
}
?>