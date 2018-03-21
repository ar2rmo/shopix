<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'image.php';

require_once CLASSES_PATH.'product.model.php';

class category extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/category.model.xml');
	}
	
	public static function find_ext($path,&$ext=null) {
		$ext=null;
		if (file_exists(MEDIA_PATH.$path.'.jpg'))  $ext='.jpg';
		elseif (file_exists(MEDIA_PATH.$path.'.jpeg')) $ext='.jpeg';
		elseif (file_exists(MEDIA_PATH.$path.'.png'))  $ext='.png';
		elseif (file_exists(MEDIA_PATH.$path.'.gif'))  $ext='.gif';
		else return null;
		return $path.$ext;
	}
	
	private $_products;
	
	public function ovget_col_products() {
		return $this->_products;
	}
	
	public function set_col_products(col_products $prods=null) {
		$this->_products=$prods;
	}
	
	public function append_col_products(product $prod) {
		if (is_null($this->_products)) $this->_products=new col_products;
		$this->_products[]=$prod;
	}
	
	private $_children;
	
	public function ovget_col_children() {
		return $this->_children;
	}
	
	public function set_col_children(col_children $chldrn=null) {
		$this->_children=$chldrn;
	}
	
	public function append_col_children(category $cat) {
		if (is_null($this->_children)) $this->_children=new col_categories;
		$this->_children[]=$cat;
	}
	
	protected function ovget_ispict() {
		$ip=true;
		
		$fp='categories/big/'.$this->pict_uri;
		if (is_null($this->find_ext($fp))) $ip=false;
		
		return $ip;
	}
	
	protected function ovget_pict_uri_big() {
		$fp='categories/big/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_medium() {
		$fp='categories/medium/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_small() {
		$fp='categories/small/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	public static function getById($id){
		$obj=null;
		DBP::Exec("call get_cat_by_id(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new category($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function getByURI($uri,$vis=true){
		$obj=null;
		DBP::Exec("call get_cat_by_uri(:vis,:uri)",
			function ($q) use ($uri,$vis) {
				$q->bindParam(":vis",$vis,PDO::PARAM_BOOL);
				$q->bindParam(":uri",$uri,PDO::PARAM_STR);
			},
			function($arr) use (&$obj){
				$obj=new category($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function getRoot(){
		$obj=null;
		DBP::Exec("call get_cat_root()",
			null,
			function($arr) use (&$obj){
				$obj=new category($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_category(:acl_uid, :nid, :np, :uri_name, :fshow, :name, :fullname, :title,
			:keywords, :description, :css, :text_top, :text_bott, :specs_t)';
			
		$res=DBP::ExecSingleRow($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		$o->id=$res['aff_id'];
		
		if ($this->mpict_del) {
			$this->drop_pic($res['old_pict_uri']);
		} else {
			if ($res['old_pict_uri']<>$res['new_pict_uri']) {
				$this->move_pics($res['old_pict_uri'],$res['new_pict_uri']);
			}
			if (!is_null($this->mpict)) {
				$this->make_pics($this->mpict,$res['new_pict_uri']);
			}		
		}
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			//var_dump(array('uid'=>$uid,'id'=>$this->id));
			DBP::MExec('call delete_category(:uid,:id)',
				array('uid'=>$uid,'id'=>$this->id),
				array(
					function($arr){
						$this->drop_pic($arr['old_pict_uri']);
					},
					function($arr){
						product::drop_pic($arr['old_pict_uri_prod']);
					}
				)
			);
		}
		
		$this->id=0;
	}
	
	public function db_move_up($uid=null) {
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call move_node_up(:id)',array('id'=>$this->id));
		}
	}
	
	public function db_move_down($uid=null) {
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call move_node_down(:id)',array('id'=>$this->id));
		}
	}
	
	public static function make_pics($file,$puri) {
		$setts=setts::get_obj();
		
		$img=new image();
		$img->from_file($file['path']);
		if (($img->width > $setts->img_max_width) || ($img->height > $setts->img_max_height)) {
			$img->resize_max(array('width'=>$setts->img_max_width,'height'=>$setts->img_max_height));
		}
		$img->to_file(MEDIA_PATH.'categories/big/'.$puri);
		
		$img=new image();
		$img->from_file($file['path']);
		$img->lbox(array('width'=>$setts->img_middle_width_cat,'height'=>$setts->img_middle_height_cat));
		$img->to_file(MEDIA_PATH.'categories/medium/'.$puri);
		
		$img=new image();
		$img->from_file($file['path']);
		$img->lbox(array('width'=>$setts->img_small_width_cat,'height'=>$setts->img_small_height_cat));
		$img->to_file(MEDIA_PATH.'categories/small/'.$puri);
	}
	
	public static function move_pics($old,$new) {
		$moves=array();
		
		$fp='categories/big/'.$old;
		$file=category::find_ext($fp,$ext);
		if (!is_null($file)) {
			$moves[]=array('from'=>MEDIA_PATH.$file,'to'=>MEDIA_PATH.'categories/big/'.$new.$ext);
		}
		
		$fp='categories/medium/'.$old;
		$file=category::find_ext($fp,$ext);
		if (!is_null($file)) {
			$moves[]=array('from'=>MEDIA_PATH.$file,'to'=>MEDIA_PATH.'categories/medium/'.$new.$ext);
		}
		
		$fp='categories/small/'.$old;
		$file=category::find_ext($fp,$ext);
		if (!is_null($file)) {
			$moves[]=array('from'=>MEDIA_PATH.$file,'to'=>MEDIA_PATH.'categories/small/'.$new.$ext);
		}
		
		foreach ($moves as $move) {
			rename($move['from'],$move['to']);
		}
	}
	
	public static function drop_pic($puri) {
		$fp='categories/big/'.$puri;
		$file=category::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
		
		$fp='categories/medium/'.$puri;
		$file=category::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
		
		$fp='categories/small/'.$puri;
		$file=category::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
	}
}

class col_categories extends mcollection {
    public function GetRTree() {
		$root=new col_categories();
		$arr=array();
		foreach ($this as $n) {
			$arr[$n->id]=$n;
			if ($n->parent_id==0) {
				$root[]=$n;
			} elseif (isset($arr[$n->parent_id])) {
				$arr[$n->parent_id]->append_col_children($n);
			}
		}
		return $root;
	}
	
	public function loadAllTree($selected_uri=null, $visible=true, $maxlev=null){
		$items=&$this->_array;
		if (is_null($selected_uri)) {
			DBP::Exec('call get_cattree(:vis,:mlv)',array('vis'=>$visible,'mlv'=>$maxlev),
				function ($arr) use (&$items) {
					$items[]=new category($arr,'db');
				});
		} else {
			DBP::Exec('call get_cattree_sel(:vis,:uri,:mlv)',array('vis'=>$visible,'uri'=>$selected_uri,'mlv'=>$maxlev),
				function ($arr) use (&$items) {
					$items[]=new category($arr,'db');
				});
		}
		//$this->_isFilled=1;
	}
	public function loadAllTreeVProducts(paginator $pg=null,$visible=true,$baseonly=true){
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		$cats=array();
		
		if (is_null($pg)) {
			DBP::MExec('call get_cats_prods_all(:vis,:ipr,:ich,null,null)',
				array('vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld),
				array(
					function ($arr) use (&$items,&$cats) {
						$c=new category($arr,'db');
						$cats[$c->id]=$c;
						$items[]=$c;
					},
					function ($arr) use (&$cats) {
						$p=new product($arr,'db');
						$cats[$p->cid]->append_col_products($p);
					}
				));
		} else {
			DBP::MExec('call get_cats_prods_all(:vis,:ipr,:ich,:off,:onp)',
				array('vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items,&$cats) {
						$c=new category($arr,'db');
						$cats[$c->id]=$c;
						$items[]=$c;
					},
					function ($arr) use (&$cats) {
						$p=new product($arr,'db');
						$cats[$p->cid]->append_col_products($p);
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadBranchTree($uri,$maxlv=null,$visible=true){
		$items=&$this->_array;
		DBP::Exec('call get_cattree_sel_branch(:vis,:uri,:ml,null)',array('vis'=>$visible,'uri'=>$uri,'ml'=>$maxlv),
			function ($arr) use (&$items) {
				$items[]=new category($arr,'db');
			});
		//$this->_isFilled=1;
	}
	public function loadBranchTree2($uri,$maxlv=null,$minlv=null,$visible=true){
		$items=&$this->_array;
		DBP::Exec('call get_cattree_sel_branch(:vis,:uri,:mxl,:mnl)',array('vis'=>$visible,'uri'=>$uri,'mxl'=>$maxlv,'mnl'=>$minlv),
			function ($arr) use (&$items) {
				$items[]=new category($arr,'db');
			});
		//$this->_isFilled=1;
	}
	public function loadByParent($cat,$select=null,$visible=true){
		if (is_object($cat)) $pid=$cat->id;
		elseif (is_numeric($cat)) $pid=$cat;
		else return;
	
		$items=&$this->_array;
		DBP::Exec('call get_cats_by_parent(:pid,:vis,:sel)',array('pid'=>$pid,'vis'=>$visible,'sel'=>$select),
			function ($arr) use (&$items) {
				$items[]=new category($arr,'db');
			});
		//$this->_isFilled=1;
	}
	public function loadBreadCrumbs($cat,$inclusive){
		if (is_object($cat)) $pid=$cat->id;
		elseif (is_numeric($cat)) $pid=$cat;
		else return;
	
		$items=&$this->_array;
		if ($inclusive) {
			DBP::Exec('call get_breadcrumbs_inc_byid(:pid)',array('pid'=>$pid),
				function ($arr) use (&$items) {
					$items[]=new category($arr,'db');
				});
		} else {
			DBP::Exec('call get_breadcrumbs_exc_byid(:pid)',array('pid'=>$pid),
				function ($arr) use (&$items) {
					$items[]=new category($arr,'db');
				});
		}
		//$this->_isFilled=1;
	}
}


?>