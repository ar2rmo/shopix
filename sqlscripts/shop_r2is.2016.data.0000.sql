set sql_mode='NO_AUTO_VALUE_ON_ZERO';

insert into NODES (N_ID, N_P0, N_KIND, N_VOID, N_HIDE, N_URI, N_ORDER) values
(0, NULL, 'ROOT', 0, 0, '', 0);

insert into PAGES (
	PG_URI_NAME, PG_CAPTION, PG_TITLE, PG_KEYWORDS, PG_DESCRIPTION,
	PG_RO, PG_MNU, PG_MREF, PG_ORDER,
	PG_TEXT
) values (
	'main', 'Главная', null, '', '',
	1, 1, 1, 1,
	'<p>Текст на главной</p>'
);

insert into CT_STATUS (CTS_NAME) 
values ('Новый');

insert into SETTINGS set
	inf_shopname='Название магазина',
	inf_shopurl='shop.com',
	inf_keywords='',
	inf_description='',
	img_max_width=800, img_max_height=600,
	img_small_width=150, img_small_height=150,
	img_middle_width=250, img_middle_height=250,
	img_art_width=100, img_art_height=100,
	num_onpage_prod=30,
	num_box_rand=5,	num_box_new=5, num_box_recomend=5,
	num_onpage_news=10, num_box_news=3,
	num_onpage_articles=10, num_box_articles=3,
	num_onpage_specials=10, num_box_specials=3,
	num_onpage_orders=20,
	num_xpd_rand=5,
	ord_mail='user2835465444@domain.com',
	ord_initstatus=LAST_INSERT_ID(),
	name_crit1='Критерий 1', name_crit2='Критерий 2', name_crit3='Критерий 3',
	name_etab1='Доп. 1', name_etab2='Доп. 2', name_etab3='Доп. 3', name_etab4='Доп. 4', name_etab5='Доп. 5'
;

insert into CURRENCIES (CR_CODE, CR_NAME, CR_SNAME, CR_FORMAT) values
('USD','Доллар США','$','$%'),
('EUR','Евро','€','€%'),
('UAH','Украинская гривна','грн.','% грн.'),
('RUB','Российский рубль','руб.','% руб.');

insert into AUTH_ADM_USERS (BANNED, EMAIL, NAME, PHASH, ROLE) values
(0, 'admin', 'Администратор', MD5('admin'), 'PL_ROOT');