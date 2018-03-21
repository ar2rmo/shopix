<?
require_once LIBRARIES_PATH.'uribuilder.php';

class uribuilder_front extends uribuilder_base {
	protected function root() {
		if (!defined('CANONICAL_DOMAIN')) return parent::root();
		$host=CANONICAL_DOMAIN;
		if (preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',$host,$m)) {
			if ($m[1]=='') $m[1]='http';
			return $m[1].'://'.$m[2];
		}
		return parent::root();
	}
	
	public function bo($object) {
		if ($object instanceof menu)
			return $this->root().$object->uri;
		
		if ($object instanceof category)
			return $this->root().$object->uri;
		
		if ($object instanceof product)
			return $this->root().$object->uri;
			
		if ($object instanceof article)
			switch ($object->kind) {
				case 'ARTI':
					return $this->root().'/articles/'.$object->uid;
				break;
				case 'NEWS':
					return $this->root().'/news/'.$object->uid;
				break;
				case 'SPEC':
					return $this->root().'/specials/'.$object->uid;
				break;
				default:
					return $this->root();
			}
			
		return $this->root();
	}
	
	public function act($name, $arg) {
		switch ($name) {
			case 'cart_del':
				if ($arg instanceof product)
					return $this->root().'/cart?del='.$arg->id;
				if ($arg instanceof cart_item)
					return $this->root().'/cart?del='.$arg->pid;
				else
					return $this->root().'/cart?del='.$arg;
			case 'filter_brand':
				if ($arg instanceof refbk)
					return $this->root().'/filter/brand-'.$arg->id;
				elseif ($arg instanceof product)
					return $this->root().'/filter/brand-'.$arg->brand_id;
				else
					return $this->root().'/filter/brand-'.$arg;
			case 'filter_crit':
				if ($arg instanceof refbk)
					return $this->root().'/filter/crit-'.$arg->id;
				else
					return $this->root().'/filter/crit-'.$arg;
			case 'filter_tag':
				if ($arg instanceof refbk)
					return $this->root().'/filter/tag-'.urlencode($arg->name);
				else
					return $this->root().'/filter/crit-'.urlencode($arg);
			case 'compare':
				return $this->root().'/compare?list='.$arg;
			default:
				return $this->root();
		}
	}
	
	public function anch($anchor) {
		switch ($anchor) {
			case 'main':
				return $this->root().'/';
			case 'catalog':
				return $this->root().'/catalog';
			case 'products':
				return $this->root().'/products';
			case 'price':
				return $this->root().'/price';
			case 'cart':
				return $this->root().'/cart';
			case 'compare':
				return $this->root().'/compare';
			case 'articles':
				return $this->root().'/articles';
			case 'news':
				return $this->root().'/news';
			case 'specials':
				return $this->root().'/specials';
			case 'sitemap':
				return $this->root().'/sitemap';
			case 'filter_special':
				return $this->root().'/filter/special';
			case 'filter_new':
				return $this->root().'/filter/new';
			default:
				return $this->root();
		}
	}
}

class uribuilder_back extends uribuilder_base {
	protected function root() {
		return parent::root().'/admin';
	}
	
	public function bo($object) {
			
		return $this->root();
	}
	
	public function act($name, $arg) {
		switch ($name) {
			default:
				return $this->root();
		}
	}
	
	public function anch($anchor) {
		switch ($anchor) {
			default:
				return $this->root();
		}
	}
}

?>