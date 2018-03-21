<?
class ymlexport {
	public function wipe() {
		
	}
	
	public function doit($prefix) {
		return $this->generate($prefix,false,false);
	}

	public function generate($prefix,$vars=false,$compress=true) {
		$ext=$compress?'.yml.gz':'.yml';
		$wrapper=$compress?'compress.zlib://':'';
		
		$file='shop'.$ext;
		
		$fl=new SplFileObject($wrapper.ROOT_PATH.'shop'.$ext,'w');
		$fl->fwrite('<?xml version="1.0" encoding="utf-8"?>'."\n");
		$fl->fwrite('<!DOCTYPE yml_catalog SYSTEM "/Users/rainx/WebstormProjects/partner/pages/help/shops.dtd">'."\n");
		$fl->fwrite('<yml_catalog date="'.date('Y-m-d H:i').'">'."\n");
		$fl->fwrite("\t".'<shop>'."\n");
		$fl->fwrite("\t\t".'<name>'.htmlspecialchars($this->setts->tx_inf_shopname).'</name>'."\n");
		$fl->fwrite("\t\t".'<company>'.htmlspecialchars($this->setts->tx_inf_shopname).'</company>'."\n");
		$fl->fwrite("\t\t".'<url>'.$prefix.'/</url>'."\n");
		$fl->fwrite("\n");
		$fl->fwrite("\t\t".'<currencies>'."\n");
		$fl->fwrite("\t\t\t".'<currency id="'.($this->setts->mcurr_code?$this->setts->tx_mcurr_code:'UAH').'" rate="1" />'."\n");
		$fl->fwrite("\t\t".'</currencies>'."\n");
		$fl->fwrite("\n");
		
		$fl->fwrite("\t\t".'<categories>'."\n");
		DBP::Exec('call get_cattree(1, null)',null,
			function ($row) use ($fl,$prefix) {
				$cat = new category($row, 'db');
				
				if ($cat->parent_id==0)
					$fl->fwrite("\t\t\t".'<category id="'.$cat->id.'">'.htmlspecialchars($cat->tx_name).'</category>'."\n");
				else
					$fl->fwrite("\t\t\t".'<category id="'.$cat->id.'" parentId="'.$cat->parent_id.'">'.htmlspecialchars($cat->tx_name).'</category>'."\n");
			}
		);
		$fl->fwrite("\t\t".'</categories>'."\n");
		
		$fl->fwrite("\n");
		
		$fl->fwrite("\t\t".'<offers>'."\n");
		$q=$vars?'call get_products(1, 1, 1, null, null)':'call get_products(1, 1, 0, null, null)';
		DBP::Exec($q,null,
			function ($row) use ($fl,$prefix) {
				$prod = new product($row, 'db');
				
				//$tp=$prod->brand?'vendor.model':'basic';
				$tp='basic';
				
				$fl->fwrite("\t\t\t".'<offer id="'.$prod->id.'"'.(($tp=='basic')?'':' type="'.$tp.'"').' available="true">'."\n");
				$fl->fwrite("\t\t\t\t".'<url>'.$prefix.'/catalog'.$prod->tx_uri.'</url>'."\n");
				$fl->fwrite("\t\t\t\t".'<price>'.round($prod->price_salebase).'</price>'."\n");
				$fl->fwrite("\t\t\t\t".'<currencyId>'.($this->setts->mcurr_code?$this->setts->tx_mcurr_code:'UAH').'</currencyId>'."\n");
				$fl->fwrite("\t\t\t\t".'<categoryId>'.$prod->cid.'</categoryId>'."\n");
				if ($prod->ispict)
					$fl->fwrite("\t\t\t\t".'<picture>'.$prefix.$prod->pict_uri_big.'</picture>'."\n");
				$fl->fwrite("\t\t\t\t".'<delivery>true</delivery>'."\n");
				if ($tp=='basic') {
					$fl->fwrite("\t\t\t\t".'<name>'.htmlspecialchars($prod->tx_name).'</name>'."\n");
					if ($prod->brand_id)
						$fl->fwrite("\t\t\t\t".'<vendor>'.htmlspecialchars($prod->tx_brand_name).'</vendor>'."\n");
				}
				elseif ($tp=='vendor.model') {
					$fl->fwrite("\t\t\t\t".'<vendor>'.htmlspecialchars($prod->tx_brand_name).'</vendor>'."\n");
					$fl->fwrite("\t\t\t\t".'<model>'.htmlspecialchars($prod->tx_name).'</model>'."\n");
				}
				if ($prod->descr_full)
					$fl->fwrite("\t\t\t\t".'<description>'.htmlspecialchars($prod->tx_descr_full).'</description>'."\n");
				elseif ($prod->descr_short)
					$fl->fwrite("\t\t\t\t".'<description>'.htmlspecialchars($prod->tx_descr_short).'</description>'."\n");
				else
					$fl->fwrite("\t\t\t\t".'<description>'.htmlspecialchars($prod->tx_name).'</description>'."\n");
				$fl->fwrite("\t\t\t".'</offer>'."\n");
				$fl->fwrite("\n");
			}
		);
		$fl->fwrite("\t\t".'</offers>'."\n");
		
		$fl->fwrite("\t".'</shop>'."\n");
		$fl->fwrite('</yml_catalog>');
		
		$fl=null;

		return $file;
	}
}
?>