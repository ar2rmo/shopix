<?php

class image {
    protected $pic;
    protected $param;

    function __construct() {
        $this->param=array('width'=>0,'height'=>0,'mime'=>'');
    }

    function __destruct() {
        imagedestroy($this->pic);
    }

    public function from_file($path) {
		$param=getimagesize($path);
		$this->param['width']=$param[0];
		$this->param['height']=$param[1];
		$this->param['mime']=$param['mime'];

		if ($param) {
		    switch($this->param['mime']){
		        case 'image/jpeg': return $this->pic=imagecreatefromjpeg($path);
		        case 'image/png':  return $this->pic=imagecreatefrompng($path);
		        case 'image/gif':  return $this->pic=imagecreatefromgif($path);
		        default:           return null;
		    }
		} else {
			trigger_error('Bad image file "'.$path.'".',E_USER_WARNING);
		}
    }

    public function from_raw($data) {
	    $pic=imagecreatefromstring($data['data']);
	    if ($pic) {
	    	$this->set_pic($pic);
			$this->param['mime']=$data['mime'];
	    } else {
	    	trigger_error('Bad image data.',E_USER_WARNING);
	    }
    }

    public function get_info(){
        return $this->param;
    }

    public function get_rect(){
        return array('width'=>$this->param['width'],'height'=>$this->param['height']);
    }

    public function __get($key) {
    	if (isset($this->param[$key])) return $this->param[$key];
    	else return false;
    }

    public function to_file($path){
		switch($this->param['mime']) {
            case 'image/jpeg': imagejpeg($this->pic,$path.'.jpg',90); break;
            case 'image/png' : imagepng($this->pic,$path.'.png'); break;
            case 'image/gif' : imagegif($this->pic,$path.'.gif'); break;
        }
    }

    public function to_stream() {
        switch($this->param['mime']) {
            case 'image/jpeg': imagejpeg($this->pic,null,90); break;
            case 'image/png' : imagepng($this->pic); break;
            case 'image/gif' : imagegif($this->pic); break;
        }
    }

    public function to_client() {
        Header("Content-Type:".$this->param['mime']);
        $this->to_stream();
    }

    public function to_client_cache() {
        $raw=$this->get_raw($md5);

		Header("Cache-Control: public");

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
			$check=str_replace('"','',stripslashes($_SERVER['HTTP_IF_NONE_MATCH']));
			if ($check==$md5) {
				header('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		Header("Content-Type: ".$this->param['mime']);
        Header('ETag: "'.$md5.'"');
        echo $raw;
    }

    public function to_mysql(db $db){
        $bin['mime']='"'.$this->mime.'"';
		$bin['data']='"'.$db->escape($this->get_raw($md5)).'"';
		$bin['hash']='"'.$md5.'"';
		return $bin;
    }

    public function get_raw(&$md5=false){
       ob_start();
           $this->to_stream();
           $blob = ob_get_contents();
       ob_end_clean();
       if ($md5!==false) $md5=md5($blob);
       return $blob;
    }

    public function get_md5(){
        $md5=true;
		$this->get_raw($md5);
		return $md5;
    }

    protected function set_pic($pic) {
    	$this->param['width']=imagesx($pic);
    	$this->param['height']=imagesy($pic);
    	//$this->param['mime']='';
    	$this->pic=$pic;
    }

    public function resize_max($rect){
        $dst_rect=$this->calc_rect_max($rect,$this->get_rect());
        return $this->resize($dst_rect);
    }

    public function resize_min($rect){
        $dst_rect=$this->calc_rect_min($rect,$this->get_rect());
        return $this->resize($dst_rect);
    }

    public function lbox($rect){
        $dst_rect=$rect;
        $src_rect=$this->get_rect();
        $new_dst_rect=$this->calc_rect_max($dst_rect,$src_rect);

        $dst_spec = imagecreatetruecolor($dst_rect['width'], $dst_rect['height']);
		imagefill($dst_spec,0,0,imagecolorallocate($dst_spec, 255, 255, 255));
	    $dst = imagecreatetruecolor($new_dst_rect['width'], $new_dst_rect['height']);
	    imagealphablending($dst, false);
		imagesavealpha($dst, true);
	    imagecopyresampled($dst,$this->pic,0,0,0,0,$new_dst_rect['width'], $new_dst_rect['height'],$src_rect['width'],$src_rect['height']);
	    imagecopy($dst_spec,$dst,intval(($dst_rect['width']-$new_dst_rect['width'])/2),intval(($dst_rect['height']-$new_dst_rect['height'])/2),0,0,$new_dst_rect['width'],$new_dst_rect['height']);
	    imagedestroy($dst);
        $this->set_pic($dst_spec);
        return $this->pic;
    }

    public function resize($rect){
        $dst_rect=$rect;
        $src_rect=$this->get_rect();
        $dst=imagecreatetruecolor($dst_rect['width'], $dst_rect['height']);
        imagecopyresampled($dst,$this->pic,0,0,0,0,$dst_rect['width'], $dst_rect['height'],$src_rect['width'],$src_rect['height']);
        $this->set_pic($dst);
        return $this->pic;
    }
	
	public function crop($rect){
        $dst_rect=$rect; 
        $src_rect=$this->get_rect();
        $new_dst_rect=$this->calc_rect_crop($dst_rect,$src_rect);

	    $dst = imagecreatetruecolor($new_dst_rect['width'], $new_dst_rect['height']);
	    imagealphablending($dst, false);
		imagesavealpha($dst, true);
	    imagecopy($dst,$this->pic,0,0,intval(($src_rect['width']-$new_dst_rect['width'])/2),intval(($src_rect['height']-$new_dst_rect['height'])/2),$new_dst_rect['width'],$new_dst_rect['height']);
        $this->set_pic($dst);
        return $this->pic;
    }	

    protected function calc_rect_max($dst_rect,$src_rect){
        $out_w=$dst_rect['width'];
        $out_h=$dst_rect['height'];
        $in_w=$src_rect['width'];
        $in_h=$src_rect['height'];
        $o_coef = $out_w/$out_h;
        $i_coef = $in_w/$in_h;
        $r_rect=array();
        if($i_coef>$o_coef){
            $r_rect['width']=$out_w;
            $r_rect['height']=(int)round($out_w/$i_coef);
        }else{
            $r_rect['width']=(int)round($out_h*$i_coef);
            $r_rect['height']=$out_h;
        }
        return $r_rect;
    }

    protected function calc_rect_min($dst_rect,$src_rect){
        $out_w=$dst_rect['width'];
        $out_h=$dst_rect['height'];
        $in_w=$src_rect['width'];
        $in_h=$src_rect['height'];
        $o_coef = $out_w/$out_h;
        $i_coef = $in_w/$in_h;
        $r_rect=array();
        if($i_coef<$o_coef){
            $r_rect['width']=$out_w;
            $r_rect['height']=(int)round($out_w/$i_coef);
        }else{
        	$r_rect['width']=(int)round($out_h*$i_coef);
            $r_rect['height']=$out_h;
        }
        return $r_rect;
    }
	
	private function calc_rect_crop($dst_rect,$src_rect){
        $out_w=$dst_rect['width'];
        $out_h=$dst_rect['height'];
        $in_w=$src_rect['width'];
        $in_h=$src_rect['height'];
		
		$r_rect=array();
		$r_rect['width']=$in_w>$out_w?$out_w:$in_w;
		$r_rect['height']=$in_h>$out_h?$out_h:$in_h;

        return $r_rect;
    }
}

class image_cache {
	protected $db;

	protected $data=false;

	function __construct($db) {
		$this->db=$db;
	}

	function hit($id,$type) {
		$q=new query($this->db,'SELECT b.`mime`, c.`hash`, c.`data` FROM image_cache c LEFT JOIN binaries b ON b.ID=c.`source` WHERE b.ID='.$id.' AND c.`type`="'.$type.'"');
        $q->execute();

        if ($q->fetch_assoc()) {
        	$this->data=$q->row_data;
        	return true;
        } else {
        	return false;
        }
	}

	function store($id,$type,image $img) {
        $this->data['mime']=$img->mime;
		$this->data['data']=$img->get_raw($md5);
		$this->data['hash']=$md5;

		$q=new query($this->db,'INSERT INTO image_cache SET `source`="'.$id.'", `type`="'.$type.'", `hash`="'.$md5.'", `data`="'.$this->db->escape($this->data['data']).'"');
        $q->execute();
	}

	function output() {
		if ($this->data===false) return;

		Header("Cache-Control: public");

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
			$check=str_replace('"','',stripslashes($_SERVER['HTTP_IF_NONE_MATCH']));
			if ($check==$this->data['hash']) {
				header('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		Header("Content-Type: ".$this->data['mime']);
        Header('ETag: "'.$this->data['hash'].'"');
        echo $this->data['data'];
	}
}

?>
