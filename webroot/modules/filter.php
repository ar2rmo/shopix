<?
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'refbk.model.php';

class mod_filter extends module_page {
	const mod_tpl='filter';
	
	function body() {
		$gets=array();
		$fbrand=null;
		$ftag=null;
		$fcrits=array();
		$fprices=array();
		$fnew=false;
		$fspecial=false;
		$frecomend=false;
		
		$sort=null;
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
				default:
					unset($gets['sort']);
			}
		}
		
		$uris=$this->src->uri->get_all();
		unset($uris['uri_1']);
		$e404=false;
		if ($this->src->get->is_unlisted(array('p','sort','brand','tag','crit1','crit2','crit3','crit4','pricerange','new','special','recomend'))) $e404=true;
		//if (count($uris)==0) {
			if ($this->src->get->num('brand')&&($this->src->get->get('brand')!=-1)) {
				$fi=refbk::get('brands',$this->src->get->brand);
				if (is_null($fi)) $e404=true;
				else {
					$fbrand=$fi->id;
					$gets['brand']=$fi->id;
				}
			} elseif ($this->src->get->def('brand')&&($this->src->get->get('brand')!=-1)) $e404=true;
			if ($this->src->get->def('tag')&&($this->src->get->get('tag')!='')) {
				$ftag=$this->src->get('tag');
				$gets['tag']=$this->src->get('tag');
			}
			if ($this->src->get->num('crit1')&&($this->src->get->get('crit1')!=-1)) {
				$fi=refbk::get('criterias1',$this->src->get->crit1);
				if (is_null($fi)) $e404=true;
				else {
					$fcrits[]=$fi->id;
					$gets['crit1']=$fi->id;
				}
			} elseif ($this->src->get->def('crit1')&&($this->src->get->get('crit1')!=-1)) $e404=true;
			if ($this->src->get->num('crit2')&&($this->src->get->get('crit2')!=-1)) {
				$fi=refbk::get('criterias2',$this->src->get->crit2);
				if (is_null($fi)) $e404=true;
				else {
					$fcrits[]=$fi->id;
					$gets['crit2']=$fi->id;
				}
			} elseif ($this->src->get->def('crit2')&&($this->src->get->get('crit2')!=-1)) $e404=true;
			if ($this->src->get->num('crit3')&&($this->src->get->get('crit3')!=-1)) {
				$fi=refbk::get('criterias3',$this->src->get->crit3);
				if (is_null($fi)) $e404=true;
				else {
					$fcrits[]=$fi->id;
					$gets['crit3']=$fi->id;
				}
			} elseif ($this->src->get->def('crit3')&&($this->src->get->get('crit3')!=-1)) $e404=true;
			if ($this->src->get->num('crit4')&&($this->src->get->get('crit4')!=-1)) {
				$fi=refbk::get('criterias4',$this->src->get->crit4);
				if (is_null($fi)) $e404=true;
				else {
					$fcrits[]=$fi->id;
					$gets['crit4']=$fi->id;
				}
			} elseif ($this->src->get->def('crit4')&&($this->src->get->get('crit4')!=-1)) $e404=true;
			if ($this->src->get->def('pricerange')&&($this->src->get->get('pricerange')!='all')) {
				$gets['pricerange']=$this->src->get('pricerange');
				$pr=explode('-',$this->src->get('pricerange'));
				if (isset($pr[0])&&$pr[0]!=''&&is_numeric($pr[0])) {
					$fprices['low']=$pr[0];
				} else $e404=true;
				if (isset($pr[1])&&$pr[1]!=''&&is_numeric($pr[1])) {
					$fprices['hi']=$pr[1];
				} else $e404=true;
			}
			if ($this->src->get->def('new')) {
				if ($this->src->get->get('new')!='') $e404=true;
				$gets['new']=null;
				$fnew=true;
			}
			if ($this->src->get->def('special')) {
				if ($this->src->get->get('special')!='') $e404=true;
				$gets['special']=null;
				$fspecial=true;
			}
			if ($this->src->get->def('recomend')) {
				if ($this->src->get->get('recomend')!='') $e404=true;
				$gets['recomend']=null;
				$frecomend=true;
			}
		//} else {
			//$e404=false;
			foreach ($uris as $uri) {
				$up=explode('-',$uri,2);
				if (count($up)==3) {
					switch ($up[0]) {
						case 'pricerange':
							if (!is_numeric($up[1]) && !is_numeric($up[2])) {
								$e404=true;
							} else {
								if (isset($up[1])&&$up[1]!=''&&is_numeric($up[1])) {
									$fprices['low']=$up[1];
								}
								if (isset($up[2])&&$up[2]!=''&&is_numeric($up[2])) {
									$fprices['hi']=$up[2];
								}
							}
						break;
						default:
							$e404=true;
					}
				} elseif (count($up)==2) {
					switch ($up[0]) {
						case 'brand':
							if (is_numeric($up[1])) {
								$fbrand=(int)$up[1];
							} else {
								$e404=true;
							}
						break;
						case 'tag':
							$ftag=$up[1];
						break;
						case 'crit':
							if (is_numeric($up[1])) {
								$fcrits[]=(int)$up[1];
							} else {
								$e404=true;
							}
						break;
						default:
							$e404=true;
					}
				} elseif (count($up)==1) {
					switch ($up[0]) {
						case 'new':
							$fnew=true;
						break;
						case 'special':
							$fspecial=true;
						break;
						case 'recomend':
							$frecomend=true;
						break;
						default:
							$e404=true;
					}
				} else {
					$e404=true;
				}
			}
			
			if ($e404) {
				$this->app->err404();
				$this->abort();
				return;
			}
		//}
		
		$fos=array();
		if (!is_null($fbrand)) {
			$obrand=refbk::get('brands',$fbrand);
			if (!is_null($obrand)) $fos['brand']=$obrand;
		}
		$fos['crits']=array();
		foreach ($fcrits as $fcrit) {
			$ocrit=refbk::get('criteriasall',$fcrit);
			if (!is_null($ocrit)) $fos['crits'][]=$ocrit;
		}
		if (!is_null($ftag)) {
			$fos['tag']=$ftag;
		}
		$fos['prices']=$fprices;
		$fos['fnew']=$fnew;
		$fos['fspecial']=$fspecial;
		$fos['frecomend']=$frecomend;
		$this->tpl->fos=$fos;
		
		$pg=new paginator($this->src,$this->app->setts->num_onpage_prod);
		
		$prods=new col_products();
		$prods->loadByFilter($pg,$sort,null,$fbrand,$ftag,$fcrits,$fprices,$fnew,$frecomend,$fspecial);
		
		$this->tpl->num=$pg->get_items();
		
		$this->tpl->pages=$pg->get_parray();
		
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
		
		$this->tpl->fbrands=new tplclsr(function () use ($gets,$fbrand,$ftag,$fcrits,$fprices,$fnew,$frecomend,$fspecial) {	
			$gs=template::pgets(array_diff_key($gets,array('brand'=>false)));
			if ($gs=='') $gs='?';
			else $gs=$gs.'&';
			
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_fbrands.xml');
			$dsc=$dscs['fbrands'];
			$dsc->set_val('tgattribs',array('onchange'=>'window.location.href=\''.$gs.'brand=\'+this.value;'));
			$dsc->set_val('proc_name','cmb_fbrand_incat_fil(1,:new,:spc,:rec,null,:tag,:crt,:prl,:prh,1,0)');
			$dsc->set_val('proc_param',array(
				'tag'=>$ftag,
				'new'=>($fnew?1:0), 'spc'=>($fspecial?1:0), 'rec'=>($frecomend?1:0),
				'crt'=>is_null($fcrits)?null:implode(',',$fcrits),
				'prl'=>isset($fprices['low'])?$fprices['low']:null,'prh'=>isset($fprices['hi'])?$fprices['hi']:null
			));
			$cnt=control::create($dsc);
			
			$fdsc=new descriptor;
			$fdsc->set_val('dtype','integer');
			$fdsc->set_val('null',true);
			$fld=field::create($fdsc);
			$fld->assign((int)$fbrand,true);
			
			return $cnt->html_input($fld);
		});
	}
}
?>