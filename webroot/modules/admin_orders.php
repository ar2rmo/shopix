<?
require_once CLASSES_PATH.'order.model.php';
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'refbk.model.php';

class mod_admin_orders extends module_admin {
	function body() {
		$this->mnu->setcurrent('orders');
		
		//$this->make_menu();
		
		if ($this->src->uri->num('uri_3')) {
			$order=order::get_order_by_id((int)$this->src->uri->uri_3);
			if (is_null($order)) {
				$this->app->err404();
				$this->abort();
				return;				
			} else {
				switch ($this->src->uri->uri_4) {
					case 'items':
						if ($this->src->uri->num('uri_5')) {
							$item=order_item::get_order_item_by_id((int)$this->src->uri->uri_5);
							if (is_null($item)) {
								$this->app->err404();
								$this->abort();
								return;
							} else {
								if (is_null($this->src->uri->uri_6)) {
									$this->show_item_form($order,$item);
								} else {
									switch ($this->src->uri->uri_6) {
										case 'edit':
											$this->show_item_form($order,$item);
										break;
										case 'delete':
											if (!is_null($this->src->post->del_conf)) {
												$item->db_delete();
												$this->app->http->relocate('/admin/orders/'.$order->num.'/items');
												$this->abort();
											} else {
												$this->show_items_list($order,$item);
											}
										break;
										default:
											$this->app->err404();
											$this->abort();
											return;									
									}
								}
							}
						} elseif (is_null($this->src->uri->uri_5)) {
							$this->show_items_list($order);
						} else {
							switch ($this->src->uri->uri_5) {
								case 'add':
									$this->show_additem_form($order);
								break;
							}
						}
					break;
					case 'edit':
						$this->show_order_form($order);
					break;
					case 'delete':
						if (!is_null($this->src->post->del_conf)) {
							$order->db_delete();
							$this->app->http->relocate('/admin/orders');
							$this->abort();
						} else {
							$this->show_orders_list($order);
						}
					break;
					case 'resend':
						$order->send_customer();
						$this->app->http->relocate('/admin/orders');
						$this->abort();
					break;
					default:
						$this->app->err404();
						$this->abort();
						return;
				}
			}
		} elseif ($this->src->uri->uri_3=='filter') {
			if ($this->src->post->def('filter')) {
				$fstatus=null;
				$fdates=array();
				$this->proc_filter($fstatus,$fdates);
				
				$furis=array();
				if (!is_null($fstatus)) $furis[]='status-'.$fstatus;
				if (isset($fdates['from']) || isset($fdates['to'])) {
					$furis[]='range-'.((isset($fdates['from'])&&(!is_null($fdates['from'])))?$fdates['from']:'').'-'.((isset($fdates['to'])&&(!is_null($fdates['to'])))?$fdates['to']:'');
				}
				
				$this->app->http->relocate('/admin/orders/'.implode('/',$furis));
			} else {
				$this->app->http->relocate('/admin/orders');
			}
			$this->abort();
		} else {
			$fdates=array();
			$fstatus=null; 
			$fcustphone=null;
			$fcustemail=null;
			
			$sort=null;
			
			$uris=$this->src->uri->get_all();
			unset($uris['uri_1']);
			unset($uris['uri_2']);

			$e404=false;
			foreach ($uris as $uri) {
				$up=explode('-',$uri);
				if (count($up)==3) {
					switch ($up[0]) {
						case 'range':
							if (!is_numeric($up[1]) && !is_numeric($up[2])) {
								$e404=true;
							} else {
								if (isset($up[1])&&$up[1]!=''&&is_numeric($up[1])) {
									$fdates['from']=$up[1];
								}
								if (isset($up[2])&&$up[2]!=''&&is_numeric($up[2])) {
									$fdates['to']=$up[2];
								}
							}
						break;
						case 'order':
							switch ($up[1]) {
								case 'num':
									if ($up[2]=='a') $sort='NUMA';
									elseif ($up[2]=='d') $sort='NUMD';
									else $e404=true;
								break;
								case 'date':
									if ($up[2]=='a') $sort='DTTA';
									elseif ($up[2]=='d') $sort='DTTD';
									else $e404=true;
								break;
								case 'summ':
									if ($up[2]=='a') $sort='SUMA';
									elseif ($up[2]=='d') $sort='SUMD';
									else $e404=true;
								break;
								default:
									$e404=true;
							}
						break;
						default:
							$e404=true;
					}
				} elseif (count($up)==2) {
					switch ($up[0]) {
						case 'status':
							$fstatus=$up[1];
						break;
						case 'custphone':
							$fcustphone=$up[1];
						break;
						case 'custemail':
							$fcustemail=$up[1];
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
			
			$this->show_orders_list(null,$fstatus,$fcustphone,$fcustemail,$fdates,$sort);
		}
	}
	
	private function make_menu() {
		$mnu=new col_menu;
		$mnu->loadLev1($this->src->uri->uri_3,false);
		foreach ($mnu as $itm) {
			$sub=false;
			if ($itm->selected) {
				$mnu2=new col_menu;
				$mnu2->loadLev2($itm->uri_name,$this->src->uri->uri_4,false);
				foreach ($mnu2 as $itm2) {
					$aitm2=array(
						'capt'=>$itm2->ht_caption,
						'href'=>'/admin/pages/'.$itm->uri_name.'/'.$itm2->uri_name,
						'selected'=>$itm2->selected,
						'submenu'=>false
					);
					$sub[]=$aitm2;
				}
			}
			$aitm=array(
				'capt'=>$itm->ht_caption,
				'href'=>'/admin/pages/'.$itm->uri_name,
				'selected'=>$itm->selected,
				'submenu'=>$sub
			);
			$this->mnu->add_submenu_item($aitm);
		}
	}
	
	private function proc_filter(&$fstatus,&$fdates) {
		$dscs=descriptors::xml_str('
			<fieldset>
				<field dtype="integer" name="status"><flag name="null" /></field>
				<field dtype="datetime" name="from"><flag name="null" /><param name="format">d.m.Y</param></field>
				<field dtype="datetime" name="to"><flag name="null" /><param name="format">d.m.Y</param></field>
			</fieldset>');
		$filter=new fieldset($dscs);
		$filter->status=$fstatus;
		$filter->from=isset($fdates['from'])?$fdates['from']:null;
		$filter->to=isset($fdates['to'])?$fdates['to']:null;

		if ($this->src->post->def('filter')) {
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_order_filter.xml');
			$frm=new cform($dscs,$filter);
			
			$frm->validate_post();
			
			$fstatus=$filter->status;
			$fdates['from']=is_null($filter->from)?null:$filter->from->getTimestamp();
			$fdates['to']=is_null($filter->to)?null:$filter->to->getTimestamp();
		}
		
		return $filter;
	}
	
	private function show_orders_list(order $del=null,$fstatus=null,$fcustphone=null,$fcustemail=null,$fdates=array(),$sort=null) {
		$this->tpl->set('jscripts',array('jquery-1.7.2.min.js','jquery-ui.js','jquery.ui.datepicker-ru.js'));
		$this->tpl->set('styles',array('jquery-ui.css'));
		
		$filter=$this->proc_filter($fstatus,$fdates);
		$this->tpl->filter=$filter;
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_order_filter.xml');
		$frm=new cform($dscs,$filter);
		$this->tpl->frm=$frm->html_data();
		
		$pg=new paginator($this->src,50);
		
		$col=new col_orders;
		$col->load($pg,$fstatus,$fcustphone,$fcustemail,$fdates,$sort);
		
		$this->tpl->sub='orders';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->del=$del;
	}
	
	private function show_order_form(order $order) {
		$order->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_order_edit.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_order_edit.xml');
		$frm=new cform($dscs,$order);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$order->db_update();
				$this->app->http->relocate('/admin/orders');
				$this->abort();
			} else {
				$this->tpl->sub='form_order';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->order=$order;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_order';
			$this->tpl->order=$order;
			$this->tpl->frm=$frm->html_data();
		}
	}
	
	private function show_items_list(order $order, order_item $del=null) {
		$col=new col_order_items;
		$col->loadByOrder($order);
		
		$this->tpl->sub='orderitems';
		$this->tpl->collect=$col;
		$this->tpl->order=$order;
		$this->tpl->del=$del;
	}
	
	private function show_item_form(order $order, order_item $item) {
		$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_order_item_edit.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_order_item_edit.xml');
		$frm=new cform($dscs,$item);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$item->db_update();
				$this->app->http->relocate('/admin/orders/'.$order->num.'/items');
				$this->abort();
			} else {
				$this->tpl->sub='form_orderitem';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->order=$order;
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_orderitem';
			$this->tpl->order=$order;
			$this->tpl->item=$item;
			$this->tpl->frm=$frm->html_data();
		}
	}
	
	private function show_additem_form(order $order) {
		$cat=null;
		if ($this->src->get->num('cat')) $cat=category::getById($this->src->get->cat);
		
		$dsc=descriptors::xml_file(MODULES_PATH.'descriptors/form_catsel_id.xml');
		$dsc['catsel']->set_val('tgattribs',array('onchange'=>'window.location.href=\'/admin/orders/'.$order->num.'/items/add?cat=\'+this.value;'));
		$cnt=control::create($dsc['catsel']);
		
		if (is_null($cat)) {
			$this->tpl->catsel=$cnt->html_input();
		} else {
			$this->tpl->catsel=$cnt->html_input($cat->fld_id);
		}
		
		if (!is_null($cat)) {
			$item=new order_item;
			$item->o_num=$order->num;
			$item->qty=1;
			
			$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_order_item_add.xml'));
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_order_item_add.xml');
			$dscs['p_id']->set_val('proc_param',array(
				'nid'=>$cat->id
			));
			$frm=new cform($dscs,$item);
			
			if ($this->src->post->def('item_add')) {
				if ($frm->validate_post($errs)) {
					$item->db_add();
					$this->app->http->relocate('/admin/orders/'.$order->num.'/items');
					$this->abort();
				} else {
					$this->tpl->sub='form_orderadditem';
					
					$this->tpl->errs=$errs;
						
					$this->tpl->order=$order;
					$this->tpl->item=$item;
					$this->tpl->frm=$frm->html_data();
				}
			} else {
				$this->tpl->sub='form_orderadditem';
				$this->tpl->order=$order;
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
		} else {
			$this->tpl->sub='form_orderadditem';
			$this->tpl->order=$order;
		}
	}
}
?>