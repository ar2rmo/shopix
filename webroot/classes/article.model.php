<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'image.php';

class article extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/article.model.xml');
	}
	
	protected function pict_dir() {
		return static::pict_dir_by_kind($this->kind);
	}
	
	public static function pict_dir_by_kind($kind) {
		switch ($kind) {
			case 'ARTI':
				return 'articles';
			break;
			case 'NEWS':
				return 'news';
			break;
			case 'SPEC':
				return 'specials';
			break;
			default:
				return null;
		}
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
	
	protected function ovget_ispict() {
		$ip=true;
		
		$fp=$this->pict_dir().'/icon-'.$this->id;
		if (is_null($this->find_ext($fp))) $ip=false;
		
		return $ip;
	}
	
	protected function ovget_pict_uri() {
		$fp=$this->pict_dir().'/icon-'.$this->id;
		$file=$this->find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	public static function getByID($kind,$id,$vis=true){
		$obj=null;
		DBP::Exec("call get_article_by_id(:kind,:id,:vis)",
			function ($q) use ($kind,$id,$vis) {
				$q->bindParam(":kind",$kind,PDO::PARAM_STR);
				$q->bindParam(":vis",$vis,PDO::PARAM_BOOL);
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new article($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function getByURI($kind,$uri,$vis=true){
		$obj=null;
		DBP::Exec("call get_article_by_uri(:kind,:uri,:vis)",
			function ($q) use ($kind,$uri,$vis) {
				$q->bindParam(":kind",$kind,PDO::PARAM_STR);
				$q->bindParam(":vis",$vis,PDO::PARAM_BOOL);
				$q->bindParam(":uri",$uri,PDO::PARAM_STR);
			},
			function($arr) use (&$obj){
				$obj=new article($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($kind,&$dupl,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$dupl=false;
		
		$o=&$this;
		
		$qry='call update_article(:acl_uid,:aid,:kind,
				:adate,:fshow,:title,:keywords,:description,
				:css,:caption,:uri_name,:short,:full, 
				:href,:link)';
			
		$row=DBP::ExecSingleRow($qry,
			function ($q) use (&$o,$uid,$kind) {
				$q->bindParam(':acl_uid',$uid);
				$q->bindParam(':kind',$kind);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			},
			function ($err) use (&$dupl) {
				if ($err[0]='23000') {
					$dupl=true;
					return true;
				}
				return false;
			}
		);
		
		$o->id=$row['aff_id'];
		$o->uid=$row['uri_id'];

		if ($this->mpict_del) {
			$this->drop_pic($o->id,$kind);
		} else {
			if (!is_null($this->mpict)) {
				$this->load_pic($this->mpict,$o->id,$kind);
			}		
		}
	}
	
	public function db_delete($kind,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_article(:uid,:id)',array('uid'=>$uid,'id'=>$this->id));
		}
		
		$this->drop_pic($this->id,$kind);
		
		$this->id=0;
	}
	
	public static function load_pic($file,$id,$kind) {
		//copy($file['path'],MEDIA_PATH.static::pict_dir_by_kind($kind).'/icon-'.$id.'.'.$file['ext']);
		
		$setts=setts::get_obj();
		
		$img=new image();
		$img->from_file($file['path']);
		if (($img->width > $setts->img_art_width) || ($img->height > $setts->img_art_height)) {
			$img->resize_min(array('width'=>$setts->img_art_width,'height'=>$setts->img_art_height));
			$img->crop(array('width'=>$setts->img_art_width,'height'=>$setts->img_art_height));
		}
		$img->to_file(MEDIA_PATH.static::pict_dir_by_kind($kind).'/icon-'.$id);
	}
	
	public static function drop_pic($id,$kind) {
		$fp=static::pict_dir_by_kind($kind).'/icon-'.$id;
		$file=article::find_ext($fp);
		if (!is_null($file)) unlink(MEDIA_PATH.$file);
	}
}

class col_article extends mcollection {
    public function loadTop($kind,$num,$visible=true){
		$items=&$this->_array;
		
		DBP::Exec('call get_articles_top(:kind,:vis,:num)',
			array('kind'=>$kind,'vis'=>$visible,'num'=>$num),
			function ($arr) use (&$items) {
				$items[]=new article($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	public function loadRand($kind,$num,$visible=true){
		$items=&$this->_array;
		
		DBP::Exec('call get_articles_rand(:kind,:vis,:num)',
			array('kind'=>$kind,'vis'=>$visible,'num'=>$num),
			function ($arr) use (&$items) {
				$items[]=new article($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	public function loadAll(paginator $pg=null,$kind,$visible=true){
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_articles(:kind,:vis,null,null)',
				array('kind'=>$kind,'vis'=>$visible),
				function ($arr) use (&$items) {
					$items[]=new article($arr,'db');
				});
		} else {
			DBP::MExec('call get_articles(:kind,:vis,:off,:onp)',
				array('kind'=>$kind,'vis'=>$visible,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new article($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	public function loadBroadcast($kind,$limit){
		$items=&$this->_array;
		
		DBP::Exec('call get_articles_4broadcast(:kind,:lim)',
			array('kind'=>$kind,'lim'=>$limit),
			function ($arr) use (&$items) {
				$items[]=new article($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
}

?>