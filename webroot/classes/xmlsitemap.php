<?
class xmlsitemap {
	public function wipe() {
		$di=new DirectoryIterator(ROOT_PATH.'sitemaps');
		foreach ($di as $fi) {
			if($fi->isFile()) {
				unlink($fi->getPathname());
			}
		}
	}
	
	public function doit($prefix) {
		$num=DBP::ExecSingleVal('call get_sitemap_cnt');
		
		$multy=($num>50000);
		
		return $this->generate($prefix,$multy,false);
	}

	public function generate($prefix,$multy,$compress=true) {
		$ext=$compress?'.xml.gz':'.xml';
		$wrapper=$compress?'compress.zlib://':'';
		
		$files=array();

		$fnum=0;
		$fns='';
		
		$fli=null;
		if ($multy) {
			$files[]='index'.$ext;
			$fli=new SplFileObject($wrapper.ROOT_PATH.'sitemaps/sitemap'.$ext,'w');
			$fli->fwrite('<?xml version="1.0" encoding="UTF-8"?>'."\n");
			$fli->fwrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
			
			$fnum++;
			$fns=(string)$fnum;
		
			$fli->fwrite("\t".'<sitemap>'."\n");
			$fli->fwrite("\t\t".'<loc>'.$prefix.'/sitemaps/sitemap'.$fns.$ext.'</loc>'."\n");
			$fli->fwrite("\t".'</sitemap>'."\n");
		}
		
		$fl=new SplFileObject($wrapper.ROOT_PATH.'sitemaps/sitemap'.$fns.$ext,'w');
		$fl->fwrite('<?xml version="1.0" encoding="UTF-8"?>'."\n");
		$fl->fwrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
		
		$files[]='sitemap'.$fns.$ext;
		$fnum++;
		$num=0;
			
		DBP::Exec('call get_sitemap',null,
			function ($row) use ($multy,$fli,$prefix,&$fl,&$num,&$fnum,&$files) {
				if ($multy && $num>=50000) {
					$fns=(string)$fnum;
					
					$fl->fwrite('</urlset>');
					$fl=null;
					
					$fli->fwrite("\t".'<sitemap>'."\n");
					$fli->fwrite("\t\t".'<loc>'.$prefix.'/sitemaps/sitemap'.$fns.$ext.'</loc>'."\n");
					$fli->fwrite("\t".'</sitemap>'."\n");

					$fl=new SplFileObject($wrapper.ROOT_PATH.'sitemaps/sitemap'.$fns.$ext,'w');
					$fl->fwrite('<?xml version="1.0" encoding="UTF-8"?>'."\n");
					$fl->fwrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n");
					
					$files[]='sitemap'.$fns.$ext;
					$fnum++;
					$num=0;
				}

				$fl->fwrite("\t".'<url>'."\n");
				$fl->fwrite("\t\t".'<loc>'.$prefix.$row['uri'].'</loc>'."\n");
				$fl->fwrite("\t".'</url>'."\n");

				$num++;
			}
		);
		
		$fl->fwrite('</urlset>');
		$fl=null;

		if ($multy) {
			$fli->fwrite('</sitemapindex>');
			$fli=null;
		}

		return $files;
	}
}
?>