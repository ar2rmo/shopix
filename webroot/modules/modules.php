<?
class mod_modules extends module {
	function body() {
		$auth_adm=new auth_lp_adm;
		if ($auth_adm->check_priv(auth::PL_ADMIN)) {
			error_reporting(0);
			if ($this->src->uri->uri_2=='editor') {
				if ($this->src->uri->uri_3=='small_img.php') $this->small_img();
				elseif ($this->src->uri->uri_3=='controlset.php') $this->controlset();
				else $this->app->err404();
			} else {
				$this->app->err404();
			}
		} else {
			$this->app->err403();
		}
	}
	
	private function small_img() {
		$img=getimagesize($_SERVER['DOCUMENT_ROOT'].$_GET['src']);
		if($img[2]==1){//GIF
			$src_img=imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].$_GET['src']);
		}elseif($img[2]==2){//JPEG
			$src_img=imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'].$_GET['src']);
		}elseif($img[2]==3){//PNG
			$src_img=imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].$_GET['src']);
		}else exit;
		$w=imagesx($src_img);
		$h=imagesy($src_img);
		
		if($w>$h){//если ширина больше высоты
			$x=100;
			$y=$h*$x/$w;
		}else{
			$y=100;
			$x=$w*$y/$h;
		}	
		
		$result_img=imagecreatetruecolor($x,$y);
		imagecopyresampled ($result_img, $src_img, 0, 0, 0, 0, $x, $y, $w, $h);
		
		if($img[2]==1){//GIF
			imagegif($result_img);
		}elseif($img[2]==2){//JPEG
			imagejpeg($result_img);
		}elseif($img[2]==3){//PNG
			imagepng($result_img);
		}else exit;	
	}
	
	private function controlset() {
		if($_POST['mode']=='savepage'){
			$page_id=(int)$_POST['page_id'];
			$content_1=iconv("utf-8","windows-1251",$_POST['content_1']);
			$content_2=iconv("utf-8","windows-1251",$_POST['content_2']);
			$content_3=iconv("utf-8","windows-1251",$_POST['content_3']);
			
			$keywords_1=iconv("utf-8","windows-1251",$_POST['keywords_1']);
			$keywords_2=iconv("utf-8","windows-1251",$_POST['keywords_2']);
			$keywords_3=iconv("utf-8","windows-1251",$_POST['keywords_3']);
			
			$description_1=iconv("utf-8","windows-1251",$_POST['description_1']);
			$description_2=iconv("utf-8","windows-1251",$_POST['description_2']);
			$description_3=iconv("utf-8","windows-1251",$_POST['description_3']);

			$block_1=iconv("utf-8","windows-1251",$_POST['block_1']);
			$block_2=iconv("utf-8","windows-1251",$_POST['block_2']);
			
			if(!get_magic_quotes_gpc()){
				$content_1=addslashes($content_1);
				$content_2=addslashes($content_2);
				$content_3=addslashes($content_3);
				$keywords_1=addslashes($keywords_1);
				$keywords_2=addslashes($keywords_2);
				$keywords_3=addslashes($keywords_3);
				$description_1=addslashes($description_1);
				$description_2=addslashes($description_2);
				$description_3=addslashes($description_3);
				$block_1=addslashes($block_1);
				$block_2=addslashes($block_2);
			}

			$query="UPDATE `pages`
				SET `content_1`='$content_1', `content_2`='$content_2', `content_3`='$content_3', 
					`keywords_1`='$keywords_1', `keywords_2`='$keywords_2', `keywords_3`='$keywords_3', 
					`description_1`='$description_1', `description_2`='$description_2', `description_3`='$description_3',
					`block_1`='$block_1', `block_2`='$block_2'
				WHERE `page_id`='$page_id'
			"; 
			$query="UPDATE `pages`
				SET `content_1`='$content_1', `content_2`='$content_2', `content_3`='$content_3', 
					`keywords_1`='$keywords_1', `keywords_2`='$keywords_2', `keywords_3`='$keywords_3', 
					`description_1`='$description_1', `description_2`='$description_2', `description_3`='$description_3'
				WHERE `page_id`='$page_id'
			";
			if($result=mysql_query($query)){
				$mess="Страница успешно сохранена";
			}else{
				 $mess='Ошибка сохранения страницы. '.mysql_error();
			}
		}elseif($_POST['mode']=='upload_img' || $_POST['mode']=='upload_file'){
			$dir=$_POST['dir'];
			$tmp_name=$_FILES['file']['tmp_name'];
			$file_name=$_FILES['file']['name'];
			
			if(preg_match("#[^a-z_\-\.0-9]#i",$file_name)){
				$mess='<b class="error">Имена файлов могут содержать только символы латинского алфавита, цифры и символы _-.</b>';
			}elseif(!preg_match("#^/images#",$dir) && !preg_match("#^/files#",$dir)){
				$mess='<b class="error">Ошибка загрузки файла. (#301)</b>'.$dir;
			}elseif(is_uploaded_file($tmp_name)){
				if($_POST['mode']=='upload_img'){
					$img_type=getimagesize($tmp_name);
				}
				if($_POST['mode']=='upload_img' && $img_type[2]!=1 && $img_type[2]!=2 && $img_type[2]!=3){
					$mess='<b class="error">Неверный формат рисунка!<br> Вы можете загружать только jpg, gif или png изображения.</b>';
				}elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$dir.'/'.$file_name) && $_POST['replace']!='ok'){
					$mess='<b class="error">Файл с таким именем уже существует!</b> <a class="button_ok" href="javascript:void(0);" onclick="replace_file()">Заменить?</a>';
				}else{
					$dir=$_SERVER['DOCUMENT_ROOT'].$dir.'/';
					if($_POST['allow_resize']=='on' && $_POST['mode']=='upload_img'){
						$img_width=(int)$_POST['img_width'];
						$img_height=(int)$_POST['img_height'];
						$proportions=$_POST['proportions'];
						//echo "serror $img_width $img_height $proportions";
						
						if($img_type[2]==1){//GIF
							$src_img=imagecreatefromgif($tmp_name);
						}elseif($img_type[2]==2){//JPEG
							$src_img=imagecreatefromjpeg($tmp_name);
						}elseif($img_type[2]==3){//PNG
							$src_img=imagecreatefrompng($tmp_name);
						}else{
							exit ("class=.error 123");
							
						}
						$w=imagesx($src_img);
						$h=imagesy($src_img);
						
						if($proportions=='none'){
							$x=$img_width;
							$y=$img_height;
						}elseif($proportions=='on_width'){
							$x=$img_width;
							$y=(int)($h*$x/$w);
						}elseif($proportions=='on_height'){
							$y=$img_height;
							$x=(int)($w*$y/$h);
						}else{
							exit("<b class='error'>Ошибка сохранения файла (Err# 501)</b>");
						}
						
						$result_img=imagecreatetruecolor($x,$y);
						imagecopyresampled($result_img, $src_img, 0, 0, 0, 0, $x, $y, $w, $h);	
						
						if($img_type[2]==1){//GIF
							if(!imagegif($result_img, $dir.$file_name)){
								$mess='<b class="error">Ошибка сохранения файла (#504)</b>';
							}
						}elseif($img_type[2]==2){//JPEG
							if(!imagejpeg($result_img, $dir.$file_name)){
							//exit ("<b class='error'>$x - $y - $w - $h</b>");
								$mess='<b class="error">Ошибка сохранения файла (#505)</b>';
							}
						}elseif($img_type[2]==3){//PNG
							if(!imagepng($result_img, $dir.$file_name)){
								$mess='<b class="error">Ошибка сохранения файла (#506)</b>';
							}
						}else exit("<b class='error'>Ошибка сохранения файла (Err# 502)</b>");	
					}elseif(!move_uploaded_file($tmp_name,$dir.$file_name)){
						$mess='<b class="error">Ошибка загрузки файла. (#303)</b>';
					}
				}
			}else{
				$mess='<b class="error">Ошибка загрузки файла. (#302)</b>';
			}
			
		}elseif($_POST['mode']=='remove_file'){
			$file_name=$_POST['file_name'];
			if(!preg_match("#^/images/.#",$file_name) && !preg_match("#^/files/.#",$file_name)){
				$mess='<b class="error">Ошибка удаления файла!</b>';
			}else{
				$file_name=$_SERVER['DOCUMENT_ROOT'].$file_name;
				if(!unlink($file_name)){
					$mess='<b class="error">Ошибка удаления файла! (#401)</b>';
				}
			}
			
		}elseif($_POST['mode']=='get_dir_list'){
			if($_POST['param']!='file' && $_POST['param']!='img'){
				exit("<b class='error'>Неверное имя каталога!</b>");
			}
			$full_path=$_SERVER['DOCUMENT_ROOT'];
			function get_dir_list($dir){
				echo '<ul>';
				chdir($dir);
				$for_scan=getcwd();
				
				$for_scan=str_replace('\\','/',$for_scan);
				$path_from_root=str_replace("$_SERVER[DOCUMENT_ROOT]","",$for_scan);
				
				echo "<li><a href='javascript:void(0);' id='$path_from_root'>$dir</a></li>";
				$els=scandir($for_scan);
				foreach($els as $el){
					if($el!='.' && $el!='..' && is_dir($el)){
						get_dir_list($el);
					}
				}
				chdir('..');
				echo '</ul>';
			}
			
			$_POST['param']=='file' ? $dir='files' : $dir='images';
			//$dir=$_SERVER['DOCUMENT_ROOT'].'/'.$dir;
			
			chdir($_SERVER['DOCUMENT_ROOT']);
			get_dir_list($dir);
		}elseif($_POST['mode']=='read_img_dir'){
			$cols=6;
			$dir=$_POST['dir'];
			$dir2=$_SERVER['DOCUMENT_ROOT'].$dir;
			
			$els=scandir($dir2);
			
			
			echo '<table border="1" class="preview_icon_table"><tr>';
			$i=0;
			foreach($els as $el){
				if($el!='.' && $el!='..' && is_file($dir2.'/'.$el) && $el!='.htaccess'){
					$src=$dir.'/'.$el;
					$img=getimagesize($_SERVER['DOCUMENT_ROOT'].$src);
					$width=$img[0];
					$height=$img[1];
					$size=round(filesize($_SERVER['DOCUMENT_ROOT'].$src)/1024,2);
					echo "<td><a id='$src'><img src='/modules/editor/small_img.php?src=$src'><div>$el</div></a><div class='file_info'><div>$width x $height <a title='Посмотреть в новом окне' href='$src' target='_blank'><img src='/modules/editor/img/zoom.png' style='border:none;'></a></div><div>$size kB</div></div></td>";
					$i++;
					if($i%$cols==0){
						echo '</tr><tr>';
					}
				}
			}
			while($i%$cols!=0){
				echo '<td><br></td>';
				$i++;
			}
			echo '</tr></table>';
		}elseif($_POST['mode']=='read_file_dir'){
			$dir=$_POST['dir'];
			$dir2=$_SERVER['DOCUMENT_ROOT'].$dir;

			$els=scandir($dir2);
			
			
			//rewinddir($hdir);
			echo '<table border="1" cellspacing="0" cellpadding="2" class="preview_icon_table"><tr>';
			$cols=6;
			$i=0;
			
			foreach($els as $el){
				if($el!='.' && $el!='..' && is_file($dir2.'/'.$el) && $el!='.htaccess'){
					$id=$dir.'/'.$el;
					$size=round(filesize($_SERVER['DOCUMENT_ROOT'].$id)/1024,2);
					
					$lastpointpos=strrpos($el, '.');
					$extension=substr($el,$lastpointpos+1);
					$extension=strtolower($extension);			
					
					switch($extension){
						case 'txt': case 'ini':
							$iconimg='txt.gif'; break;
						case 'doc': case 'rtf': case 'dochtml': case 'docxml': case 'dot':
							$iconimg='word.gif'; break;
						case 'xls': case 'csv': 
							$iconimg='excel.gif'; break;
						case 'htm': case 'html': case 'xml': 
							$iconimg='html.gif'; break;
						//case 'js': 
							//$iconimg='.gif'; break;
						case 'css': 
							$iconimg='css.gif'; break;
						case '3gp': case 'asf': case 'avi': case 'flv': case 'dat': case 'divx': case 'm1v': case 'm2v': case 'mkv': case 'mov': case 'mp4': case 'mpe': case 'mpeg': case 'mpg': case 'mpv': case 'ogm': case 'qt': case 'ram': case 'rm': case 'rv': case 'vob': case 'wm': case 'wmv':
							$iconimg='video.gif'; break;
						case 'ac3': case 'aif': case 'aifc': case 'aiff': case 'au': case 'it': case 'kar': case 'mid': case 'midi': case 'mka': case 'mod': case 'mp1': case 'mp2': case 'mp3': case 'mpa': case 'ogg': case 'ra': case 'rmi': case 's3m': case 'snd': case 'stm': case 'wav': case 'wma': case 'xm':
							$iconimg='audio.gif'; break;
						case 'pdf': case 'fdf':
							$iconimg='pdf.gif'; break;
						case 'mdb':
							$iconimg='mdb.gif'; break;
						case 'djvu': case 'djv':
							$iconimg='djvu.gif'; break;
						case 'fon': case 'ttf':
							$iconimg='font.gif'; break;
						case 'art': case 'bmp': case 'crw': case 'emf': case 'fpx': case 'gif': case 'icl': case 'icn': case 'ico': case 'cur': case 'ani': case 'iff': case 'jpeg': case 'jpg': case 'kdc': case 'mag': case 'pbm': case 'pcd': case 'pcx': case 'pgm': case 'pic': case 'pict': case 'pct': case 'pix': case 'png': case 'ppm': case 'psd': case 'psp': case 'ras': case 'rsb': case 'sgi': case 'rgb': case 'sid': case 'tga': case 'tif': case 'tiff': case 'xif': case 'ttc': case 'wmf': case 'xbm': case 'xpm':
							$iconimg='img.gif'; break;
						case 'exe': case 'bat': case 'com': case 'pif':
							$iconimg='exe.gif'; break;
						case 'zip': case 'rar': case 'tar': case 'arj': case 'uc2': case 'gz': case 'lha': case 'ace': case 'tgz': case '7z': case 'cab': case 'uha': case 'lst':
							$iconimg='zip.gif'; break;
						case 'chm':
							$iconimg='chm.gif'; break;
						case 'hlp':
							$iconimg='hlp.gif'; break;
						case 'eml':
							$iconimg='eml.gif'; break;
						case 'mus': case 'ftm':
							$iconimg='mus.gif'; break;
						case 'dll':
							$iconimg='dll.gif'; break;
						default:
							$iconimg='unknown.gif';
							//break;
					}
					echo "<td><a id='$id'><img src='/modules/editor/fileicons/$iconimg'><div>$el</div></a><div class='file_info'>$size kB</div></td>";
					$i++;
					if($i%$cols==0){
						echo '<tr></tr>';
					}
				}
			}
			
			while($i%$cols!=0){
				echo '<td><br></td>';
				$i++;
			}
			echo '</tr></table>';

		}elseif($_POST['mode']=='save_new_dir'){
			$parent_dir=$_POST['parent_dir'];
			$new_dir=$_POST['new_dir'];
			$mess='';
			$dir=$_SERVER['DOCUMENT_ROOT'].$parent_dir.'/'.$new_dir;
			if(!preg_match("#^/images#",$parent_dir) && !preg_match("#^/files#",$parent_dir)){
				$mess='<b class="error">Странный родительский каталог.</b>';
			}elseif(preg_match("#[^0-9a-z_\.\-]#i",$new_dir)){
				$mess='<b class="error">В названиях каталогов разрешается использовать только буквы латинского алфавита, цифры и символы ._-</b>';
			}elseif(file_exists($dir)){
				$mess='<b class="error">Такой каталог уже существует!</b>';
			}
			
			if($mess==''){
				if(!mkdir($dir,0755)){
					$mess='<b class="error">Ошибка создания каталога!</b>';
				}else{
					$mess='<b class="ok">Каталог успешно создан.</b>';
				}
			}
		}elseif($_POST['mode']=='remove_dir'){
			$dir=$_POST['dir'];
			if(!preg_match("#^/images#",$dir) && !preg_match("#^/files#",$dir) || $dir=='/images' || $dir=='/files'){
				$mess = '<b class="error">Невозможно удалить каталог.</b>';
			}else{
				function remove_catalog($dir){
					if(!@chdir($dir)){
						return '<b class="error">Ошибка 103 удаления каталога!</b>';
					}
					$this_dir=getcwd();
					$this_dir=str_replace("\\","/",$this_dir);
					$els=scandir($this_dir);
					foreach($els as $el){
						if($el!='.' && $el!='..' && is_dir($el)){
							remove_catalog($el);
						}elseif(is_file($el)){
							if(!@unlink($el)){
								return '<b class="error">Ошибка 101 удаления каталога!</b>';
							}
						}
					}
					if(!@chdir('..')){
						return '<b class="error">Ошибка 104 удаления каталога!</b>';
					}
					if(!@rmdir($dir)){
						return '<b class="error">Ошибка 102 удаления каталога!</b>';
					}
				}
				$mess='';
				$mess=remove_catalog($_SERVER['DOCUMENT_ROOT'].$dir);
				if($mess==''){
					$mess='<b class="ok">Каталог успешно удален!</b>';
				}
			}
		}elseif($_POST['mode']=='save_renamed_dir'){
			$old_name=$_POST['old_name'];
			$new_name=$_POST['new_name'];
			if((!preg_match("#^/images/.#",$old_name) && !preg_match("#^/files/.#",$old_name)) || (!preg_match("#^/images/.#",$new_name) && !preg_match("#^/files/.#",$new_name))){
				$mess='<b class="error">Ошибка переименования каталога! (ErrN 201)</b>';
			}else{
				$old_name=$_SERVER['DOCUMENT_ROOT'].$old_name;
				$new_name=$_SERVER['DOCUMENT_ROOT'].$new_name;
				if(file_exists($new_name)){
					if(is_dir($new_name)){
						$mess='<b class="error">Каталог с таким именем уже существует!</b>';
					}else{
						if($_POST['replace']==1){
							unlink($new_name);
							rename($old_name,$new_name);

						}else{
							$mess='<b class="error">Файл с таким именем уже существует! <a class="button_ok" onclick="save_renamed_file(1)">Заменить?</a></b>';
						}
					}
				}elseif(!@rename($old_name,$new_name)){
					$mess='<b class="error">Ошибка переименования каталога! (ErrN 202)</b>';
				}
			}
			
		}
		echo $mess;
	}
}
?>