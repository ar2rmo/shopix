replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-views','2016.0000');

drop view if exists viewPRODUCTS_PRICE;
create view viewPRODUCTS_PRICE (
	pp_pid,
	pp_salebase, pp_salebase_old, pp_salebase_min, pp_salebase_max
) as
select
	p.P_ID,
	p.P_PRICE*ifnull(pt.PT_RATE,1)*ifnull(pt.PT_MARK,1)+ifnull(pt.PT_INCR,0),
	p.P_PRICE_OLD*ifnull(pt.PT_RATE,1)*ifnull(pt.PT_MARK,1)+ifnull(pt.PT_INCR,0),
	p.P_PRICE_MIN*ifnull(pt.PT_RATE,1)*ifnull(pt.PT_MARK,1)+ifnull(pt.PT_INCR,0),
	p.P_PRICE_MAX*ifnull(pt.PT_RATE,1)*ifnull(pt.PT_MARK,1)+ifnull(pt.PT_INCR,0)
from PRODUCTS p
left join PRICE_TYPES pt on pt.PT_ID=p.PT_ID;

drop view if exists viewCATEGORIES;
create view viewCATEGORIES (
	nid,
	np,
	uri_name,
	uri,
	fshow,
	visible,
	count,
	fullcount,
	vcount,
	vfullcount,
	modcount,
	fullmodcount,
	vmodcount,
	vfullmodcount,
	name,
	fullname,
	title,
	keywords,
	description,
	css,
	text_top,
	text_bott,
	d_name,
	d_title,
	d_keywords,
	d_description,
	t_level,
	c_order,
	pict_uri
) as
select
	n.N_ID,
	n.N_P0,
	n.N_URI,
	t.N_URI,
	not n.N_HIDE,
	not t.N_HIDE,
	t.CNT_ALL_PRNT,
	t.CNT_ALL_FULL_PRNT,
	t.CNT_VIS_PRNT,
	t.CNT_VIS_FULL_PRNT,
	t.CNT_ALL_CHLD,
	t.CNT_ALL_FULL_CHLD,
	t.CNT_VIS_CHLD,
	t.CNT_VIS_FULL_CHLD,
	c.CAT_NAME,
	c.CAT_FULLNAME,
	c.CAT_TITLE,
	c.CAT_KEYWORDS,
	c.CAT_DESCR,
	c.CAT_CSS,
	c.TEXT_TOP,
	c.TEXT_BOTT,
	IFNULL(c.CAT_FULLNAME,c.CAT_NAME),
	IFNULL(c.CAT_TITLE,c.CAT_NAME),
	IF(c.CAT_KEYWORDS='',NULL,c.CAT_KEYWORDS),
	IF(c.CAT_DESCR='',NULL,c.CAT_DESCR),
	i.N_CLEV-1,
	t.N_ORDER,
	c.CAT_PICT_URI
from NODES n
inner join NODE_TREE t on t.N_ID=n.N_ID
inner join CATEGORIES c on c.N_ID=n.N_ID
inner join NODE_INCAPS i on i.N_C=t.N_ID and i.N_P is null;

drop view if exists viewCATEGORIESshort;
create view viewCATEGORIESshort as
select
	nid,
	np,
	uri_name,
	uri,
	fshow,
	visible,
	count,
	fullcount,
	vcount,
	vfullcount,
	modcount,
	fullmodcount,
	vmodcount,
	vfullmodcount,
	name,
	fullname,
	title,
	css,
	d_name,
	d_title,
	t_level,
	c_order,
	pict_uri
from viewCATEGORIES;

drop view if exists viewROOT;
create view viewROOT (
	nid,
	np,
	uri_name,
	uri,
	fshow,
	visible,
	count,
	fullcount,
	vcount,
	vfullcount,
	modcount,
	fullmodcount,
	vmodcount,
	vfullmodcount,
	name,
	fullname,
	title,
	keywords,
	description,
	css,
	text_top,
	text_bott,
	d_fname,
	d_title,
	d_keywords,
	d_description,
	t_level,
	c_order,
	pict_uri
) as
select
	0,
	0,
	'',
	'/',
	0,
	0,
	t.CNT_ALL_PRNT,
	t.CNT_ALL_FULL_PRNT,
	t.CNT_VIS_PRNT,
	t.CNT_VIS_FULL_PRNT,
	t.CNT_ALL_CHLD,
	t.CNT_ALL_FULL_CHLD,
	t.CNT_VIS_CHLD,
	t.CNT_VIS_FULL_CHLD,
	'',
	null,
	null,
	'',
	'',
	null,
	'',
	'',
	'',
	'',
	null,
	null,
	i.N_CLEV-1,
	t.N_ORDER,
	''
from NODE_TREE t
inner join NODE_INCAPS i on i.N_C=t.N_ID and i.N_P is null
where t.N_ID=0;

drop view if exists viewPRODUCTS;
create view viewPRODUCTS (
	pid,
	nid,
	parent_id,
	inherited,
	uri_name,
	cat_uri,
	uri,
	fshow,
	visible,
	name,
	fullname,
	title,
	variant,
	keywords,
	description,
	css,
	code,
	barcode,
	brand_id,
	brand_name,
	measure,
	size,
	descr_short,
	descr_full,
	descr_tech,
	price_type,
	price,
	oprice,
	price_min,
	price_max,
	avail_id,
	avail_name,
	avail_num,
	fnew,
	frecomend,
	fspecial,
	forsale,
	sellable,
	created,
	modified,
	modified_price,
	log_all,
	log_day,
	d_name,
	d_title,
	d_keywords,
	d_description,
	c_order,
	p_order,
	v_order,
	pict_uri,
	extra1,
	extra2,
	extra3,
	extra4,
	extra5,
	price_salebase,
	price_salebase_old,
	price_salebase_min,
	price_salebase_max
) as
select
	p.P_ID,
	n.N_ID,
	p.P_P0,
	p.P_P0 is not null,
	p.P_URI,
	t.N_URI,
	CONCAT_WS('/',t.N_URI,p.P_URI),
	not p.P_HIDE,
	not (t.N_HIDE or p.P_HIDE),
	p.P_NAME,
	p.P_FULLNAME,
	p.P_TITLE,
	p.P_VARIANT,
	p.P_KEYWORDS,
	p.P_DESCR,
	p.P_CSS,
	p.P_CODE,
	p.P_BARCODE,
	b.B_ID,
	b.B_NAME,
	p.P_MEASURE,
	p.P_SIZE,
	p.P_DESCR_SHORT,
	p.P_DESCR_FULL,
	p.P_DESCR_TECH,
	p.PT_ID,
	p.P_PRICE,
	p.P_PRICE_OLD,
	p.P_PRICE_MIN,
	p.P_PRICE_MAX,
	av.PV_ID,
	av.PV_NAME,
	p.P_AVAIL_COUNT,
	p.IS_NEW,
	p.IS_RECOMEND,
	p.IS_SPECIAL,
	p.P_FORSALE,
	p.P_FORSALE,
	p.D_CREATE,
	p.D_MODIFY,
	p.D_MODIFY_PRICE,
	p.LOG_ALL,
	IF(TO_DAYS(p.LOG_DAY)=TO_DAYS(NOW()),p.LOG_TODAY,0),
	IFNULL(p.P_FULLNAME,p.P_NAME),
	IFNULL(p.P_TITLE,IFNULL(p.P_FULLNAME,p.P_NAME)),
	IF(p.P_KEYWORDS='',IF(c.CAT_KEYWORDS='',NULL,c.CAT_KEYWORDS),p.P_KEYWORDS),
	IF(p.P_DESCR='',IF(c.CAT_DESCR='',NULL,c.CAT_DESCR),p.P_DESCR),
	t.N_ORDER,
	p.P_ORDER,
	p.P_ORDER_CHLD,
	p.P_PICT_URI,
	p.EXTRA_1,
	p.EXTRA_2,
	p.EXTRA_3,
	p.EXTRA_4,
	p.EXTRA_5,
	pp.pp_salebase,
	pp.pp_salebase_old,
	pp.pp_salebase_min,
	pp.pp_salebase_max
from PRODUCTS p
inner join viewPRODUCTS_PRICE pp on pp.pp_pid=p.P_ID
inner join NODES n on n.N_ID=p.N_ID
inner join CATEGORIES c on c.N_ID=n.N_ID
inner join NODE_TREE t on t.N_ID=n.N_ID
left join BRANDS b on b.B_ID=p.B_ID
left join P_AVAIL av on av.PV_ID=p.PV_ID;



drop view if exists viewPRODUCTSshort;
create view viewPRODUCTSshort as
select
	pid,
	nid,
	parent_id,
	inherited,
	uri_name,
	cat_uri,
	uri,
	fshow,
	visible,
	name,
	fullname,
	title,
	css,
	code,
	barcode,
	brand_id,
	brand_name,
	measure,
	size,
	descr_short,
	price,
	oprice,
	price_min,
	price_max,
	avail_id,
	avail_name,
	avail_num,
	fnew,
	frecomend,
	fspecial,
	forsale,
	sellable,
	created,
	modified,
	modified_price,
	log_all,
	log_day,
	d_name,
	d_title,
	c_order,
	p_order,
	v_order,
	pict_uri,
	price_salebase,
	price_salebase_old,
	price_salebase_min,
	price_salebase_max
from viewPRODUCTS;

drop view if exists viewPRODUCTS_INH;
create view viewPRODUCTS_INH (
	pid,
	inh_name,
	inh_fullname,
	inh_title,
	inh_keywords,
	inh_description,
	inh_css,
	inh_code,
	inh_barcode,
	inh_brand,
	inh_measure,
	inh_size,
	inh_descr_short,
	inh_descr_full,
	inh_descr_tech,
	inh_price,
	inh_fnew,
	inh_frecomend,
	inh_fspecial,
	inh_tags,
	inh_crits
) as
select
	pi.P_ID,
	pi.P_NAME,
	pi.P_FULLNAME,
	pi.P_TITLE,
	pi.P_KEYWORDS,
	pi.P_DESCR,
	pi.P_CSS,
	pi.P_CODE,
	pi.P_BARCODE,
	pi.B_ID,
	pi.P_MEASURE,
	pi.P_SIZE,
	pi.P_DESCR_SHORT,
	pi.P_DESCR_FULL,
	pi.P_DESCR_TECH,
	pi.P_PRICE,
	pi.IS_NEW,
	pi.IS_RECOMEND,
	pi.IS_SPECIAL,
	pi.PRODUCT_TAGS,
	pi.PRODUCT_CRETERIAS
from PRODUCTS_INHERIT pi;


drop view if exists viewSPEC_GROUPS;
create view viewSPEC_GROUPS (
	sgid,
    `code`, `name`,
    sg_order
) as
select
	sg.SG_ID,
    sg.SG_CODE, sg.SG_NAME,
    sg.SG_ORDER
from SPEC_GROUPS sg;

drop view if exists viewSPEC_CLASSES;
create view viewSPEC_CLASSES (
	scid, sgid, icode,
    `code`, `name`, is_multy, datatype,
    group_id, group_code, group_name,
    g_order, sg_order, sc_order
) as
select
	sc.SC_ID, sg.SG_ID, concat(ifnull(sg.SG_CODE,concat('sg-',sg.SG_ID)),'-',ifnull(sc.SC_CODE,concat('sc-',sc.SC_ID))),
    sc.SC_CODE, sc.SC_NAME, sc.SC_MULTY, sc.SC_DTYPE,
    sg.SG_ID, sg.SG_CODE, sg.SG_NAME,
    sc.SC_ORDER_GLOBAL, sg.SG_ORDER, sc.SC_ORDER
from SPEC_CLASSES sc
left join SPEC_GROUPS sg on sg.SG_ID=sc.SG_ID;

drop view if exists viewPRODUCT_SPECS;
create view viewPRODUCT_SPECS (
	ProductId, ClassId,
    DataType,
    
    RefbookId, RefbookName, RefbookCode,
    ValueString, ValueInteger, ValueFloat, ValueMoney, ValueBoolean, ValueDatetime/*,
    
    ParentRefbookId, ParentRefbookName, ParentRefbookCode,
    ParentValueString, ParentValueInteger, ParentValueFloat, ParentValueMoney, ParentValueBoolean, ParentValueDatetime,
 
	InheritedRefbookId, InheritedRefbookName, InheritedRefbookCode,
    InheritedValueString, InheritedValueInteger, InheritedValueFloat, InheritedValueMoney, InheritedValueBoolean, InheritedValueDatetime*/
) as
select
	p.P_ID, sc.SC_ID,
    sc.SC_DTYPE,
    
    rb.SR_ID, rb.SR_NAME, rb.SR_CODE,
    ps.PS_VALUE_STRING, ps.PS_VALUE_INTEGER, ps.PS_VALUE_FLOAT, ps.PS_VALUE_MONEY, ps.PS_VALUE_BOOLEAN, ps.PS_VALUE_DATETIME/*,
    
    rb_p.SR_ID, rb_p.SR_NAME, rb_p.SR_CODE,
    ps_p.PS_VALUE_STRING, ps_p.PS_VALUE_INTEGER, ps_p.PS_VALUE_FLOAT, ps_p.PS_VALUE_MONEY, ps_p.PS_VALUE_BOOLEAN, ps_p.PS_VALUE_DATETIME,
    
    ifnull(rb.SR_ID,rb_p.SR_ID), ifnull(rb.SR_NAME,rb_p.SR_NAME), ifnull(rb.SR_CODE,rb_p.SR_CODE),
    ifnull(ps.PS_VALUE_STRING,ps_p.PS_VALUE_STRING), ifnull(ps.PS_VALUE_INTEGER,ps_p.PS_VALUE_INTEGER), ifnull(ps.PS_VALUE_FLOAT,ps_p.PS_VALUE_FLOAT), ifnull(ps.PS_VALUE_MONEY,ps_p.PS_VALUE_MONEY), ifnull(ps.PS_VALUE_BOOLEAN,ps_p.PS_VALUE_BOOLEAN), ifnull(ps.PS_VALUE_DATETIME,ps_p.PS_VALUE_DATETIME)*/
from PRODUCTS p
inner join PRODUCT_SPECS ps on ps.P_ID=p.P_ID
inner join SPEC_CLASSES sc on sc.SC_ID=ps.SC_ID
left join SPEC_REFBOOK rb on rb.SR_ID=ps.SR_ID
/*left join PRODUCT_SPECS ps_p on ps_p.P_ID=p.P_P0 and ps_p.SC_ID=sc.SC_ID
left join SPEC_REFBOOK rb_p on rb_p.SR_ID=ps_p.SR_ID*/;

drop view if exists viewPRODUCT_SPECS_INH;
create view viewPRODUCT_SPECS_INH (
	ProductId, ClassId,
    DataType,
    
    RefbookId, RefbookName, RefbookCode,
    ValueString, ValueInteger, ValueFloat, ValueMoney, ValueBoolean, ValueDatetime/*,
    
    ParentRefbookId, ParentRefbookName, ParentRefbookCode,
    ParentValueString, ParentValueInteger, ParentValueFloat, ParentValueMoney, ParentValueBoolean, ParentValueDatetime,
 
	InheritedRefbookId, InheritedRefbookName, InheritedRefbookCode,
    InheritedValueString, InheritedValueInteger, InheritedValueFloat, InheritedValueMoney, InheritedValueBoolean, InheritedValueDatetime*/
) as
select
	p.P_ID, sc.SC_ID,
    sc.SC_DTYPE,
    
    rb.SR_ID, rb.SR_NAME, rb.SR_CODE,
    ps.PS_VALUE_STRING, ps.PS_VALUE_INTEGER, ps.PS_VALUE_FLOAT, ps.PS_VALUE_MONEY, ps.PS_VALUE_BOOLEAN, ps.PS_VALUE_DATETIME/*,
    
    rb_p.SR_ID, rb_p.SR_NAME, rb_p.SR_CODE,
    ps_p.PS_VALUE_STRING, ps_p.PS_VALUE_INTEGER, ps_p.PS_VALUE_FLOAT, ps_p.PS_VALUE_MONEY, ps_p.PS_VALUE_BOOLEAN, ps_p.PS_VALUE_DATETIME,
    
    ifnull(rb.SR_ID,rb_p.SR_ID), ifnull(rb.SR_NAME,rb_p.SR_NAME), ifnull(rb.SR_CODE,rb_p.SR_CODE),
    ifnull(ps.PS_VALUE_STRING,ps_p.PS_VALUE_STRING), ifnull(ps.PS_VALUE_INTEGER,ps_p.PS_VALUE_INTEGER), ifnull(ps.PS_VALUE_FLOAT,ps_p.PS_VALUE_FLOAT), ifnull(ps.PS_VALUE_MONEY,ps_p.PS_VALUE_MONEY), ifnull(ps.PS_VALUE_BOOLEAN,ps_p.PS_VALUE_BOOLEAN), ifnull(ps.PS_VALUE_DATETIME,ps_p.PS_VALUE_DATETIME)*/
from PRODUCTS p
inner join PRODUCT_SPECS ps on ps.P_ID=p.P_ID
inner join SPEC_CLASSES sc on sc.SC_ID=ps.SC_ID
left join SPEC_REFBOOK rb on rb.SR_ID=ps.SR_ID
/*left join PRODUCT_SPECS ps_p on ps_p.P_ID=p.P_P0 and ps_p.SC_ID=sc.SC_ID
left join SPEC_REFBOOK rb_p on rb_p.SR_ID=ps_p.SR_ID*/;

drop view if exists viewSPEC_REFBOOK;
create view viewSPEC_REFBOOK (
	srid, scid,
    `code`, `name`,
    sr_order
) as
select
	rb.SR_ID, rb.SC_ID,
    rb.SR_CODE, rb.SR_NAME,
    rb.SR_ORDER
from SPEC_REFBOOK rb
where not rb.SR_VOID;


# ------------------------------------------------------


drop view if exists viewMENU;
create view viewMENU (
	pid,
	uri_name,
	uri,
	caption,
	css,
	ro,
	mref,
    aref,
	par_id,
	par_caption,
	par_uri,
	m_level,
	m_order,
	m_torder,
	visible
) as
select
	p.PG_ID,
	p.PG_URI_NAME,
	IF(p.PG_RO and (p.PG_URI_NAME='main'),'/',IF(p.PG_MREF,IF(p.PG_ALTREF is null,CONCAT_WS('/','',p.PG_URI_NAME),p.PG_ALTREF),CONCAT_WS('/','','page',pp.PG_URI_NAME,p.PG_URI_NAME))),
	p.PG_CAPTION,
	p.PG_CSS,
	p.PG_RO,
	p.PG_MREF,
    p.PG_ALTREF,
	pp.PG_ID,
	pp.PG_CAPTION,
	pp.PG_URI_NAME,
	IF(p.PG_P0 IS NULL,1,2),
	p.PG_ORDER,
	IF(pp.PG_ID is null,p.PG_ORDER*1000,pp.PG_ORDER*1000 + p.PG_ORDER),
	p.PG_MNU and (pp.PG_MNU is null or pp.PG_MNU)
from PAGES p
left join PAGES pp on pp.PG_ID=p.PG_P0;


drop view if exists viewPAGE;
create view viewPAGE (
	pid,caption,uri_name,uri,par_id,  -- 5
	par_caption,par_uri_name,par_uri,title,keywords, -- 10
	description,css,d_title,d_keywords,d_description, -- 15
	ptext,p_empty,mnu,mref,aref,ro
) as
select
	p.PG_ID,
	p.PG_CAPTION,
	p.PG_URI_NAME,
	IF(p.PG_RO and (p.PG_URI_NAME='main'),'/',IF(p.PG_MREF,IF(p.PG_ALTREF is null,CONCAT_WS('/','',p.PG_URI_NAME),p.PG_ALTREF),CONCAT_WS('/','','page',pp.PG_URI_NAME,p.PG_URI_NAME))),
	p.PG_P0,  -- 5
	pp.PG_CAPTION,
	pp.PG_URI_NAME,
	IF(pp.PG_RO and (pp.PG_URI_NAME='main'),'/',IF(pp.PG_MREF,IF(pp.PG_ALTREF is null,CONCAT_WS('/','',pp.PG_URI_NAME),pp.PG_ALTREF),CONCAT_WS('/','','page',pp.PG_URI_NAME))),
	p.PG_TITLE,
	p.PG_KEYWORDS, -- 10
	p.PG_DESCRIPTION,
	p.PG_CSS,
	IFNULL(p.PG_TITLE,p.PG_CAPTION),
	IFNULL(p.PG_KEYWORDS,pp.PG_KEYWORDS),
	IFNULL(p.PG_DESCRIPTION,pp.PG_DESCRIPTION), -- 15
	p.PG_TEXT,
	(p.PG_TEXT=''),
	p.PG_MNU,
	p.PG_MREF,
    p.PG_ALTREF,
	p.PG_RO
from PAGES p
left join PAGES pp on pp.PG_ID=p.PG_P0;

drop view if exists viewARTICLES;
create view viewARTICLES (
	aid,adate,fshow,title,keywords, -- 5
	description,	d_title,	d_keywords,	d_description,	css, -- 10
	caption,	uri_name,	uri_id,	short,	full,  -- 15
	href,	link,	isfull,	a_kind,	a_order -- 20
) as
select
	a.A_ID,	a.A_DATE,	not a.A_HIDDEN,	a.A_TITLE,	a.A_KEYWORDS,  -- 5
	a.A_DESCRIPTION,	IFNULL(a.A_TITLE,a.A_CAPTION),	a.A_KEYWORDS,	IFNULL(a.A_DESCRIPTION,a.A_TXT_SHORT),	a.A_CSS, -- 10	
	a.A_CAPTION,	a.A_URI,	IFNULL(a.A_URI,a.A_ID),	a.A_TXT_SHORT,a.A_TXT_FULL,	-- 15
	a.A_HREF,	a.A_LINK,	a.A_TXT_FULL is not null,	a.A_KIND, a.A_DT_PUB  -- 20
from ARTICLES a;

drop view if exists viewARTICLESshort;
create view viewARTICLESshort as
select
	aid,
	adate,
	fshow,
	title,
	d_title,
	css,
	caption,
	uri_name,
	uri_id,
	short,
	href,
	link,
	isfull,
	a_kind,
	a_order
from viewARTICLES;

drop view if exists viewORDERS;
create view viewORDERS as
	select
		o.AC_ID as 			c_id,
		o.OR_NAME as 		c_name,
		o.OR_EMAIL as 		c_email,
		o.OR_PHONE as 		c_telephone,
		o.OR_COUNTRY as 	c_country,
		o.OR_CITY as 		c_city,
		o.OR_REGION as 		c_region,
		o.OR_ADDRESS as		c_address,
		o.CTH_ID as 		shipping_id,
		h.CTH_NAME as 		shipping,
		o.CTP_ID as 		payment_id,
		p.CTP_NAME as 		payment,
		o.CTS_ID as 		status_id,
		s.CTS_NAME as 		`status`,
		o.OR_MEMO as 		message,
		o.OR_COUPON as 		coupon,
		o.OR_ID as 			num,
		o.OR_DATE as 		created,       
		sum(od.ORD_QTY*od.ORD_PRICE) as summ
	from ORDERS o
	left join ORDER_DETAILS od on od.OR_ID=o.OR_ID
	left join CT_SHIPPING h on h.CTH_ID=o.CTH_ID
	left join CT_STATUS s on s.CTS_ID=o.CTS_ID
	left join CT_PAYMENT p on p.CTP_ID=o.CTP_ID
	group by o.OR_ID;

drop view if exists viewORDER_DETAILS;
create view viewORDER_DETAILS as
	select
		od.ORD_ID as ord_id,
		od.OR_ID as or_id,
		od.P_ID as p_id,
		od.P_VARIANT as p_variant,
		od.P_CODE as p_code,
		od.P_BARCODE as p_barcode,
		od.B_ID as p_brand_id,

		od.B_NAME as p_brand_name,
		od.P_NAME as p_name,
		od.ORD_QTY as qty,
		od.ORD_PRICE as price,
		od.ORD_QTY*od.ORD_PRICE as summ
	from ORDER_DETAILS od;
	
drop view if exists viewCART_DETAILS;
create view viewCART_DETAILS as
	select
		C_ID as cid,
		CD_ID as cdid,
		CD_QTY as qty,
		CD_PRICE as price,
		P_ID as pid,
		CD_QTY*CD_PRICE as summ
	from CART_DETAILS od;

drop view if exists viewSUBSCRIBERS;
create view viewSUBSCRIBERS (
	`id`,
	`email`,
	`subscribed`,
	`ip`,
	`unsuc`,
	`broadcasted`
) as
select
	sc.SC_ID,
	sc.SC_EMAIL,
	sc.SC_DATETIME,
	sc.SC_IP,
	sc.SC_UNSUBSCRIBE,
	sc.SC_BROADCASTED
from SUBSCRIBERS sc;
	
drop view if exists viewCURRENCIES;
create view viewCURRENCIES (
	`code`,
	`name`,
	`sname`,
	`codename`,
	`format`
) as
select
	CR_CODE,
	CR_NAME,
	CR_SNAME,
	CONCAT(CR_CODE,' - ',CR_NAME),
	CR_FORMAT
from CURRENCIES;

drop view if exists viewDCURRENCIES;
create view viewDCURRENCIES (
	`num`,
	`code`,
	`name`,
	`sname`,
	`codename`,
	`format`,
	`ratio`
) as
select
	cd.DCR_NUM,
	cr.CR_CODE,
	cr.CR_NAME,
	cr.CR_SNAME,
	CONCAT(cr.CR_CODE,' - ',cr.CR_NAME),
	cr.CR_FORMAT,
	cd.DCR_RATIO
from CURRENCIES cr
inner join CURRENCIES_DISP cd on cd.CR_CODE=cr.CR_CODE
where cd.DCR_SHOW;


drop view if exists viewPRICE_TYPES;
create view viewPRICE_TYPES (
	ptid, currency_code,
	`name`, rate, mark, incr
) as
select
	pt.PT_ID, pt.CR_CODE,
	pt.PT_NAME, pt.PT_RATE, (pt.PT_MARK-1)*100, pt.PT_INCR
from PRICE_TYPES pt;

