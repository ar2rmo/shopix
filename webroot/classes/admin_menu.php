<?
class admin_menu {
	private $tpl;

	private $menu=array(
		'main'=>array(
			'capt'=>'Главная',
			'href'=>'/admin',
			'selected'=>false,
			'submenu'=>false
		),
		'catalog'=>array(
			'capt'=>'Каталог',
			'href'=>'/admin/catalog',
			'selected'=>false,
			'submenu'=>array(
				'catalog'=>array(
					'capt'=>'Структура',
					'href'=>'/admin/catalog',
					'selected'=>false,
					'submenu'=>false
				),
				'products'=>array(
					'capt'=>'Товары',
					'href'=>'/admin/products',
					'selected'=>false,
					'submenu'=>false
				),
				'specs'=>array(
					'capt'=>'Спецификации',
					'href'=>'/admin/specs/classes',
					'selected'=>false,
					'submenu'=>false
				),
				'prices'=>array(
					'capt'=>'Цены',
					'href'=>'/admin/prices',
					'selected'=>false,
					'submenu'=>false
				),
				'specials'=>array(
					'capt'=>'Акционные',
					'href'=>'/admin/filter/specials',
					'selected'=>false,
					'submenu'=>false
				),
				'new'=>array(
					'capt'=>'Новинки',
					'href'=>'/admin/filter/new',
					'selected'=>false,
					'submenu'=>false
				),
				'hidden'=>array(
					'capt'=>'Скрытые',
					'href'=>'/admin/filter/hidden',
					'selected'=>false,
					'submenu'=>false
				),
				'import'=>array(
					'capt'=>'Импорт',
					'href'=>'/admin/import',
					'selected'=>false,
					'submenu'=>false
				),
				'export'=>array(
					'capt'=>'Экспорт',
					'href'=>'/admin/export',
					'selected'=>false,
					'submenu'=>false
				)/*,
				'yml'=>array(
					'capt'=>'Yandex Market',
					'href'=>'/admin/yml',
					'selected'=>false,
					'submenu'=>false
				)*/
			)
		),
		/*'specs'=>array(
			'capt'=>'Спецификации',
			'href'=>'/admin/specs/refbooks',
			'selected'=>false,
			'submenu'=>array(
				'refbooks'=>array(
					'capt'=>'Справочники',
					'href'=>'/admin/specs/refbooks',
					'selected'=>false,
					'submenu'=>false
				)
			)
		),*/
		'refbooks'=>array(
			'capt'=>'Справочники',
			'href'=>'/admin/refbooks',
			'selected'=>false,
			'submenu'=>array(
				'shipping'=>array(
					'capt'=>'Способы доставки',
					'href'=>'/admin/refbooks/shipping',
					'selected'=>false,
					'submenu'=>false
				),
				'payment'=>array(
					'capt'=>'Способы оплаты',
					'href'=>'/admin/refbooks/payment',
					'selected'=>false,
					'submenu'=>false
				),
				'avail'=>array(
					'capt'=>'Наличие товара',
					'href'=>'/admin/refbooks/avail',
					'selected'=>false,
					'submenu'=>false
				),
				'status'=>array(
					'capt'=>'Статус заказа',
					'href'=>'/admin/refbooks/status',
					'selected'=>false,
					'submenu'=>false
				),
				'brands'=>array(
					'capt'=>'Бренд',
					'href'=>'/admin/refbooks/brands',
					'selected'=>false,
					'submenu'=>false
				),
				'criterias'=>array(
					'capt'=>'Критерии',
					'href'=>'/admin/refbooks/criterias1',
					'selected'=>false,
					'submenu'=>array(
						'criterias1'=>array(
							'capt'=>'Критерий 1',
							'href'=>'/admin/refbooks/criterias1',
							'selected'=>false,
							'submenu'=>false
						),
						'criterias2'=>array(
							'capt'=>'Критерий 2',
							'href'=>'/admin/refbooks/criterias2',
							'selected'=>false,
							'submenu'=>false
						),
						'criterias3'=>array(
							'capt'=>'Критерий 3',
							'href'=>'/admin/refbooks/criterias3',
							'selected'=>false,
							'submenu'=>false
						)
					)
				),
				'specs'=>array(
					'capt'=>'Спецификации',
					'href'=>'/admin/specs/refbooks',
					'selected'=>false,
					'submenu'=>false
				)
				/*'criterias1'=>array(
					'capt'=>'Критерий 1',
					'href'=>'/admin/refbooks/criterias1',
					'selected'=>false,
					'submenu'=>false
				),
				'criterias2'=>array(
					'capt'=>'Критерий 2',
					'href'=>'/admin/refbooks/criterias2',
					'selected'=>false,
					'submenu'=>false
				),*/
				/*,
				'prices'=>array(
					'capt'=>'Цена',
					'href'=>'/admin/refbooks/prices',
					'selected'=>false,
					'submenu'=>false
				)*/
			)
		),
		'orders'=>array(
			'capt'=>'Заказы',
			'href'=>'/admin/orders',
			'selected'=>false,
			'submenu'=>false
		),
		'customers'=>array(
			'capt'=>'Клиенты',
			'href'=>'/admin/customers',
			'selected'=>false,
			'submenu'=>false
		),
		'pages'=>array(
			'capt'=>'Страницы',
			'href'=>'/admin/pages/main',
			'selected'=>false,
			'submenu'=>false
		),
		'articles'=>array(
			'capt'=>'Новости +',
			'href'=>'/admin/artedit/articles',
			'selected'=>false,
			'submenu'=>array(
				'articles'=>array(
					'capt'=>'Статьи',
					'href'=>'/admin/artedit/articles',
					'selected'=>false,
					'submenu'=>false
				),
				'news'=>array(
					'capt'=>'Новости',
					'href'=>'/admin/artedit/news',
					'selected'=>false,
					'submenu'=>false
				),
				'specials'=>array(
					'capt'=>'Акции',
					'href'=>'/admin/artedit/specials',
					'selected'=>false,
					'submenu'=>false
				),
				'subscribers'=>array(
					'capt'=>'Подписчики',
					'href'=>'/admin/subscribers',
					'selected'=>false,
					'submenu'=>false
				)
			)
		),
		'settings'=>array(
			'capt'=>'Настройки',
			'href'=>'/admin/settings',
			'selected'=>false,
			'submenu'=>array(
				'settings'=>array(
					'capt'=>'Общие',
					'href'=>'/admin/settings',
					'selected'=>false,
					'submenu'=>false
				),
				'currencies'=>array(
					'capt'=>'Валюты',
					'href'=>'/admin/currencies',
					'selected'=>false,
					'submenu'=>false
				),
				'admusers'=>array(
					'capt'=>'Администраторы сайта',
					'href'=>'/admin/admusers',
					'selected'=>false,
					'submenu'=>false
				)
			)
		),
		'logout'=>array(
			'capt'=>'Выход',
			'href'=>'/admin/logout',
			'selected'=>false,
			'submenu'=>false
		),
	);

	private $submenu=false;
	private $submenu2=false;

	function __construct(template $tpl) {
		$this->tpl=$tpl;
		
		if (!(defined('SPEC_ENABLED') && SPEC_ENABLED)) {
			unset($this->menu['catalog']['submenu']['specs']);
			unset($this->menu['refbooks']['submenu']['specs']);
		}
		
		if (!(defined('PRICE_ENABLED') && PRICE_ENABLED)) {
			unset($this->menu['catalog']['submenu']['prices']);
		}
		
		if (!(defined('IMPEXP_ENABLED') && IMPEXP_ENABLED)) {
			unset($this->menu['catalog']['submenu']['import']);
			unset($this->menu['catalog']['submenu']['export']);
		}
	}

	public function setcurrent($item) {
		$this->menu[$item]['selected']=true;
		$this->submenu=$this->menu[$item]['submenu'];
	}

	public function setcurrentsub($item) {
		if (!$this->submenu) return false;
		$this->submenu[$item]['selected']=true;
		$this->submenu2=$this->submenu[$item]['submenu'];
	}

	public function setcurrentsub2($item) {
		if (!$this->submenu2) return false;
		$this->submenu2[$item]['selected']=true;
	}

	public function add_submenu_item($item) {
		$this->submenu[]=$item;
		if ($item['selected']&&$item['submenu']) $this->submenu2=$item['submenu'];
	}

	public function add_submenu2_item($item) {
		$this->submenu2[]=$item;
	}

	public function add_submenu($item) {
		$this->submenu=$item;
	}

	public function add_submenu2($item) {
		$this->submenu=$item;
	}

	public function hide($menu,$submenu=false) {
		if ($submenu) unset($this->menu[$menu]['submenu'][$submenu]);
		else unset($this->menu[$menu]);
	}

	public function apply() {
		$this->tpl->set('menu',$this->menu);
		$this->tpl->set('submenu',$this->submenu);
		$this->tpl->set('submenu2',$this->submenu2);
		unset($this->menu,$this->submenu,$this->submenu2);
	}
}
?>