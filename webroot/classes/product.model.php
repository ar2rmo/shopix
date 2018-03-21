<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'image.php';

require_once CLASSES_PATH.'refbk.model.php';
require_once CLASSES_PATH.'product_pict.model.php';
require_once CLASSES_PATH.'product_inh.model.php';

require_once CLASSES_PATH.'spec_class.model.php';

require_once CLASSES_PATH.'rating.model.php'; // #PRT

class product extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/product.model.xml');
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call get_product_by_id(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new product($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function getByURI($uri,$vis=true,$baseonly=true){
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$obj=null;
		DBP::Exec("call get_product_by_uri(:vis,:ipr,:ich,:uri)",
			function ($q) use ($uri,$vis,$ispar,$ischld) {
				$q->bindParam(":vis",$vis,PDO::PARAM_BOOL);
				$q->bindParam(":ipr",$ispar,PDO::PARAM_BOOL);
				$q->bindParam(":ich",$ischld,PDO::PARAM_BOOL);
				$q->bindParam(":uri",$uri,PDO::PARAM_STR);
			},
			function($arr) use (&$obj){
				$obj=new product($arr,'db');
			}
		);
		return $obj;
	}
	
	private $_tags=null;
	
	protected function ovget_col_tags() {
		if (is_null($this->_tags) && !is_null($this->id) && ($this->id!=0)){
			$this->_tags=new col_refbk;
			$this->_tags->LoadProdTags($this->id);
		} 
		return $this->_tags;
	}
	
	private $_crits1=null;
	
	protected function ovget_col_crits1() {
		if (is_null($this->_crits1) && !is_null($this->id) && ($this->id!=0)){
			$this->_crits1=new col_refbk;
			$this->_crits1->LoadProdCrits($this->id,'FRST');
		} 
		return $this->_crits1;
	}
	
	private $_crits2=null;
	
	protected function ovget_col_crits2() {
		if (is_null($this->_crits2) && !is_null($this->id) && ($this->id!=0)){
			$this->_crits2=new col_refbk;
			$this->_crits2->LoadProdCrits($this->id,'SCND');
		} 
		return $this->_crits2;
	}
	
	private $_crits3=null;
	
	protected function ovget_col_crits3() {
		if (is_null($this->_crits3) && !is_null($this->id) && ($this->id!=0)){
			$this->_crits3=new col_refbk;
			$this->_crits3->LoadProdCrits($this->id,'THRD');
		} 
		return $this->_crits3;
	}
	
	private $_cat=null;
	
	protected function ovget_cat() {
		if (is_null($this->_cat) && !is_null($this->cid)){
			$this->_cat=category::getById($this->cid);
		} 
		return $this->_cat;
	}
	
	private $_pc=null;
	
	protected function ovget_price_currencies() {
		if (is_null($this->_pc)) {
			if ($this->price_salebase) $this->_pc=currencies::convert($this->price_salebase);
			else $this->_pc=array();
		}
		return $this->_pc;
	}
	
	private $_pcht=null;
	
	protected function ovget_ht_price_currencies() {
		if (is_null($this->_pcht)) {
			if ($this->price_salebase) $this->_pcht=currencies::ht_convert($this->price_salebase);
			else $this->_pcht=array();
		}
		return $this->_pcht;
	}
	
	protected function ovget_ht_price_min_currencies() {
		if (is_null($this->_pcht)) {
			if ($this->price_salebase) $this->_pcht=currencies::ht_convert($this->price_salebase_min);
			else $this->_pcht=array();
		}
		return $this->_pcht;
	}
	
	protected function ovget_ht_price_max_currencies() {
		if (is_null($this->_pcht)) {
			if ($this->price_salebase) $this->_pcht=currencies::ht_convert($this->price_salebase_max);
			else $this->_pcht=array();
		}
		return $this->_pcht;
	}
	
	protected function ovget_price_currency() {
		$val=currencies::format($this->price_salebase);
		if (is_null($val)) $val=$this->tx_price_salebase;
		return $val;
	}
	
	protected function ovget_ht_price_currency() {
		$val=currencies::ht_format($this->price_salebase);
		if (is_null($val)) $val=$this->ht_price_salebase;
		return $val;
	}
	
	protected function ovget_tx_full_price() {
		$htp=$this->price_currencies;
		switch (count($htp)) {
			case 0: return $this->tx_price_salebase;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].', '.$htp[2].')';
			default: return 'NP';
		}
	}
	
	protected function ovget_ht_full_price() {
		$htp=$this->ht_price_currencies;
		switch (count($htp)) {
			case 0: return $this->ht_price_salebase;
			case 1: return $htp[0];
			case 2: return $htp[0].' <span>('.$htp[1].')</span>';
			case 3: return $htp[0].' <span>('.$htp[1].',&nbsp;'.$htp[2].')</span>';
			default: return 'NP';
		}
	}
	
	protected function ovget_is_price_range() {
		return (!is_null($this->price_min) && !is_null($this->price_max));
	}
	
	protected function ovget_is_price_range_equal() {
		return (!is_null($this->price_min) && !is_null($this->price_max) && $this->price_min==$this->price_max);
	}
	
	protected function ovget_price_min_currency() {
		$val=currencies::ht_format($this->price_salebase_min);
		if (is_null($val)) $val=$this->tx_price_salebase_min;
		return $val;
	}
	
	protected function ovget_price_max_currency() {
		$val=currencies::ht_format($this->price_salebase_max);
		if (is_null($val)) $val=$this->tx_price_salebase_max;
		return $val;
	}
	
	protected function ovget_ht_price_min_currency() {
		$val=currencies::ht_format($this->price_salebase_min);
		if (is_null($val)) $val=$this->ht_price_salebase_min;
		return $val;
	}
	
	protected function ovget_ht_price_max_currency() {
		$val=currencies::ht_format($this->price_salebase_max);
		if (is_null($val)) $val=$this->ht_price_salebase_max;
		return $val;
	}
	
	protected function ovget_tx_full_price_min() {
		$htp=$this->price_min_currencies;
		switch (count($htp)) {
			case 0: return $this->tx_price_salebase_min;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].', '.$htp[2].')';
			default: return 'NP';
		}
	}
	
	protected function ovget_tx_full_price_max() {
		$htp=$this->price_max_currencies;
		switch (count($htp)) {
			case 0: return $this->tx_price_salebase_max;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].', '.$htp[2].')';
			default: return 'NP';
		}
	}
	
	protected function ovget_ht_full_price_min() {
		$htp=$this->ht_price_min_currencies;
		switch (count($htp)) {
			case 0: return $this->ht_price_salebase_min;
			case 1: return $htp[0];
			case 2: return $htp[0].' <span>('.$htp[1].')</span>';
			case 3: return $htp[0].' <span>('.$htp[1].',&nbsp;'.$htp[2].')</span>';
			default: return 'NP';
		}
	}
	
	protected function ovget_ht_full_price_max() {
		$htp=$this->ht_price_max_currencies;
		switch (count($htp)) {
			case 0: return $this->ht_price_salebase_max;
			case 1: return $htp[0];
			case 2: return $htp[0].' <span>('.$htp[1].')</span>';
			case 3: return $htp[0].' <span>('.$htp[1].',&nbsp;'.$htp[2].')</span>';
			default: return 'NP';
		}
	}
	
	protected function ovget_tx_price_range() {
		if ($this->is_price_range) {
			if ($this->is_price_range_equal) {
				return $this->price_min_currency;
			} else {
				return $this->price_min_currency.' - '.$this->price_max_currency;
			}
		} elseif ($this->price) {
			return $this->price_currency;
		} else {
			return 'NP';
		}
	}
	
	protected function ovget_ht_price_range() {
		if ($this->is_price_range) {
			if ($this->is_price_range_equal) {
				return $this->ht_price_min_currency;
			} else {
				return $this->ht_price_min_currency.' &mdash; '.$this->ht_price_max_currency;
			}
		} elseif ($this->price) {
			return $this->ht_price_currency;
		} else {
			return 'NP';
		}
	}
	
	protected function ovget_oprice_currency() {
		$val=currencies::format($this->price_salebase_old);
		if (is_null($val)) $val=$this->tx_price_salebase_old;
		return $val;
	}
	
	protected function ovget_ht_oprice_currency() {
		$val=currencies::ht_format($this->price_salebase_old);
		if (is_null($val)) $val=$this->ht_price_salebase_old;
		return $val;
	}
	
	protected function ovget_ud_price_currency() {
		$val=currencies::format($this->price_salebase_old);
		if (is_null($val)) $val=$this->tx_price_salebase_old;
		return $val;
	}
	
	protected function ovget_ht_ud_price_currency() {
		$val=currencies::ht_format($this->price_salebase_old);
		if (is_null($val)) $val=$this->ht_price_salebase_old;
		return $val;
	}
	
	private $_picts=null;
	
	protected function ovget_col_picts() {
		if (is_null($this->_picts) && !is_null($this->id) && ($this->id!=0)){
			$this->_picts=new col_product_pics;
			$this->_picts->loadByProduct($this);
		} 
		return $this->_picts;
	}
	
	public static function find_ext($path,&$ext=null) {
		$ext=null;
		if (file_exists(MEDIA_PATH.$path.'.jpg'))  $ext='.jpg';
		elseif (file_exists(MEDIA_PATH.$path.'.jpeg')) $ext='.jpeg';
		elseif (file_exists(MEDIA_PATH.$path.'.png'))  $ext='.png';
		elseif (file_exists(MEDIA_PATH.$path.'.gif'))  $ext='.gif';
		else return null;
		$st=stat(MEDIA_PATH.$path.$ext);
		$h=md5($st['size'].'-'.$st['mtime']);
		return $path.$ext.'?'.$h;
	}
	
	protected function ovget_ispict() {
		$ip=true;
		
		$fp='products/big/'.$this->pict_uri;
		if (is_null($this->find_ext($fp))) $ip=false;
		
		return $ip;
	}
	
	protected function ovget_pict_uri_big() {
		$fp='products/big/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_medium() {
		$fp='products/medium/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_small() {
		$fp='products/small/'.$this->pict_uri;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	private $_specs_values=null;
	
	protected function ovget_specs_values() {
		if (is_null($this->_specs_values) && !is_null($this->id)){
			$col=new col_spec_classes();
			$col->loadByProductVal($this->id,true,true);
			$this->_specs_values=$col;
		}
		return $this->_specs_values;
	}
	
	private $_specs_values_all=null;
	
	protected function ovget_specs_values_all() {
		if (is_null($this->_specs_values_all) && !is_null($this->id)){
			$col=new col_spec_classes();
			$col->loadByProductVal($this->id,false,true);
			$this->_specs_values_all=$col;
		}
		return $this->_specs_values_all;
	}
	
	protected function onchange_id() {
		$this->_crits1=null;
		$this->_crits2=null;
		$this->_cat=null;
		$this->_tags=null;
		$this->_picts=null;
	}
	
	protected function onchange_cid() {
		$this->_cat=null;
	}
	
	protected function onchange_price() {
		$this->_pc=null;
		$this->_pcht=null;
	}
	
	private $_bas=null;
	
	protected function ovget_base() {
		if (!$this->inherited) return null;
		if (is_null($this->_bas) && !is_null($this->id)){
			$this->_bas=product::getById($this->parent_id);
		} 
		return $this->_bas;
	}
	
	protected $cp_uri=null;
	
	public function set_clone_picts($pict_uri) {
		$this->cp_uri=$pict_uri;
	}
	
	private $_vars=null;
	
	protected function ovget_variants() {
		if ($this->inherited) return null;
		if (is_null($this->_vars) && !is_null($this->id)){
			$col=new col_products;
			$col->loadVariants($this,false);
			$this->_vars=$col;
		} 
		return $this->_vars;
	}
	
	private $_vars_cs=null;
	
	protected function ovget_variants_spec_classes() {
		if (is_null($this->_vars_cs)) {
			$vars=$this->variants;
			if (is_null($vars)) return null;
			$col_cs=new col_spec_classes();
			$col_cs->loadByProducts($vars);
			$this->_vars_cs=$col_cs;
		}
		return $this->_vars_cs;
	}
	
	private $_varsv=null;
	
	protected function ovget_variants_vis() {
		if ($this->inherited) return null;
		if (is_null($this->_varsv) && !is_null($this->id)){
			$col=new col_products;
			$col->loadVariants($this,true);
			$this->_varsv=$col;
		} 
		return $this->_varsv;
	}
	
	private $_varsv_cs=null;
	
	protected function ovget_variants_vis_spec_classes() {
		if (is_null($this->_varsv_cs)) {
			$vars=$this->variants_vis;
			if (is_null($vars)) return null;
			$col_cs=new col_spec_classes();
			$col_cs->loadByProducts($vars);
			$this->_varsv_cs=$col_cs;
		}
		return $this->_varsv_cs;
	}
	
	private $_inh=null;
	
	protected function ovget_inheritance() {
		if (!$this->inherited) return null;
		if (is_null($this->_inh) && !is_null($this->id)){
			$this->_inh=product_inh::getById($this->id);
			if (is_null($this->_inh)) $this->_inh=new product_inh(array('pid'=>$this->id,'plain'));
		} 
		return $this->_inh;
	}
	
	private $_par=null;
	
	protected function ovget_parent() {
		if (!$this->inherited) return null;
		if (is_null($this->_par) && !is_null($this->id)){
			$this->_par=product::getById($this->parent_id);
		} 
		return $this->_par;
	}
	
	// #PRT [
	private $_rt=null;

	protected function ovget_Rating() {
		if (is_null($this->_rt) && !is_null($this->id)){
			$this->_rt=Rating::getByProduct($this);
		} 
		return $this->_rt;
	}
	// ] #PRT
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		if (!is_null($this->inheritance)) {
			$this->inheritance->db_update();
		}
		$qry='call update_product(:acl_uid, :pid, :nid, :uri_name, :fshow, :name, :fullname, :title, :variant,
			:keywords, :description, :css, :code, :barcode, :brand_id, :measure, :size, :descr_short,
			:descr_full, :descr_tech, :price_type, :price, :oprice, :avail_id, :avail_num, :fnew, :frecomend,
			:fspecial, :forsale, :tags, :crits1, :crits2, :crits3, :specs_inh,
			:extra1, :extra2, :extra3, :extra4, :extra5)';
			
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
		
		if (!is_null($this->cp_uri)) {
			$this->copy_pics($this->cp_uri,$res['new_pict_uri']);
		}
		
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
	
	public function db_update_part($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if ($this->id==0) return;
		
		$o=&$this;
		
		$qry='call update_product_part(:acl_uid, :pid, :price, :fshow, :fnew, :frecomend, :fspecial)';
			
		DBP::Exec($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				
				$o->fld_id->pdo_bind($q);
				
				$o->fld_price->pdo_bind($q);
				$o->fld_fshow->pdo_bind($q);
				$o->fld_fnew->pdo_bind($q);
				$o->fld_frecomend->pdo_bind($q);
				$o->fld_fspecial->pdo_bind($q);
			}
		);
	}
	
	public static function db_recalc() {
		$qry='call node_tree_recalc_counters';
		DBP::Exec($qry);
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			//var_dump(array('uid'=>$uid,'id'=>$this->id));
			DBP::Exec('call delete_product(:uid,:id)',
				array('uid'=>$uid,'id'=>$this->id),
				function($arr){
					$this->drop_pic($arr['old_pict_uri']);
				}
			);
		}
		
		$this->id=0;
	}
	
	public static function make_pics($file,$puri) {
		$setts=setts::get_obj();
		
		$img=new image();
		$img->from_file($file['path']);
		if (($img->width > $setts->img_max_width) || ($img->height > $setts->img_max_height)) {
			$img->resize_max(array('width'=>$setts->img_max_width,'height'=>$setts->img_max_height));
		}
		$img->to_file(MEDIA_PATH.'products/big/'.$puri);
		
		$img=new image();
		$img->from_file($file['path']);
		$img->lbox(array('width'=>$setts->img_middle_width,'height'=>$setts->img_middle_height));
		$img->to_file(MEDIA_PATH.'products/medium/'.$puri);
		
		$img=new image();
		$img->from_file($file['path']);
		$img->lbox(array('width'=>$setts->img_small_width,'height'=>$setts->img_small_height));
		$img->to_file(MEDIA_PATH.'products/small/'.$puri);
	}
	
	/*public static function move_pics($old,$new) {
		$oldn=mb_strlen($old);
		
		$dirs = array(
			MEDIA_PATH.'products/big',
			MEDIA_PATH.'products/medium',
			MEDIA_PATH.'products/small'
		);
		$moves=array();
		foreach ($dirs as $dir) {
		$iterator = new DirectoryIterator($dir);
			foreach ($iterator as $fileinfo) {
				if ($fileinfo->isFile()) {
					$file=$fileinfo->getFilename();
					if (substr($file,0,$oldn)==$old) {
						$sfx=substr($file,$oldn);
						if (preg_match('/^(?:_[0-9]{1,3})?\.([a-z]{3,4})$/i',$sfx)) {
							$moves[] = array(
								'from'=>$dir.'/'.$file,
								'to'=>$dir.'/'.$new.$sfx
							);
						}
					}
				}
			}
		}
		
		foreach ($moves as $move) {
			rename($move['from'],$move['to']);
		}
	}*/
	
	public static function move_pics($old,$new) {
		for ($i=0;$i<=PROD_PICT_NUM;$i++) {
			if ($i==0) $suff='';
			else $suff='_'.str_pad($i,3,'0',STR_PAD_LEFT);
			
			$fp='products/big/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				rename(MEDIA_PATH.$file,MEDIA_PATH.'products/big/'.$new.$suff.$ext);
			}
			
			$fp='products/medium/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				rename(MEDIA_PATH.$file,MEDIA_PATH.'products/medium/'.$new.$suff.$ext);
			}
			
			$fp='products/small/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				rename(MEDIA_PATH.$file,MEDIA_PATH.'products/small/'.$new.$suff.$ext);
			}
		}
	}
	
	public static function copy_pics($old,$new) {
		for ($i=0;$i<=PROD_PICT_NUM;$i++) {
			if ($i==0) $suff='';
			else $suff='_'.str_pad($i,3,'0',STR_PAD_LEFT);
			
			$fp='products/big/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				copy(MEDIA_PATH.$file,MEDIA_PATH.'products/big/'.$new.$suff.$ext);
			}
			
			$fp='products/medium/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				copy(MEDIA_PATH.$file,MEDIA_PATH.'products/medium/'.$new.$suff.$ext);
			}
			
			$fp='products/small/'.$old.$suff;
			$file=product::find_ext($fp,$ext);
			if (!is_null($file)) {
				copy(MEDIA_PATH.$file,MEDIA_PATH.'products/small/'.$new.$suff.$ext);
			}
		}
	}
	
	public static function drop_pic($puri) {
		$fp='products/big/'.$puri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
		
		$fp='products/medium/'.$puri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
		
		$fp='products/small/'.$puri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			unlink(MEDIA_PATH.$file);
		}
	}
	
	public static function db_reorder(array $order) {
		$ord=implode(',',$order);
		DBP::Exec('call products_reorder(:ord)',array('ord'=>$ord));
	}
	
	public static function db_reorder_vars(array $order) {
		$ord=implode(',',$order);
		DBP::Exec('call products_chld_reorder(:ord)',array('ord'=>$ord));
	}
	
	// #XPD [
	public function GetLinkedSL(paginator $pg=null,$kind,$visible=true,$baseonly=true){
		$col = new col_products;
		$col->loadByLinksMS($pg,$kind,$this,$visible,$baseonly);
		return $col;
	}
	
	public function GetLinkedSLRnd($num,$kind,$visible=true,$baseonly=true){
		$col = new col_products;
		$col->loadByLinksMSRnd($num,$kind,$this,$visible,$baseonly);
		return $col;
	}
	
	public function GetLinkedX(paginator $pg=null,$kind,$visible=true,$baseonly=true){
		$col = new col_products;
		$col->loadByLinksX($pg,$kind,$this,$visible,$baseonly);
		return $col;
	}
	
	public function GetLinkedXRnd($num,$kind,$visible=true,$baseonly=true){
		$col = new col_products;
		$col->loadByLinksXRnd($num,$kind,$this,$visible,$baseonly);
		return $col;
	}
	
	// ] #XPD
}

class col_products extends mcollection {
    public function loadByCat(paginator $pg=null,$sort=null,$cat,$visible=true,$baseonly=true){
		if (is_object($cat)) $cid=$cat->id;
		elseif (is_numeric($cat)) $cid=$cat;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_by_cat(:cid,:vis,:ipr,:ich,:null,null,:sort)',
				array('cid'=>$cid,'vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld,'sort'=>$sort),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_by_cat(:cid,:vis,:ipr,:ich,:off,:onp,:sort)',
				array('cid'=>$cid,'vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage(),'sort'=>$sort),
				array(
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		//$this->_isFilled=1;
	}
	public function loadByCatInc(paginator $pg=null,$sort=null,$cat,$visible=true,$baseonly=true){
		if (is_object($cat)) $cid=$cat->id;
		elseif (is_numeric($cat)) $cid=$cat;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_by_cat_inc(:cid,:vis,:ipr,:ich,null,null,:sort)',
				array('cid'=>$cid,'vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld,'sort'=>$sort,'sort'=>$sort),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_by_cat_inc(:cid,:vis,:ipr,:ich,:off,:onp,:sort)',
				array('cid'=>$cid,'vis'=>$visible,'ipr'=>$ispar,'ich'=>$ischld,'sort'=>$sort,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage(),'sort'=>$sort),
				array(
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadByFilter(paginator $pg=null,$sort=null,$fcat,$fbrand,$ftag,$fcrits,$fprices,$fnew=false,$frecomend=false,$fspecial=false,$visible=true,$baseonly=true,$catinc=true){
		if (!is_null($fcat)) {
			if (is_object($fcat)) $fcid=$fcat->id;
			elseif (is_numeric($fcat)) $fcid=$fcat;
			else return;
		} else {
			$fcid=null;
		}
		
		if (is_null($fprices)) $fprices=array();
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		if (is_null($pg)) {
			DBP::Exec('call get_products_fil(:vis,:new,:spc,:rec,:cat,:inc,:brn,:tag,:crt,:prl,:prh,:ipr,:ich,null,null,:sort)',
				array('vis'=>$visible,'cat'=>$fcid,'inc'=>$catinc,'brn'=>$fbrand,'tag'=>$ftag,
				      'new'=>($fnew?1:0), 'spc'=>($fspecial?1:0), 'rec'=>($frecomend?1:0),
					  'crt'=>is_null($fcrits)?null:implode(',',$fcrits),
				      'prl'=>isset($fprices['low'])?$fprices['low']:null,'prh'=>isset($fprices['hi'])?$fprices['hi']:null,
					  'ipr'=>$ispar,'ich'=>$ischld,
					  'sort'=>$sort),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_fil(:vis,:new,:spc,:rec,:cat,:inc,:brn,:tag,:crt,:prl,:prh,:ipr,:ich,:off,:onp,:sort)',
				array('vis'=>$visible,'cat'=>$fcid,'inc'=>$catinc,'brn'=>$fbrand,'tag'=>$ftag,
				      'new'=>($fnew?1:0), 'spc'=>($fspecial?1:0), 'rec'=>($frecomend?1:0),
				      'crt'=>is_null($fcrits)?null:implode(',',$fcrits),
				      'prl'=>isset($fprices['low'])?$fprices['low']:null,'prh'=>isset($fprices['hi'])?$fprices['hi']:null,
					  'ipr'=>$ispar,'ich'=>$ischld,
					  'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage(),'sort'=>$sort),
				array (
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadHidden(paginator $pg=null,$sort=null,$baseonly=true){
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_hidden(:ipr,:ich,null,null,:sort)',
				array('ipr'=>$ispar,'ich'=>$ischld,'sort'=>$sort),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_hidden(:ipr,:ich,:off,:onp,:sort)',
				array('ipr'=>$ispar,'ich'=>$ischld,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage(),'sort'=>$sort),
				array (
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadBySearch(paginator $pg=null,$sa,$baseonly=true){
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_by_search(:ipr,:ich,:sa,null,null)',
				array('ipr'=>$ispar,'ich'=>$ischld,'sa'=>$sa),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_by_search(:ipr,:ich,:sa,:off,:onp)',
				array('ipr'=>$ispar,'ich'=>$ischld,'sa'=>$sa,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadRand($filter,$num,$baseonly=true){
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}

		$items=&$this->_array;
		
		DBP::Exec('call get_products_rand(:vis,:ipr,:ich,:new,:rec,:spec,:num)',
			array(
				'vis'=>isset($filter['visible'])&&$filter['visible'],
				'new'=>isset($filter['new'])&&$filter['new'],
				'rec'=>isset($filter['recomend'])&&$filter['recomend'],
				'spec'=>isset($filter['special'])&&$filter['special'],
				
				'num'=>$num,
				'ipr'=>$ispar,'ich'=>$ischld
			),
			function ($arr) use (&$items) {
				$items[]=new product($arr,'db');
			});

		//$this->_isFilled=1;
	}
	
	public function loadVariants($prod,$visible=true,$showbase=false){
		if ($prod instanceof product) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		$uid=auth_lp_adm::get_uid();
		
		if ($showbase) {
			$ispar=true;
			$ischld=true;
		} else {
			$ispar=false;
			$ischld=true;
		}
	
		$items=&$this->_array;
		
		DBP::Exec('call get_product_variants(:uid,:pid,:vis,:ipr,:ich)',
			array('uid'=>$uid, 'pid'=>$pid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld),
			function ($arr) use (&$items) {
				$items[]=new product($arr,'db');
			});

		//$this->_isFilled=1;
	}
	
	
	public function loadByIdList(array $ids,$visible=true,$showbase=false){
		$uid=auth_lp_adm::get_uid();
		
		if ($showbase) {
			$ispar=true;
			$ischld=true;
		} else {
			$ispar=false;
			$ischld=true;
		}
	
		$items=&$this->_array;
		
		DBP::Exec('call get_products_by_ids(:ids,:vis,:ipr,:ich)',
			array('ids'=>implode(',',$ids), 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld),
			function ($arr) use (&$items) {
				$items[]=new product($arr,'db');
			});

		//$this->_isFilled=1;
	}
	
	// #XPD [
	public function loadByLinksMS(paginator $pg=null,$kind,$master,$visible=true,$baseonly=true){
		if (is_object($master)) $mid=$master->id;
		elseif (is_numeric($master)) $mid=$master;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_linked_slaves(:knd,:mid,:vis,:ipr,:ich,null,null)',
				array('knd'=>$kind, 'mid'=>$mid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_linked_slaves(:knd,:mid,:vis,:ipr,:ich,:off,:onp)',
				array('knd'=>$kind, 'mid'=>$mid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld, 'off'=>$pg->get_offset(), 'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	
	public function loadByLinksMSRnd($num,$kind,$master,$visible=true,$baseonly=true){
		if (is_object($master)) $mid=$master->id;
		elseif (is_numeric($master)) $mid=$master;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		DBP::Exec('call get_products_linked_slaves_rnd(:knd,:mid,:vis,:ipr,:ich,:num)',
			array('knd'=>$kind, 'mid'=>$mid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld, 'num'=>$num),
			function ($arr) use (&$items) {
				$items[]=new product($arr,'db');
			});
		
		//$this->_isFilled=1;
	}
	
	public function loadByLinksX(paginator $pg=null,$kind,$prod,$visible=true,$baseonly=true){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_products_linked_cross(:knd,:pid,:vis,:ipr,:ich,null,null)',
				array('knd'=>$kind, 'pid'=>$pid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld),
				function ($arr) use (&$items) {
					$items[]=new product($arr,'db');
				});
		} else {
			DBP::MExec('call get_products_linked_cross(:knd,:pid,:vis,:ipr,:ich,:off,:onp)',
				array('knd'=>$kind, 'pid'=>$pid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld, 'off'=>$pg->get_offset(), 'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new product($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	
	public function loadByLinksXRnd($num,$kind,$prod,$visible=true,$baseonly=true){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		if ($baseonly) {
			$ispar=true;
			$ischld=false;
		} else {
			$ispar=true;
			$ischld=true;
		}
		
		$items=&$this->_array;
		
		DBP::Exec('call get_products_linked_cross_rnd(:knd,:pid,:vis,:ipr,:ich,:num)',
			array('knd'=>$kind, 'pid'=>$pid, 'vis'=>$visible, 'ipr'=>$ispar, 'ich'=>$ischld, 'num'=>$num),
			function ($arr) use (&$items) {
				$items[]=new product($arr,'db');
			});
		
		//$this->_isFilled=1;
	}
	
	// ] #XPD
}

?>