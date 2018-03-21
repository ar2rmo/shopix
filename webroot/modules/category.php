<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';

class mod_category extends module_page {
	const mod_hidden=true;
	const mod_tpl='catalog';
	
	function body() {
		$cat=$this->data;
		
		$this->tpl->title=$cat->tx_d_title.($this->app->setts->inf_nameintitle?(' | '.$this->app->setts->inf_shopname):'');
		if ($cat->r_keywords) $this->tpl->keywords=$cat->nq_r_keywords;
		if ($cat->r_description) $this->tpl->description=$cat->nq_r_description;
		
		$this->add_sub_pc('box_nextlev',$cat);

		$this->tpl->cat=$cat;
		
		$bc=new col_categories;
		$bc->loadBreadCrumbs($cat,false);
		$this->tpl->breadcrumbs=$bc;
		
		$e404=false;
		$sort=null;
		$gets=array();
		$filtered=false;
		$fbrand=null;
		$fcrits=array();
		
		if ($this->src->get->is_unlisted(array('p','sort','brand','crit1','crit2'))) $e404=true;
		
		if ($this->src->get->num('brand')) {
			$fi=refbk::get('brands',$this->src->get->brand);
			if (is_null($fi)) $e404=true;
			else {
				$gets['brand']=$fi->id;
				$filtered=true;
				$fbrand=$gets['brand'];
			}
		} elseif ($this->src->get->def('brand') && $this->src->get->brand!='null') $e404=true;
		if ($this->src->get->num('crit1')) {
			$fi=refbk::get('criterias1',$this->src->get->crit1);
			if (is_null($fi)) $e404=true;
			else {
				$gets['crit1']=$fi->id;
				$filtered=true;
				$fcrits[1]=$gets['crit1'];
			}
		} elseif ($this->src->get->def('crit1') && $this->src->get->crit1!='null') $e404=true;
		if ($this->src->get->num('crit2')) {
			$fi=refbk::get('criterias2',$this->src->get->crit2);
			if (is_null($fi)) $e404=true;
			else {
				$gets['crit2']=$fi->id;
				$filtered=true;
				$fcrits[2]=$gets['crit2'];
			}
		} elseif ($this->src->get->def('crit2') && $this->src->get->crit2!='null') $e404=true;
		
		if ($this->src->get->def('sort')) {
			$gets['sort']=$this->src->get->sort;
			switch ($gets['sort']) {
				case 'price_asc':
					$sort='PRCA';
				break;
				case 'price_desc':
					$sort='PRCD';
				break;
				case 'name':
					$sort='NAME';
				break;
				case 'default':
					$sort=null;
				break;
				default:
					$e404=true;
					unset($gets['sort']);
			}
		}
		
		if ($e404) {
			$this->app->err404();
			$this->abort();
			return;
		}
		
		$pg=new paginator($this->src,$this->app->setts->num_onpage_prod);
		
		$this->tpl->firstpage=($pg->get_current()==1);
		
		$prods=new col_products();
		if ($filtered) {
			$prods->loadByFilter($pg,$sort,$cat,$fbrand,null,$fcrits,null);
		} else {
			$prods->loadByCatInc($pg,$sort,$cat);
		}
		
		$this->tpl->num=$pg->get_items();
		
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->gets=$gets;
		
		$this->tpl->prods=$prods;
		
		$this->tpl->fsort=new tplclsr(function () use ($gets,$sort) {	
			$gs=template::pgets(array_diff_key($gets,array('sort'=>false)));
			if ($gs=='') $gs='?';
			else $gs=$gs.'&';
			
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_fsort.xml');
			$dsc=$dscs['fsort'];
			$dsc->set_val('tgattribs',array('onchange'=>'window.location.href=\''.$gs.'sort=\'+this.value;'));
			$cnt=control::create($dsc);
			
			switch ($sort) {
				case 'NAME':
					$vsort='name';
				break;
				case 'PRCA':
					$vsort='price_asc';
				break;
				case 'PRCD':
					$vsort='price_desc';
				break;
				default:
					$vsort='default';
			}
			
			$fdsc=new descriptor;
			$fdsc->set_val('dtype','string');
			$fld=field::create($fdsc);
			$fld->assign($vsort,true);
			
			return $cnt->html_input($fld);
		});
		
		$this->tpl->fbrands=new tplclsr(function ($cat) use ($gets,$fbrand,$fcrits) {	
			$gs=template::pgets(array_diff_key($gets,array('brand'=>false)));
			if ($gs=='') $gs='?';
			else $gs=$gs.'&';
			
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_fbrands.xml');
			$dsc=$dscs['fbrands'];
			$dsc->set_val('tgattribs',array('onchange'=>'window.location.href=\''.$gs.'brand=\'+this.value;'));
			$dsc->set_val('proc_name','cmb_fbrand_incat_fc(:cat,:cr,1,0)');
			$dsc->set_val('proc_param',array('cat'=>$cat->id,'cr'=>implode(',',$fcrits)));
			$cnt=control::create($dsc);
			
			$fdsc=new descriptor;
			$fdsc->set_val('dtype','integer');
			$fdsc->set_val('null',true);
			$fld=field::create($fdsc);
			$fld->assign((int)$fbrand,true);
			
			return $cnt->html_input($fld);
		},$cat);
		
		$this->tpl->fcrit1=new tplclsr(function ($cat) use ($gets,$fbrand,$fcrits) {	
			$gs=template::pgets(array_diff_key($gets,array('crit1'=>false)));
			if ($gs=='') $gs='?';
			else $gs=$gs.'&';
			
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_fcrit1.xml');
			$dsc=$dscs['fcrit1'];
			$dsc->set_val('tgattribs',array('onchange'=>'window.location.href=\''.$gs.'crit1=\'+this.value;'));
			$dsc->set_val('proc_name','cmb_fcrit_incat("FRST",:cat,:brn,:cr,1,0)');
			$dsc->set_val('proc_param',array('cat'=>$cat->id,'brn'=>$fbrand,'cr'=>implode(',',$fcrits)));
			$cnt=control::create($dsc);
			
			$fdsc=new descriptor;
			$fdsc->set_val('dtype','integer');
			$fdsc->set_val('null',true);
			$fld=field::create($fdsc);
			if (isset($fcrits[1])) $fld->assign((int)$fcrits[1],true);
			
			return $cnt->html_input($fld);
		},$cat);
		
		$this->tpl->fcrit2=new tplclsr(function ($cat) use ($gets,$fbrand,$fcrits) {	
			$gs=template::pgets(array_diff_key($gets,array('crit1'=>false)));
			if ($gs=='') $gs='?';
			else $gs=$gs.'&';
			
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_fcrit2.xml');
			$dsc=$dscs['fcrit2'];
			$dsc->set_val('tgattribs',array('onchange'=>'window.location.href=\''.$gs.'crit2=\'+this.value;'));
			$dsc->set_val('proc_name','cmb_fcrit_incat("SCND",:cat,:brn,:cr,1,0)');
			$dsc->set_val('proc_param',array('cat'=>$cat->id,'brn'=>$fbrand,'cr'=>implode(',',$fcrits)));
			$cnt=control::create($dsc);
			
			$fdsc=new descriptor;
			$fdsc->set_val('dtype','integer');
			$fdsc->set_val('null',true);
			$fld=field::create($fdsc);
			if (isset($fcrits[2])) $fld->assign((int)$fcrits[2],true);
			
			return $cnt->html_input($fld);
		},$cat);
		
		$this->add_sub('box_filter_incat',array('cat'=>$cat));
	}
}
?>