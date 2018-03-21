replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-swreq','2011.0000');

replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-dbprog','2016.0000');

DELIMITER $$
DROP PROCEDURE  IF EXISTS sys_str2int_iT1$$
CREATE PROCEDURE sys_str2int_iT1(str varchar(1024))
begin
	declare i int default 1 ;
	declare t bigint default '';

	drop temporary table if exists iT1;
	CREATE temporary table iT1 (ID bigint) engine=memory;

	while  t is not null do
		
		set t=str_part(str,',',i); 
		if  t is not null then
			insert into iT1(ID) values (t);
		end if;
		set i=i+1;
	end while;
end$$


DELIMITER $$
DROP PROCEDURE  IF EXISTS sys_str2int_sT1$$
CREATE PROCEDURE sys_str2int_sT1(str varchar(1024))
begin
	declare i int default 1 ;
	declare t varchar(50) default '';

	drop temporary table if exists sT1;
	CREATE temporary table sT1 (STR varchar(50)) engine=memory;

	while  t is not null do
		
		set t=str_part(str,',',i); 
		if  t is not null then
			insert into sT1(STR) values (t);
		end if;
		set i=i+1;
	end while;
end$$


DELIMITER $$
DROP PROCEDURE  IF EXISTS sys_str2int_iT1_ord$$
CREATE PROCEDURE sys_str2int_iT1_ord(str varchar(1024))
begin
	declare i int default 1 ;
	declare t bigint default '';

	drop temporary table if exists iT1;
	CREATE temporary table iT1 (`ID` bigint, `ORDER` int) engine=memory;

	while  t is not null do
		
		set t=str_part(str,',',i); 
		if  t is not null then
			insert into iT1(`ID`,`ORDER`) values (t,i);
		end if;
		set i=i+1;
	end while;
end$$




DELIMITER $$
drop procedure if exists AUTH_ADM_CHECK_UID $$
create procedure AUTH_ADM_CHECK_UID (in iUID int)
begin
	select ID as UID, ROLE as PL
	from AUTH_ADM_USERS
	where ID=iUID and not BANNED;
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_CHECK_LP $$
create procedure AUTH_ADM_CHECK_LP (in iLOGIN varchar(128), in iPHASH varchar(32) character set utf8 collate utf8_bin)
begin
	select ID as UID, ROLE as PL
	from AUTH_ADM_USERS
	where EMAIL=iLOGIN and PHASH=iPHASH and not BANNED;
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_CHECK_COOKIE $$
create procedure AUTH_ADM_CHECK_COOKIE (in iUID int, in iPHASH varchar(32) character set utf8 collate utf8_bin, in iCHASH varchar(32) character set utf8 collate utf8_bin, in TTL bigint)
begin
	declare vCOOKID int;
	set vCOOKID=NULL;

	select c.ID into vCOOKID
	from AUTH_ADM_USERS u inner join AUTH_ADM_COOKIES c ON u.ID=c.UID
	where u.ID=iUID and u.PHASH=iPHASH and c.CHASH=iCHASH and not u.BANNED and c.EXPIRATION>NOW();

	update AUTH_ADM_COOKIES
	set LASTUSED=NOW(), USED=USED+1, EXPIRATION=ADDDATE(NOW(), INTERVAL TTL SECOND)
	where ID=vCOOKID;
	
	select u.ID as UID, u.ROLE as PL
	from AUTH_ADM_COOKIES c inner join AUTH_ADM_USERS u ON c.UID=u.ID
	where c.ID=vCOOKID;
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_SET_COOKIE $$
create procedure AUTH_ADM_SET_COOKIE (in iUID int, in iIP varchar(15) character set utf8 collate utf8_bin, in iCHASH varchar(32) character set utf8 collate utf8_bin, in TTL bigint)
begin
	insert into AUTH_ADM_COOKIES
	set UID=iUID, FROMIP=iIP, CHASH=iCHASH, CREATED=NOW(), EXPIRATION=ADDDATE(NOW(), INTERVAL TTL SECOND);
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_CHANGE_PWORD $$
create procedure AUTH_ADM_CHANGE_PWORD (in iUID int, in iPHASH varchar(32) character set utf8 collate utf8_bin)
begin
	update AUTH_ADM_USERS
	set PHASH=iPHASH
	where ID=iUID;
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_PREC_GET_TOKEN $$
create procedure AUTH_ADM_PREC_GET_TOKEN (in iLOGIN varchar(128) character set utf8 collate utf8_general_ci, in TTL bigint)
begin
	declare vUID int;
	declare vTOKEN varchar(32) character set utf8 collate utf8_bin;

	select ID into vUID
	from AUTH_ADM_USERS
	where EMAIL=iLOGIN;

	set vTOKEN=md5(concat(vUID,'-',unix_timestamp(now()),'-',floor(rand()*100)));

	insert into AUTH_ADM_PRECOVER set
		UID=vUID,
		TOKEN=vTOKEN,
		EXPIRATION=date_add(now(), INTERVAL TTL SECOND);

	select vUID as UID, vTOKEN as TOKEN;
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_PREC_CHECK_TOKEN $$
create procedure AUTH_ADM_PREC_CHECK_TOKEN (in iTOKEN varchar(32) character set utf8 collate utf8_bin)
begin
	delete from AUTH_ADM_PRECOVER where EXPIRATION <=NOW() limit 32;

	select t.ID as TID, u.ID as UID
	from AUTH_ADM_PRECOVER t
	inner join AUTH_ADM_USERS u on t.user=u.ID
	where t.TOKEN=iTOKEN and t.EXPIRATION > NOW();
end $$

DELIMITER $$
drop procedure if exists AUTH_ADM_PREC_KILL_TOKEN $$
create procedure AUTH_ADM_PREC_KILL_TOKEN (in iTID int)
begin
	delete from AUTH_ADM_PRECOVER where ID=iTID;
end $$


delimiter $$
DROP PROCEDURE IF EXISTS `AUTH_CUST_CHECK_COOKIE` $$
CREATE PROCEDURE `AUTH_CUST_CHECK_COOKIE`(in iUID bigint, in iCHASH varchar(32) character set utf8 collate utf8_bin, in TTL bigint)
begin
	declare vCOOKID bigint;
	set vCOOKID=NULL;

	select c.ID into vCOOKID
	from AUTH_CUST_COOKIES c
	where c.ID=iUID and c.CHASH=iCHASH and EXPIRATION>NOW();

	if vCOOKID is not null then
		update AUTH_CUST_COOKIES
		set LASTUSED=NOW(), USED=USED+1, EXPIRATION=ADDDATE(NOW(), INTERVAL TTL SECOND)
		where ID=vCOOKID;

		select vCOOKID as UID, 'PL_ANON' as PL;
	end if;
end$$

delimiter $$
DROP PROCEDURE IF EXISTS `AUTH_CUST_SET_COOKIE` $$
CREATE PROCEDURE `AUTH_CUST_SET_COOKIE`(in iIP varchar(15) character set utf8 collate utf8_bin, in iCHASH varchar(32) character set utf8 collate utf8_bin, in TTL bigint)
begin
	insert into AUTH_CUST_COOKIES
	set FROMIP=iIP, CHASH=iCHASH, CREATED=NOW(), EXPIRATION=ADDDATE(NOW(), INTERVAL TTL SECOND);

	select LAST_INSERT_ID() as UID, 'PL_ANON' as PL;
end$$

delimiter $$
DROP PROCEDURE IF EXISTS `AUTH_CUST_CHECK_UID` $$
CREATE PROCEDURE `AUTH_CUST_CHECK_UID`(in iUID bigint)
begin
	select ID as UID, 'PL_ANON' as PL
	from AUTH_CUST_COOKIES
	where ID=iUID;
end$$






DELIMITER $$
DROP PROCEDURE IF EXISTS get_cat_by_id $$
CREATE PROCEDURE get_cat_by_id (IN id BIGINT)
BEGIN
	select v.*,
		IFNULL(GROUP_CONCAT(DISTINCT sgt.SG_ID ORDER BY sgt.SG_ORDER SEPARATOR ','),'') as specs_t,
		if(v.keywords='',(
			select c.CAT_KEYWORDS
			from NODE_INCAPS i
			inner join CATEGORIES c on c.N_ID=i.N_P
			where i.N_C=v.nid and c.CAT_KEYWORDS<>''
			order by i.N_LEV_DIFF
			limit 1
		),v.keywords) as r_keywords,
		if(v.description='',(
			select c.CAT_DESCR
			from NODE_INCAPS i
			inner join CATEGORIES c on c.N_ID=i.N_P
			where i.N_C=v.nid and c.CAT_DESCR<>''
			order by i.N_LEV_DIFF
			limit 1
		),v.description) as r_description
	from viewCATEGORIES v
    left join NODE_SPEC_GROUPS nsgt on nsgt.N_ID=v.nid and nsgt.NSG_KIND='TREE'
    left join SPEC_GROUPS sgt on sgt.SG_ID=nsgt.SG_ID
	where v.nid=id
    group by v.nid;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cat_by_uri $$
CREATE PROCEDURE get_cat_by_uri (IN fvisible TINYINT, IN suri VARCHAR (255))
BEGIN
	select v.*,
		IFNULL(GROUP_CONCAT(DISTINCT sgt.SG_ID ORDER BY sgt.SG_ORDER SEPARATOR ','),'') as specs_t,
		if(v.keywords='',(
			select c.CAT_KEYWORDS
			from NODE_INCAPS i
			inner join CATEGORIES c on c.N_ID=i.N_P
			where i.N_C=v.nid and c.CAT_KEYWORDS<>''
			order by i.N_LEV_DIFF
			limit 1
		),v.keywords) as r_keywords,
		if(v.description='',(
			select c.CAT_DESCR
			from NODE_INCAPS i
			inner join CATEGORIES c on c.N_ID=i.N_P
			where i.N_C=v.nid and c.CAT_DESCR<>''
			order by i.N_LEV_DIFF
			limit 1
		),v.description) as r_description
	from viewCATEGORIES v
    left join NODE_SPEC_GROUPS nsgt on nsgt.N_ID=v.nid and nsgt.NSG_KIND='TREE'
    left join SPEC_GROUPS sgt on sgt.SG_ID=nsgt.SG_ID
	where v.uri=suri and (not fvisible or v.visible)
    group by v.nid
	limit 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cat_root $$
CREATE PROCEDURE get_cat_root ()
BEGIN
	select * from
	viewROOT;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_product_by_id $$
CREATE PROCEDURE get_product_by_id (IN id BIGINT)
BEGIN
	select v.*,
		IFNULL(GROUP_CONCAT(DISTINCT t.T_NAME SEPARATOR ', '),'') as tags,
		IFNULL(GROUP_CONCAT(DISTINCT c1.CR_ID ORDER BY c1.CR_ORDER SEPARATOR ','),'') as crits1,
		IFNULL(GROUP_CONCAT(DISTINCT c2.CR_ID ORDER BY c2.CR_ORDER SEPARATOR ','),'') as crits2,
		IFNULL(GROUP_CONCAT(DISTINCT c3.CR_ID ORDER BY c3.CR_ORDER SEPARATOR ','),'') as crits3,
        IFNULL(GROUP_CONCAT(DISTINCT sgi.SG_ID ORDER BY sgi.SG_ORDER SEPARATOR ','),'') as specs_inh
	from
	viewPRODUCTS v
	left join PRODUCT_TAGS xt on xt.P_ID=v.pid
	left join TAGS t on t.T_ID=xt.T_ID
	left join PRODUCT_CRETERIAS xc on xc.P_ID=v.pid
	left join CRITERIAS c1 on c1.CR_ID=xc.CR_ID and c1.CR_KIND='FRST'
	left join CRITERIAS c2 on c2.CR_ID=xc.CR_ID and c2.CR_KIND='SCND'
	left join CRITERIAS c3 on c3.CR_ID=xc.CR_ID and c3.CR_KIND='THRD'
    left join PRODUCT_SPEC_GROUPS psgi on psgi.P_ID=v.pid and psgi.PSG_KIND='PINH'
    left join SPEC_GROUPS sgi on sgi.SG_ID=psgi.SG_ID
	where v.pid=id
	group by v.pid
	limit 1;
END $$

delimiter $$
DROP PROCEDURE IF EXISTS get_product_by_uri $$
CREATE PROCEDURE `get_product_by_uri`(IN fvisible TINYINT, IN prnt tinyint, IN chld tinyint, IN suri VARCHAR (255))
BEGIN
	select v.*,
		IFNULL(GROUP_CONCAT(DISTINCT t.T_NAME SEPARATOR ', '),'') as tags,
		IFNULL(GROUP_CONCAT(DISTINCT c1.CR_ID ORDER BY c1.CR_ORDER SEPARATOR ','),'') as crits1,
		IFNULL(GROUP_CONCAT(DISTINCT c2.CR_ID ORDER BY c2.CR_ORDER SEPARATOR ','),'') as crits2,
		IFNULL(GROUP_CONCAT(DISTINCT c3.CR_ID ORDER BY c3.CR_ORDER SEPARATOR ','),'') as crits3,
        IFNULL(GROUP_CONCAT(DISTINCT sgi.SG_ID ORDER BY sgi.SG_ORDER SEPARATOR ','),'') as specs_inh
	from
	viewPRODUCTS v
	left join PRODUCT_TAGS xt on xt.P_ID=v.pid
	left join TAGS t on t.T_ID=xt.T_ID
	left join PRODUCT_CRETERIAS xc on xc.P_ID=v.pid
	left join CRITERIAS c1 on c1.CR_ID=xc.CR_ID and c1.CR_KIND='FRST'
	left join CRITERIAS c2 on c2.CR_ID=xc.CR_ID and c2.CR_KIND='SCND'
	left join CRITERIAS c3 on c3.CR_ID=xc.CR_ID and c3.CR_KIND='THRD'
    left join PRODUCT_SPEC_GROUPS psgi on psgi.P_ID=v.pid and psgi.PSG_KIND='PINH'
    left join SPEC_GROUPS sgi on sgi.SG_ID=psgi.SG_ID
	where cat_uri=LEFT(suri,CHAR_LENGTH(suri)-CHAR_LENGTH(SUBSTRING_INDEX(suri,'/',-1))-1)  and uri_name=SUBSTRING_INDEX(suri,'/',-1) and (not fvisible or visible)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	group by v.pid,v.v_order asc
	limit 1;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree $$
CREATE PROCEDURE get_cattree (IN fvisible TINYINT, IN maxlv INT)
BEGIN
	select * from
	viewCATEGORIESshort
	where (not fvisible or visible)
	and (maxlv is null or t_level<=maxlv)
	order by c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree_sel $$
CREATE PROCEDURE get_cattree_sel (IN fvisible TINYINT, IN suri VARCHAR (255), IN maxlv INT)
BEGIN
	select v.nid in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) as selected, v.* from
	viewCATEGORIESshort v
	where (not fvisible or v.visible)
	and (maxlv is null or v.t_level<=maxlv)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree_rt $$
CREATE PROCEDURE get_cattree_rt (IN rtid BIGINT, IN fvisible TINYINT, IN maxlv INT)
BEGIN
	select v.*
	from viewCATEGORIESshort v
	inner join NODES n on n.N_ID=v.nid
	inner join NODE_INCAPS i on i.N_C=n.N_ID and i.N_P=rtid
	where (not fvisible or v.visible)
	and (maxlv is null or v.t_level<=maxlv)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree_rt_sel $$
CREATE PROCEDURE get_cattree_rt_sel (IN rtid BIGINT, IN fvisible TINYINT, IN suri VARCHAR (255), IN maxlv INT)
BEGIN
	select v.nid in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) as selected, v.*
	from viewCATEGORIESshort v
	inner join NODES n on n.N_ID=v.nid
	inner join NODE_INCAPS i on i.N_C=n.N_ID and i.N_P=rtid
	where (not fvisible or v.visible)
	and (maxlv is null or v.t_level<=maxlv)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree_rt_sel_branch $$
CREATE PROCEDURE get_cattree_rt_sel_branch (IN rtid BIGINT, IN fvisible TINYINT, IN suri VARCHAR (255), IN maxlv INT)
BEGIN
	select v.nid in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) as selected, v.* from
	viewCATEGORIESshort v
	inner join NODES n on n.N_ID=v.nid
	inner join NODE_INCAPS i on i.N_C=n.N_ID and i.N_P=rtid
	where n.N_P0 in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) and (not fvisible or v.visible)
	and (maxlv is null or v.t_level<=maxlv)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cattree_sel_branch $$
CREATE PROCEDURE get_cattree_sel_branch (IN fvisible TINYINT, IN suri VARCHAR (255), IN maxlv INT, IN minlv INT)
BEGIN
	select v.nid in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) as selected, v.* from
	viewCATEGORIESshort v
	inner join NODES n on n.N_ID=v.nid
	where n.N_P0 in (
		select ifnull(i.N_P,t.N_ID)
		from NODE_TREE t
		left join NODE_INCAPS i on i.N_C=t.N_ID
		where t.N_URI=suri
	) and (not fvisible or v.visible)
	and (maxlv is null or v.t_level<=maxlv)
	and (minlv is null or v.t_level>=minlv)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cats_prods_all $$
CREATE PROCEDURE get_cats_prods_all (IN fvisible TINYINT,
	IN prnt tinyint,IN chld tinyint,
	IN loffset INT, IN lnum INT
	)
BEGIN
	declare cnt bigint;
	drop temporary table if exists PROD;
	create temporary table PROD(N_ID bigint,P_ID bigint,N_ORDER int,P_ORDER int,P_ORDER_CHLD int) engine=heap;
	
	if loffset is null or lnum is null then
		insert into PROD(N_ID,P_ID,N_ORDER,P_ORDER,P_ORDER_CHLD)
		select  distinct SQL_CALC_FOUND_ROWS p.N_ID,p.P_ID,nt.N_ORDER,p.P_ORDER,p.P_ORDER_CHLD 
		from PRODUCTS p
		inner join NODE_TREE nt on nt.N_ID=p.N_ID
		where (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)  and (not fvisible or (not p.P_HIDE and not nt.N_HIDE))
		order by nt.N_ORDER,p.P_ORDER,p.P_ORDER_CHLD;
	else
		insert into PROD(N_ID,P_ID,N_ORDER,P_ORDER,P_ORDER_CHLD)
		select  distinct SQL_CALC_FOUND_ROWS p.N_ID,p.P_ID,nt.N_ORDER,p.P_ORDER,p.P_ORDER_CHLD 
		from PRODUCTS p
		inner join NODE_TREE nt on nt.N_ID=p.N_ID
		where (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)  and (not fvisible or (not p.P_HIDE and not nt.N_HIDE))
		order by nt.N_ORDER,p.P_ORDER,p.P_ORDER_CHLD
		limit loffset,lnum;
	end if;
	set cnt=FOUND_ROWS();

	select distinct c.*,nt.N_ORDER
	from PROD t
	inner join NODE_INCAPS ni on ni.N_C=t.N_ID
	inner join NODE_TREE nt on nt.N_ID=ifnull(ni.N_P,ni.N_C)
	inner join viewCATEGORIES c on c.nid=ifnull(ni.N_P,ni.N_C)
	order by nt.N_ORDER;
	
	select distinct v.*,t.N_ID,t.P_ORDER,t.P_ORDER_CHLD
	from viewPRODUCTSshort v
	inner join PROD t on t.P_ID=v.pid
	order by t.N_ORDER,t.P_ORDER,t.P_ORDER_CHLD;
	select cnt;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_cats_by_parent $$
CREATE PROCEDURE get_cats_by_parent (IN fcid BIGINT,IN fvisible TINYINT,IN mselect BIGINT)
BEGIN
	select IF(mselect is null,0,(v.nid=mselect)) as selected, v.* from
	viewCATEGORIESshort v
	inner join NODES n on n.N_ID=v.nid
	where n.N_P0=fcid
	and (not fvisible or v.visible)
	order by v.c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_breadcrumbs_inc_byid $$
CREATE PROCEDURE get_breadcrumbs_inc_byid (IN id INT)
BEGIN
	(select v.* from
	NODE_INCAPS i
	inner join viewCATEGORIESshort v on v.nid=i.N_P
	where i.N_C=id
	order by i.N_CLEV)
	union
	(select v.*
	from viewCATEGORIESshort v
	where v.nid=id);
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_breadcrumbs_exc_byid $$
CREATE PROCEDURE get_breadcrumbs_exc_byid (IN id INT)
BEGIN
	select v.* from
	NODE_INCAPS i
	inner join viewCATEGORIESshort v on v.nid=i.N_P
	where i.N_C=id
	order by i.N_CLEV;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_breadcrumbs_byuri $$
CREATE PROCEDURE get_breadcrumbs_byuri (IN furi VARCHAR(255))
BEGIN
	declare id INT;

	select N_ID into id
	from NODE_TREE where N_URI=furi;

	CALL get_breadcrumbs_inc_byid(id);
END $$

delimiter $$
DROP PROCEDURE IF EXISTS get_products_by_ids $$
CREATE PROCEDURE get_products_by_ids(IN ids VARCHAR(255),IN fvisible TINYINT, IN prnt tinyint,IN chld tinyint)
BEGIN
	call sys_str2int_iT1_ord(ids);
	
	select v.*
	from viewPRODUCTSshort v
	inner join iT1 t on t.ID=v.pid
	where (not fvisible or v.fshow)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	order by t.`ORDER` asc;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products $$
CREATE PROCEDURE get_products (IN fvisible TINYINT,
	IN prnt tinyint, IN chld tinyint,
	IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select v.* from
		viewPRODUCTSshort v
		where (not fvisible or v.visible)
		and (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by v.c_order, v.p_order, v.v_order;
	else
		select SQL_CALC_FOUND_ROWS v.* from
		viewPRODUCTSshort v
		inner join (
			select p.P_ID as pid
			from PRODUCTS p
			inner join NODE_TREE t on t.N_ID=p.N_ID
			where (not fvisible or not (t.N_HIDE or p.P_HIDE))
			and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
			order by t.N_ORDER, p.P_ORDER
			limit loffset, lnum
		) s on s.pid=v.pid
		order by v.c_order, v.p_order, v.v_order;
	end if;
END $$

delimiter $$
DROP PROCEDURE IF EXISTS get_products_fil $$
CREATE PROCEDURE `get_products_fil`(IN fvisible TINYINT,
	IN fnew TINYINT, IN fspecial TINYINT, IN frecomend TINYINT,
	IN fcid INT, IN fcidinc TINYINT, IN fbrand INT, IN ftag varchar(63),
	IN fcrit text,
	IN fpricelow DOUBLE, IN fpricehi DOUBLE,
	IN prnt tinyint,IN chld tinyint,
	IN loffset INT, IN lnum INT, IN sort CHAR(4))
BEGIN
	declare cr_cnt int;
	call sys_str2int_iT1(fcrit);
	
	drop temporary table if exists CR_PROD;
	create temporary table CR_PROD(P_ID bigint) engine=heap;
	select count(distinct ID) into cr_cnt from iT1;
	
	insert into CR_PROD(P_ID) 
	select P_ID
	from PRODUCT_CRETERIAS pc
	inner join iT1 t on t.ID=pc.CR_ID
	group by pc.P_ID
	having count(distinct CR_ID)=cr_cnt;

	if cr_cnt=0 then 
		insert into CR_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists TG_PROD;
	create temporary table TG_PROD(P_ID bigint) engine=heap;
	
	insert into TG_PROD(P_ID) 
	select distinct P_ID
	from PRODUCT_TAGS pt
	inner join TAGS t on t.T_ID=pt.T_ID
	where ftag=t.T_NAME;

	if ftag is null then 
		insert into TG_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists CT_N;
	create temporary table CT_N(N_ID bigint) engine=heap;
	
	if fcidinc=1 then
		insert into CT_N(N_ID) 
		select distinct ni.N_C
		from NODE_INCAPS ni
		where ifnull(N_P,N_C)=fcid;
	else
		insert into CT_N(N_ID) 
		select distinct n.N_ID
		from NODES n
		where n.N_ID=fcid;
	end if;

	if fcid is null then 
		insert into CT_N(N_ID)
		select 0;
	end if;

	if loffset is null or lnum is null then 
		if sort in ('NAME','PRCA','DTCA') or sort is null then
			select  distinct SQL_CALC_FOUND_ROWS v.* from
			viewPRODUCTSshort v 
			inner join NODE_TREE nt on nt.N_ID=v.nid
			inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=v.pid
			inner join TG_PROD s3 on ftag is null or s3.P_ID=v.pid
			inner join CT_N s4 on fcid is null or s4.N_ID=v.nid
			where 
				(not fvisible or v.visible)
			and (not fnew or v.fnew)
			and (not fspecial or v.fspecial)
			and (not frecomend or v.frecomend)
			and (fbrand is null or v.brand_id=fbrand)
			and (fpricelow is null or v.price>=fpricelow)
			and (fpricehi is null or v.price<=fpricehi)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			order by 
				case sort 
					when 'NAME' then v.`name`
					when 'PRCA' then concat(lpad(floor(v.price_salebase), 12, '0'),floor(mod(v.price_salebase*10000,10000)))
                    when 'DTCA' then cast(v.created as char)
					when null 	then nt.N_ORDER end
				asc,
				v.p_order asc,v.v_order asc
			;
		end if;
		if sort in ('PRCD','DTCD') then
			select  distinct SQL_CALC_FOUND_ROWS v.* from
			viewPRODUCTSshort v
			inner join NODE_TREE nt on nt.N_ID=v.nid
			inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=v.pid
			inner join TG_PROD s3 on ftag is null or s3.P_ID=v.pid
			inner join CT_N s4 on fcid is null or s4.N_ID=v.nid
			where 
				(not fvisible or v.visible)
			and (not fnew or v.fnew)
			and (not fspecial or v.fspecial)
			and (not frecomend or v.frecomend)
			and (fbrand is null or v.brand_id=fbrand)
			and (fpricelow is null or v.price>=fpricelow)
			and (fpricehi is null or v.price<=fpricehi)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			order by 
				case sort 
					when 'PRCD' then concat(lpad(floor(v.price_salebase), 12, '0'),floor(mod(v.price_salebase*10000,10000)))
                    when 'DTCD' then cast(v.created as char)
				end desc,
				v.p_order asc,v.v_order asc
			;
		end if;	
	
	else
		if sort in ('NAME','PRCA','DTCA') or sort is null then
			select  distinct SQL_CALC_FOUND_ROWS v.* from
			viewPRODUCTSshort v 
			inner join NODE_TREE nt on nt.N_ID=v.nid
			inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=v.pid
			inner join TG_PROD s3 on ftag is null or s3.P_ID=v.pid
			inner join CT_N s4 on fcid is null or s4.N_ID=v.nid
			where 
				(not fvisible or v.visible)
			and (not fnew or v.fnew)
			and (not fspecial or v.fspecial)
			and (not frecomend or v.frecomend)
			and (fbrand is null or v.brand_id=fbrand)
			and (fpricelow is null or v.price>=fpricelow)
			and (fpricehi is null or v.price<=fpricehi)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			order by 
				case sort 
					when 'NAME' then v.`name`
					when 'PRCA' then concat(lpad(floor(v.price_salebase), 12, '0'),floor(mod(v.price_salebase*10000,10000)))
                    when 'DTCA' then cast(v.created as char)
					when null 	then nt.N_ORDER end
				asc,
				v.p_order asc,v.v_order asc
			limit loffset,lnum
			;
		end if;
		if sort in ('PRCD','DTCD')  then
			select  distinct SQL_CALC_FOUND_ROWS v.* from
			viewPRODUCTSshort v
			inner join NODE_TREE nt on nt.N_ID=v.nid
			inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=v.pid
			inner join TG_PROD s3 on ftag is null or s3.P_ID=v.pid
			inner join CT_N s4 on fcid is null or s4.N_ID=v.nid
			where 
				(not fvisible or v.visible)
			and (not fnew or v.fnew)
			and (not fspecial or v.fspecial)
			and (not frecomend or v.frecomend)
			and (fbrand is null or v.brand_id=fbrand)
			and (fpricelow is null or v.price>=fpricelow)
			and (fpricehi is null or v.price<=fpricehi)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			order by 
				case sort 
					when 'PRCD' then concat(lpad(floor(v.price_salebase), 12, '0'),floor(mod(v.price_salebase*10000,10000)))
                    when 'DTCD' then cast(v.created as char)
				end desc,
				v.p_order asc,
				v.v_order asc
			limit loffset,lnum
			;
		end if;
	end if;
	select FOUND_ROWS() as cnt;
	
	drop temporary table iT1;
end $$

delimiter $$
DROP PROCEDURE IF EXISTS get_products_by_cat $$
CREATE PROCEDURE `get_products_by_cat`(IN fcid BIGINT,IN fvisible TINYINT,
	IN prnt tinyint,IN chld tinyint,
	IN loffset INT, IN lnum INT, IN sort CHAR(4))
BEGIN
	if loffset is null or lnum is null then
		if sort is null then
			select * from
			viewPRODUCTSshort
			where nid=fcid
			and (not fvisible or fshow)
			and (prnt=1 and not inherited or chld=1 and inherited)
			order by p_order,v_order asc;
		else
			case sort
			when 'NAME' then
				select * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by name,v_order asc;
			when 'PRCA' then
				select * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by price_salebase;
			when 'PRCD' then
				select * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by price_salebase desc;
			when 'DTCA' then
				select * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by created;
			when 'DTCD' then
				select * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by created desc;
			end case;
		end if;
	else
		if sort is null then
			select SQL_CALC_FOUND_ROWS * from
			viewPRODUCTSshort
			where nid=fcid
			and (not fvisible or fshow)
			and (prnt=1 and not inherited or chld=1 and inherited)
			order by p_order,v_order asc
			limit loffset, lnum;
		else
			case sort
			when 'NAME' then
				select SQL_CALC_FOUND_ROWS * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by name,v_order asc
				limit loffset, lnum;
			when 'PRCA' then
				select SQL_CALC_FOUND_ROWS * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by price
				limit loffset, lnum;
			when 'PRCD' then
				select SQL_CALC_FOUND_ROWS * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by price_salebase desc
				limit loffset, lnum;
			when 'DTCA' then
				select SQL_CALC_FOUND_ROWS * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by created
				limit loffset, lnum;
			when 'DTCD' then
				select SQL_CALC_FOUND_ROWS * from
				viewPRODUCTSshort
				where nid=fcid
				and (not fvisible or fshow)
				and (prnt=1 and not inherited or chld=1 and inherited)
				order by created desc
				limit loffset, lnum;
			end case;
		end if;
		select FOUND_ROWS() as cnt;
	end if;
END$$

delimiter $$
DROP PROCEDURE IF EXISTS get_products_by_cat_inc $$
CREATE PROCEDURE `get_products_by_cat_inc`(IN fcid BIGINT,IN fvisible TINYINT,
	IN prnt tinyint,IN chld tinyint,
	IN loffset INT, IN lnum INT, IN sort CHAR(4))
BEGIN
	declare cn bigint;
	if loffset is null or lnum is null then
		if sort is null then
			select v.* from
			viewPRODUCTSshort v
			inner join NODE_INCAPS i on i.N_C=v.nid
			where (v.nid=fcid or i.N_P=fcid)
			and (not fvisible or v.visible)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			group by v.pid
			order by v.c_order, v.p_order,v.v_order asc;
		else
			case sort
			when 'NAME' then
				select v.* from
				viewPRODUCTSshort v
				inner join NODE_INCAPS i on i.N_C=v.nid
				where (v.nid=fcid or i.N_P=fcid)
				and (not fvisible or v.visible)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				group by v.pid
				order by v.name,v.v_order asc;
			when 'PRCA' then
				select v.* from
				viewPRODUCTSshort v
				inner join NODE_INCAPS i on i.N_C=v.nid
				where (v.nid=fcid or i.N_P=fcid)
				and (not fvisible or v.visible)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				group by v.pid
				order by v.price_salebase;
			when 'PRCD' then
				select v.* from
				viewPRODUCTSshort v
				inner join NODE_INCAPS i on i.N_C=v.nid
				where (v.nid=fcid or i.N_P=fcid)
				and (not fvisible or v.visible)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				group by v.pid
				order by v.price_salebase desc;
			when 'DTCA' then
				select v.* from
				viewPRODUCTSshort v
				inner join NODE_INCAPS i on i.N_C=v.nid
				where (v.nid=fcid or i.N_P=fcid)
				and (not fvisible or v.visible)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				group by v.created
				order by v.price;
			when 'DTCD' then
				select v.* from
				viewPRODUCTSshort v
				inner join NODE_INCAPS i on i.N_C=v.nid
				where (v.nid=fcid or i.N_P=fcid)
				and (not fvisible or v.visible)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				group by v.pid
				order by v.created desc;
			end case;
		end if;
	else
		drop temporary table if exists pd;
		create temporary table pd(pid bigint) engine=heap;

		if sort is null then
			insert into pd(pid)
			select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
			from PRODUCTS p
			inner join NODE_TREE t on t.N_ID=p.N_ID
			inner join NODE_INCAPS i on i.N_C=p.N_ID
			where (t.N_ID=fcid or i.N_P=fcid)
			and (not fvisible or not (t.N_HIDE or p.P_HIDE))
			and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
			order by t.N_ORDER, p.P_ORDER
			limit loffset, lnum;
			
			set cn=FOUND_ROWS();
			select SQL_CALC_FOUND_ROWS v.* from
			viewPRODUCTSshort v
			inner join pd s on s.pid=v.pid
			order by v.c_order, v.p_order,v.v_order asc;
			select cn as cnt;
		else
			case sort
				when 'NAME' then
				insert into pd(pid)
				select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
				from PRODUCTS p
				inner join NODE_TREE t on t.N_ID=p.N_ID
				inner join NODE_INCAPS i on i.N_C=p.N_ID
				where (t.N_ID=fcid or i.N_P=fcid)
				and (not fvisible or not (t.N_HIDE or p.P_HIDE))
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.P_NAME
				limit loffset, lnum;
				
				set cn=FOUND_ROWS();
				select SQL_CALC_FOUND_ROWS v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.name;
				select cn as cnt;
			when 'PRCA' then
				insert into pd(pid)
				select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
				from PRODUCTS p
				inner join viewPRODUCTS_PRICE pp on pp.pp_pid=p.P_ID
				inner join NODE_TREE t on t.N_ID=p.N_ID
				inner join NODE_INCAPS i on i.N_C=p.N_ID
				where (t.N_ID=fcid or i.N_P=fcid)
				and (not fvisible or not (t.N_HIDE or p.P_HIDE))
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by pp.pp_salebase
				limit loffset, lnum;
				
				set cn=FOUND_ROWS();
				select SQL_CALC_FOUND_ROWS v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.price_salebase;
				select cn as cnt;
			when 'PRCD' then
				insert into pd(pid)
				select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
				from PRODUCTS p
				inner join viewPRODUCTS_PRICE pp on pp.pp_pid=p.P_ID
				inner join NODE_TREE t on t.N_ID=p.N_ID
				inner join NODE_INCAPS i on i.N_C=p.N_ID
				where (t.N_ID=fcid or i.N_P=fcid)
				and (not fvisible or not (t.N_HIDE or p.P_HIDE))
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by pp.pp_salebase desc
				limit loffset, lnum;
				
				set cn=FOUND_ROWS();
				select SQL_CALC_FOUND_ROWS v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.price_salebase desc;
				select cn as cnt;
			when 'DTCA' then
				insert into pd(pid)
				select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
				from PRODUCTS p
				inner join NODE_TREE t on t.N_ID=p.N_ID
				inner join NODE_INCAPS i on i.N_C=p.N_ID
				where (t.N_ID=fcid or i.N_P=fcid)
				and (not fvisible or not (t.N_HIDE or p.P_HIDE))
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.D_CREATE
				limit loffset, lnum;
				
				set cn=FOUND_ROWS();
				select SQL_CALC_FOUND_ROWS v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.created;
				select cn as cnt;
			when 'DTCD' then
				insert into pd(pid)
				select SQL_CALC_FOUND_ROWS distinct p.P_ID as pid
				from PRODUCTS p
				inner join NODE_TREE t on t.N_ID=p.N_ID
				inner join NODE_INCAPS i on i.N_C=p.N_ID
				where (t.N_ID=fcid or i.N_P=fcid)
				and (not fvisible or not (t.N_HIDE or p.P_HIDE))
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.D_CREATE desc
				limit loffset, lnum;
				
				set cn=FOUND_ROWS();
				select SQL_CALC_FOUND_ROWS v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.created desc;
				select cn as cnt;
			end case;
		end if;
	end if;
END$$

delimiter $$
DROP PROCEDURE IF EXISTS get_products_rand $$
CREATE PROCEDURE `get_products_rand`(IN fvisible TINYINT,
	IN prnt tinyint,IN chld tinyint,
	IN fnew TINYINT, IN frecomend TINYINT, IN fspecial TINYINT,  
	IN num INT)
BEGIN 
	select v.* from
	viewPRODUCTSshort v
	inner join (
		select P_ID from PRODUCTS p
		inner join NODE_TREE t on t.N_ID=p.N_ID
		where (not fvisible or not (t.N_HIDE or p.P_HIDE))
		and (not fnew or p.IS_NEW)
		and (not frecomend or p.IS_RECOMEND)
		and (not fspecial or p.IS_SPECIAL)
		and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
		order by RAND()
		limit num
	) r on r.P_ID=v.pid;
END$$

delimiter $$

DROP PROCEDURE IF EXISTS get_products_hidden $$
CREATE PROCEDURE `get_products_hidden`(IN prnt tinyint,IN chld tinyint,IN loffset INT, IN lnum INT, IN sort CHAR(4))
BEGIN
	declare cn bigint;

	if not (loffset is null or lnum is null) then
		drop temporary table if exists pd;
		create temporary table pd(pid bigint,pord bigint default null) engine=heap;
	end if;

	if loffset is null or lnum is null then
		if sort is null then
			select distinct v.* from
			viewPRODUCTSshort v
			where (not v.fshow)
			and (prnt=1 and not v.inherited or chld=1 and v.inherited)
			order by v.c_order, v.p_order,v.v_order asc;
		else
			case sort
			when 'NAME' then
				select distinct v.* from
				viewPRODUCTSshort v
				where (not v.fshow)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				order by v.name,v.v_order asc;
			when 'PRCA' then
				select distinct v.* from
				viewPRODUCTSshort v
				where (not v.fshow)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				order by v.price,v.v_order asc;
			when 'PRCD' then
				select distinct v.* from
				viewPRODUCTSshort v 
				where (not v.fshow)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				order by v.price desc,v.v_order asc;
			when 'DTCA' then
				select distinct v.* from
				viewPRODUCTSshort v
				where (not v.fshow)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				order by v.created,v.v_order asc;
			when 'DTCD' then
				select distinct v.* from
				viewPRODUCTSshort v 
				where (not v.fshow)
				and (prnt=1 and not v.inherited or chld=1 and v.inherited)
				order by v.created desc,v.v_order asc;
			end case;
		end if;
	else
		if sort is null then
			insert into pd(pid)
			select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
			from PRODUCTS p
			inner join NODE_TREE t on t.N_ID=p.N_ID
			where (p.P_HIDE)
			and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
			order by t.N_ORDER, p.P_ORDER,p.P_ORDER_CHLD
			limit loffset, lnum;

			set cn=FOUND_ROWS();

			select  v.* from
			viewPRODUCTSshort v
			inner join pd s on s.pid=v.pid
			order by v.c_order, v.p_order,v.v_order asc;

			select cn as cnt;
		else
			case sort
			when 'NAME' then
				insert into pd(pid)
				select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
				from PRODUCTS p
				where (p.P_HIDE)
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.P_NAME,p.P_ORDER_CHLD
				limit loffset, lnum;

				set cn=FOUND_ROWS();

				select  v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.name,v.v_order asc;

				select cn as cnt;
			when 'PRCA' then
				insert into pd(pid)
				select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
				from PRODUCTS p
				where (p.P_HIDE)
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.P_PRICE,p.P_ORDER_CHLD
				limit loffset, lnum;

				set cn=FOUND_ROWS();

				select  v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.price,v.v_order asc;

				select cn as cnt;
			when 'PRCD' then
				insert into pd(pid)
				select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
				from PRODUCTS p
				where (p.P_HIDE)
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.P_PRICE desc,p.P_ORDER_CHLD
				limit loffset, lnum;

				set cn=FOUND_ROWS();

				select  v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.price desc,v.v_order asc;

				select cn as cnt;
			when 'DTCA' then
				insert into pd(pid)
				select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
				from PRODUCTS p
				where (p.P_HIDE)
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.D_CREATE,p.P_ORDER_CHLD
				limit loffset, lnum;

				set cn=FOUND_ROWS();

				select  v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.created,v.v_order asc;

				select cn as cnt;
			when 'DTCD' then
				insert into pd(pid)
				select distinct SQL_CALC_FOUND_ROWS p.P_ID as pid
				from PRODUCTS p
				where (p.P_HIDE)
				and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
				order by p.D_CREATE desc,p.P_ORDER_CHLD
				limit loffset, lnum;

				set cn=FOUND_ROWS();

				select  v.* from
				viewPRODUCTSshort v
				inner join pd s on s.pid=v.pid
				order by v.created desc,v.v_order asc;

				select cn as cnt;
			end case;
		end if;
	end if;	
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products_linked_slaves $$
CREATE PROCEDURE get_products_linked_slaves(IN `kind` CHAR(4), IN `mid` BIGINT, IN fvisible TINYINT, IN prnt tinyint, IN chld tinyint, IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select v.*
		from PRODUCTS_LINKS l
		inner join viewPRODUCTSshort v on v.pid=l.P_ID_S
		where l.PL_KIND=`kind` and l.P_ID_M=`mid`
		and (not fvisible or v.fshow)
		and (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by v.pid;
	else
		select v.*
		from PRODUCTS_LINKS l
		inner join viewPRODUCTSshort v on v.pid=l.P_ID_S
		where l.PL_KIND=`kind` and l.P_ID_M=`mid`
		and (not fvisible or v.fshow)
		and (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by v.pid
		limit loffset, lnum;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products_linked_slaves_rnd $$
CREATE PROCEDURE get_products_linked_slaves_rnd(IN `kind` CHAR(4), IN `mid` BIGINT, IN fvisible TINYINT, IN prnt tinyint, IN chld tinyint, IN num INT)
BEGIN
	select v.*
	from PRODUCTS_LINKS l
	inner join viewPRODUCTSshort v on v.pid=l.P_ID_S
	where l.PL_KIND=`kind` and l.P_ID_M=`mid`
	and (not fvisible or v.fshow)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	order by rand()
	limit num;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products_linked_cross $$
CREATE PROCEDURE get_products_linked_cross(IN `kind` CHAR(4), IN `id` BIGINT, IN fvisible TINYINT, IN prnt tinyint,IN chld tinyint, IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select v.*
		from (
			select l.P_ID_S
			from PRODUCTS_LINKS l
			where l.PL_KIND=`kind` and l.P_ID_M=`id`
	
			union distinct
	
			select l.P_ID_M
			from PRODUCTS_LINKS l
			where l.PL_KIND=`kind` and l.P_ID_S=`id`
		) s
		inner join viewPRODUCTSshort v on v.pid=s.P_ID
		and (not fvisible or v.fshow)
		and (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by v.pid;
	else
		select v.*
		from (
			select l.P_ID_S
			from PRODUCTS_LINKS l
			where l.PL_KIND=`kind` and l.P_ID_M=`id`
	
			union distinct
	
			select l.P_ID_M
			from PRODUCTS_LINKS l
			where l.PL_KIND=`kind` and l.P_ID_S=`id`
		) s
		inner join viewPRODUCTSshort v on v.pid=s.P_ID
		and (not fvisible or v.fshow)
		and (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by v.pid
		limit loffset, lnum;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products_linked_cross_rnd $$
CREATE PROCEDURE get_products_linked_cross_rnd(IN `kind` CHAR(4), IN `id` BIGINT, IN fvisible TINYINT, IN prnt tinyint,IN chld tinyint, IN num INT)
BEGIN
	select v.*
	from (
		select l.P_ID_S
		from PRODUCTS_LINKS l
		where l.PL_KIND=`kind` and l.P_ID_M=`id`

		union distinct

		select l.P_ID_M
		from PRODUCTS_LINKS l
		where l.PL_KIND=`kind` and l.P_ID_S=`id`
	) s
	inner join viewPRODUCTSshort v on v.pid=s.P_ID
	and (not fvisible or v.fshow)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	order by rand()
	limit num;
END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS products_link_add $$
CREATE PROCEDURE products_link_add(IN `kind` CHAR(4), IN `mid` BIGINT, IN `sid` BIGINT)
BEGIN
	if (not exists (
		select 1
		from PRODUCTS_LINKS
		where
			PL_KIND=`kind` and
			P_ID_M=`mid` and
			P_ID_S=`sid`
	)) then
		insert into PRODUCTS_LINKS
		set
			PL_KIND=`kind`,
			P_ID_M=`mid`,
			P_ID_S=`sid`;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS products_link_delete $$
CREATE PROCEDURE products_link_delete(IN `kind` CHAR(4), IN `mid` BIGINT, IN `sid` BIGINT)
BEGIN
	delete from PRODUCTS_LINKS
	where
		PL_KIND=`kind` and
		P_ID_M=`mid` and
		P_ID_S=`sid`;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_product_inheritance $$
CREATE PROCEDURE get_product_inheritance(in ipid bigint)
BEGIN
	select *
	from viewPRODUCTS_INH
	where pid=ipid;
END$$

delimiter $$
DROP PROCEDURE IF EXISTS get_product_variants $$
CREATE PROCEDURE get_product_variants(in acl_uid bigint, in ipid bigint,
	in ivisible varchar(255),in iprnt tinyint,in ichld tinyint
)
BEGIN 
	declare p bigint;
	select ifnull(P_P0,P_ID) into p from PRODUCTS where ipid in (P_ID,P_P0) group by ifnull(P_P0,P_ID);
	
	select v.*,
		IFNULL(GROUP_CONCAT(DISTINCT t.T_NAME SEPARATOR ', '),'') as tags,
		IFNULL(GROUP_CONCAT(DISTINCT c1.CR_ID ORDER BY c1.CR_ORDER SEPARATOR ','),'') as crits1,
		IFNULL(GROUP_CONCAT(DISTINCT c2.CR_ID ORDER BY c2.CR_ORDER SEPARATOR ','),'') as crits2
	from
	viewPRODUCTS v
	left join PRODUCT_TAGS xt on xt.P_ID=v.pid
	left join TAGS t on t.T_ID=xt.T_ID
	left join PRODUCT_CRETERIAS xc on xc.P_ID=v.pid
	left join CRITERIAS c1 on c1.CR_ID=xc.CR_ID and c1.CR_KIND='FRST'
	left join CRITERIAS c2 on c2.CR_ID=xc.CR_ID and c2.CR_KIND='SCND'
	where p in (v.parent_id,v.pid)
	and (iprnt=1 and v.pid=p or ichld=1 and v.parent_id=p)
	and (ivisible=0 or v.visible=1)
	group by v.pid
	order by cast(v.inherited as unsigned) desc,v.v_order;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS `prep_code` $$
CREATE FUNCTION `prep_code`(str VARCHAR(255))
	RETURNS varchar(255) CHARSET utf8
BEGIN
	DECLARE n INT;
	DECLARE res VARCHAR(128);
	DECLARE ch CHAR(4);

	IF str IS NULL THEN
		RETURN null;
	END IF;

	SET n=CHAR_LENGTH(str);
	SET res="";
	WHILE n>0 DO
		SET ch=SUBSTRING(str,n,1);
		
		SET ch=CASE ch


		WHEN "0" THEN "0"
		WHEN "1" THEN "1"
		WHEN "2" THEN "2"
		WHEN "3" THEN "3"
		WHEN "4" THEN "4"
		WHEN "5" THEN "5"
		WHEN "6" THEN "6"
		WHEN "7" THEN "7"
		WHEN "8" THEN "8"
		WHEN "9" THEN "9"

		WHEN "a" THEN "a"
		WHEN "b" THEN "b"
		WHEN "c" THEN "c"
		WHEN "d" THEN "d"
		WHEN "e" THEN "e"
		WHEN "f" THEN "f"
		WHEN "g" THEN "g"
		WHEN "h" THEN "h"
		WHEN "i" THEN "i"
		WHEN "j" THEN "j"
		WHEN "k" THEN "k"
		WHEN "l" THEN "l"
		WHEN "m" THEN "m"
		WHEN "n" THEN "n"
		WHEN "o" THEN "o"
		WHEN "p" THEN "p"
		WHEN "q" THEN "q"
		WHEN "r" THEN "r"
		WHEN "s" THEN "s"
		WHEN "t" THEN "t"
		WHEN "u" THEN "u"
		WHEN "v" THEN "v"
		WHEN "w" THEN "w"
		WHEN "x" THEN "x"
		WHEN "y" THEN "y"
		WHEN "z" THEN "z"

		WHEN "а" THEN "a"
		WHEN "в" THEN "b"
		WHEN "е" THEN "e"
		WHEN "ё" THEN "e"
		WHEN "з" THEN "3"
		WHEN "к" THEN "k"
		WHEN "м" THEN "m"
		WHEN "н" THEN "h"
		WHEN "о" THEN "o"
		WHEN "р" THEN "p"
		WHEN "с" THEN "c"
		WHEN "т" THEN "t"
		WHEN "у" THEN "y"
		WHEN "х" THEN "x"

		WHEN "і" THEN "i"

		ELSE "" END;

		SET res=CONCAT(ch,res);
		SET n=n-1;
	END WHILE;

	RETURN res;
END$$




delimiter $$
DROP PROCEDURE IF EXISTS get_products_by_search $$
CREATE PROCEDURE `get_products_by_search`(IN prnt tinyint,IN chld tinyint,IN sa TEXT, IN loffset INT, IN lnum INT)
BEGIN
	declare i int;
	declare part varchar(255);
	declare ft_srch text;
	declare s_srch text;
	declare pcnt int;

	drop temporary table if exists SS;
	create temporary table SS (idx int, str varchar(255)) engine=heap;

	drop temporary table if exists SR;
	create temporary table SR (pid bigint, kind char(4), rel double) engine=heap;

	select ifnull(count(*),0) into pcnt from PRODUCTS;

	set i=1;
	repeat
		set part=str_part_trim(sa,' ',i);
		if (part is not null and part<>'') then
			insert into SS set idx=i, str=part;
		end if;
		set i=i+1;
	until i=100
	end repeat;

	select GROUP_CONCAT(CONCAT('+',str) order by idx separator ' ') into ft_srch from SS;
	select GROUP_CONCAT(str order by idx separator ' ') into s_srch from SS;

	insert into SS
	set str=s_srch;

	insert into SR (pid, kind, rel)
	select p.P_ID, 'CODE', 100
	from PRODUCTS p
	inner join (select prep_code(sb.str) as str from SS sb) s on s.str=p.P_SRCH_CODE and s.str<>'';

	if pcnt<=25000 then
		insert into SR (pid, kind, rel)
		select t.`ID`, 'NAME', 50
		from TEXTS t
		where t.TX_TYPE='PNAM'
		and t.TX_TEXT like concat('%',s_srch,'%');
	end if;

	insert into SR (pid, kind, rel)
	select p.P_ID, 'CRIT', 10
	from PRODUCTS p
	inner join PRODUCT_CRETERIAS cx on cx.P_ID=p.P_ID
	inner join CRITERIAS c on c.CR_ID=cx.CR_ID
	where locate(c.CR_NAME,s_srch);

	insert into SR (pid, kind, rel)
	select p.P_ID, 'BRND', 10
	from PRODUCTS p
	inner join BRANDS b on b.B_ID=p.B_ID
	where locate(b.B_NAME,s_srch);

	/*insert into SR (pid, kind, rel)
	select p.P_ID, 'CRIT', 10
	from PRODUCTS p
	inner join PRODUCT_CRETERIAS cx on cx.P_ID=p.P_ID
	inner join CRITERIAS c on c.CR_ID=cx.CR_ID
	inner join SS s on locate(s.str,c.CR_NAME);

	insert into SR (pid, kind, rel)
	select p.P_ID, 'CRIT', 10
	from PRODUCTS p
	inner join BRANDS b on b.B_ID=p.B_ID
	inner join SS s on locate(s.str,b.B_NAME);*/

	insert into SR (pid, kind, rel)
	select t.ID, 'PNAM', (match(t.TX_TEXT) against (ft_srch in boolean mode))*5
	from TEXTS t
	where t.TX_TYPE='PNAM' and (match(t.TX_TEXT) against (ft_srch in boolean mode));

	if ifnull((select sum(rel) from SR),0) < 2 then 
		insert into SR (pid, kind, rel)
		select t.ID, 'PDSC', (match(t.TX_TEXT) against (ft_srch in boolean mode))*1
		from TEXTS t
		where t.TX_TYPE='PDSC' and (match(t.TX_TEXT) against (ft_srch in boolean mode));
	end if;

	if loffset is null or lnum is null then
		select v.*,
			s.rel as srch_rel, s.kinds as srch_kinds
		from viewPRODUCTSshort v
		inner join (
			select pid, sum(rel) as rel,
				group_concat(distinct kind order by rel desc separator ',') as kinds
			from SR
			group by pid
		) s on s.pid=v.pid
		where (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by s.rel desc,v.v_order asc;
	else
		select v.*,
			s.rel as srch_rel, s.kinds as srch_kinds
		from viewPRODUCTSshort v
		inner join (
			select pid, sum(rel) as rel,
				group_concat(distinct kind order by rel desc separator ',') as kinds
			from SR
			group by pid
		) s on s.pid=v.pid
		where (prnt=1 and not v.inherited or chld=1 and v.inherited)
		order by s.rel desc,v.v_order asc
		limit loffset, lnum;

		select FOUND_ROWS() as cnt;
	end if;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS pict_ext_by_type $$
CREATE FUNCTION pict_ext_by_type (ptype CHAR(4))
RETURNS varchar(16)
BEGIN
	return case ptype
	when 'JPEG' then '.jpg'
	when 'PNG' then '.png'
	when 'GIF' then '.gif'
	else '.jpg'
	end;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_products_picts $$
CREATE PROCEDURE get_products_picts (IN pid BIGINT)
BEGIN
	select
		pp.PP_N as pict_num,
		concat(p.P_PICT_URI,'_',pp.PP_N,pict_ext_by_type(pp.PP_TYPE)) as pict_uri,
		pp.PP_TYPE as pict_type,
		pp.PP_NAME as pict_name
	from PRODUCTS p
	left join PRODUCT_PICTS pp on pp.P_ID=p.P_ID
	where p.P_ID=pid and p.P_PICT_URI is not null
	order by pp.PP_N;
END $$


# ----------------------------

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_avail $$
CREATE PROCEDURE rb_get_itm_avail (in id int)
BEGIN
	select PV_ID as `id`, PV_NAME as `name`
	from P_AVAIL where PV_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_avail $$
CREATE PROCEDURE rb_get_list_avail ()
BEGIN
	select PV_ID as `id`, PV_NAME as `name`
	from P_AVAIL order by PV_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_brands $$
CREATE PROCEDURE rb_get_itm_brands (in id int)
BEGIN
	select B_ID as `id`, B_NAME as `name`
	from BRANDS where B_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_brands $$
CREATE PROCEDURE rb_get_list_brands ()
BEGIN
	select B_ID as `id`, B_NAME as `name`
	from BRANDS
	order by if(cast(replace(B_NAME,',','.') as decimal(7,2))=0,100000,cast(replace(B_NAME,',','.') as decimal(7,2))), B_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_shipping $$
CREATE PROCEDURE rb_get_itm_shipping (in id int)
BEGIN
	select CTH_ID as `id`, CTH_NAME as `name`
	from CT_SHIPPING where CTH_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_shipping $$
CREATE PROCEDURE rb_get_list_shipping ()
BEGIN
	select CTH_ID as `id`, CTH_NAME as `name`
	from CT_SHIPPING where not CTH_VOID order by CTH_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_payment $$
CREATE PROCEDURE rb_get_itm_payment (in id int)
BEGIN
	select CTP_ID as `id`, CTP_NAME as `name`
	from CT_PAYMENT where CTP_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_payment $$
CREATE PROCEDURE rb_get_list_payment ()
BEGIN
	select CTP_ID as `id`, CTP_NAME as `name`
	from CT_PAYMENT where not CTP_VOID order by CTP_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_status $$
CREATE PROCEDURE rb_get_itm_status (in id int)
BEGIN
	select CTS_ID as `id`, CTS_NAME as `name`
	from CT_STATUS where CTS_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_status $$
CREATE PROCEDURE rb_get_list_status ()
BEGIN
	select CTS_ID as `id`, CTS_NAME as `name`
	from CT_STATUS where not CTS_VOID order by CTS_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_criterias1 $$
CREATE PROCEDURE rb_get_itm_criterias1 (in id int)
BEGIN
	call rb_get_itm_criterias(id,'FRST');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_criterias2 $$
CREATE PROCEDURE rb_get_itm_criterias2 (in id int)
BEGIN
	call rb_get_itm_criterias(id,'SCND');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_criterias3 $$
CREATE PROCEDURE rb_get_itm_criterias3 (in id int)
BEGIN
	call rb_get_itm_criterias(id,'THRD');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_criterias $$
CREATE PROCEDURE rb_get_itm_criterias (in id int,in kind varchar(4))
BEGIN
	select CR_ID as `id`, CR_NAME as `name`
	from CRITERIAS
	where CR_ID=id and CR_KIND=kind;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_itm_criteriasall $$
CREATE PROCEDURE rb_get_itm_criteriasall (in id int)
BEGIN
	select CR_ID as `id`, CR_NAME as `name`
	from CRITERIAS
	where CR_ID=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_criterias1 $$
CREATE PROCEDURE rb_get_list_criterias1 ()
BEGIN
	call rb_get_criterias('FRST');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_criterias2 $$
CREATE PROCEDURE rb_get_list_criterias2 ()
BEGIN
	call rb_get_criterias('SCND');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_list_criterias3 $$
CREATE PROCEDURE rb_get_list_criterias3 ()
BEGIN
	call rb_get_criterias('THRD');
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_criterias $$
CREATE PROCEDURE rb_get_criterias (IN kind CHAR(4))
BEGIN
	select CR_ID as `id`, CR_NAME as `name`
	from CRITERIAS
	where CR_KIND=kind
	order by CR_ORDER;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_criterias $$
CREATE PROCEDURE get_criterias (IN kind CHAR(4))
BEGIN
	call rb_get_criterias(kind);
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_prod_criterias $$
CREATE PROCEDURE rb_get_prod_criterias (IN pid BIGINT, IN kind CHAR(4))
BEGIN
	select c.CR_ID as `id`, c.CR_NAME as `name`
	from CRITERIAS c
	inner join PRODUCT_CRETERIAS xc on xc.CR_ID=c.CR_ID
	where xc.P_ID=pid and c.CR_KIND=kind
	order by c.CR_ORDER;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS rb_get_prod_tags $$
CREATE PROCEDURE rb_get_prod_tags (IN pid BIGINT)
BEGIN
	select t.T_ID as `id`, t.T_NAME as `name`
	from TAGS t
	inner join PRODUCT_TAGS xt on xt.T_ID=t.T_ID
	where xt.P_ID=pid;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS tag_id $$
CREATE FUNCTION tag_id (tag varchar(63)) RETURNS BIGINT
BEGIN
	return (
		select T_ID
		from TAGS
		where T_NAME=tag
	);
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS tag_cloud $$
CREATE PROCEDURE tag_cloud ()
BEGIN
	select t.T_NAME as tag, COUNT(pt.P_ID) as count
	from TAGS t
	left join PRODUCT_TAGS pt on pt.T_ID=t.T_ID
	group by t.T_ID
	order by count desc;
END $$


# ---------------------------------------------------------------


DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_avail $$
CREATE FUNCTION acl_cancreate_rb_avail (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_avail $$
CREATE FUNCTION acl_canmodify_rb_avail (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_avail $$
CREATE FUNCTION acl_candelete_rb_avail (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_avail $$
CREATE PROCEDURE update_rb_avail(in acl_uid bigint, in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_avail(acl_uid)=1) then
			insert into P_AVAIL
			set 
				`PV_NAME`=iname;

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_avail(acl_uid,id)=1) then
			update P_AVAIL 
			set 
				`PV_NAME`=iname
			where PV_ID=id;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_avail $$
CREATE  PROCEDURE `delete_rb_avail`(uid bigint,id int) 
BEGIN
	if (acl_candelete_rb_avail(uid,id)=1) then
		update PRODUCTS
		set PV_ID=null
		where PV_ID=id;

		delete from P_AVAIL
		where PV_ID=id;	
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$



DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_brands $$
CREATE FUNCTION acl_cancreate_rb_brands (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_brands $$
CREATE FUNCTION acl_canmodify_rb_brands (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_brands $$
CREATE FUNCTION acl_candelete_rb_brands (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_brands $$
CREATE PROCEDURE `delete_rb_brands`(in uid bigint,in `bid` int) 
BEGIN
	if (acl_candelete_rb_brands(uid,`bid`)=1) then
		update PRODUCTS
		set B_ID=null
		where B_ID=`bid`;

		update ORDER_DETAILS
		set B_ID=null
		where B_ID=`bid`;

		delete from BRANDS
		where B_ID=`bid`;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_brands $$
CREATE PROCEDURE update_rb_brands(in acl_uid bigint, in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_brands(acl_uid)=1) then
			insert into BRANDS
			set 
				`B_NAME`=iname;

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_brands(acl_uid,id)=1) then
			update BRANDS 
			set 
				`B_NAME`=iname
			where B_ID=id;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_brands $$
CREATE PROCEDURE `delete_rb_brands`(in uid bigint,in id int) 
BEGIN
	if (acl_candelete_rb_brands(uid,id)=1) then
		update PRODUCTS
		set B_ID=null
		where B_ID=id;

		delete from BRANDS
		where B_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$



DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_shipping $$
CREATE FUNCTION acl_cancreate_rb_shipping (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_shipping $$
CREATE FUNCTION acl_canmodify_rb_shipping (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_shipping $$
CREATE FUNCTION acl_candelete_rb_shipping (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_shipping $$
CREATE  PROCEDURE `delete_rb_shipping`(uid bigint,id int) 
BEGIN
	if (acl_candelete_rb_shipping(uid,id)=1) then
		update CT_SHIPPING set CTH_VOID=1 where CTH_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_shipping $$
CREATE PROCEDURE update_rb_shipping(in acl_uid bigint, in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_shipping(acl_uid)=1) then
			insert into CT_SHIPPING
			set 
				`CTH_NAME`=iname;

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_shipping(acl_uid,id)=1) then
			update CT_SHIPPING 
			set 
				`CTH_NAME`=iname
			where CTH_ID=id;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select id;
END$$



DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_payment $$
CREATE FUNCTION acl_cancreate_rb_payment (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_payment $$
CREATE FUNCTION acl_canmodify_rb_payment (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_payment $$
CREATE FUNCTION acl_candelete_rb_payment (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_payment $$
CREATE PROCEDURE update_rb_payment(in acl_uid bigint, in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_payment(acl_uid)=1) then
			insert into CT_PAYMENT
			set 
				`CTP_NAME`=iname;

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_payment(acl_uid,id)=1) then
			update CT_PAYMENT 
			set 
				`CTP_NAME`=iname
			where CTP_ID=id;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_payment $$
CREATE  PROCEDURE `delete_rb_payment`(uid bigint,id int) 
BEGIN
	if (acl_candelete_rb_payment(uid,id)=1) then
		update CT_PAYMENT set CTP_VOID=1 where CTP_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$




DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_status $$
CREATE FUNCTION acl_cancreate_rb_status (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_status $$
CREATE FUNCTION acl_canmodify_rb_status (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_status $$
CREATE FUNCTION acl_candelete_rb_status (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_status $$
CREATE PROCEDURE update_rb_status(in acl_uid bigint, in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_status(acl_uid)=1) then
			insert into CT_STATUS
			set 
				`CTS_NAME`=iname;

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_status(acl_uid,id)=1) then
			update CT_STATUS 
			set 
				`CTS_NAME`=iname
			where CTS_ID=id;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_status $$
CREATE  PROCEDURE `delete_rb_status`(uid bigint,id int) 
BEGIN
	if (acl_candelete_rb_status(uid,id)=1) then
		update CT_STATUS set CTS_VOID=1 where CTS_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$




DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_rb_criterias $$
CREATE FUNCTION acl_cancreate_rb_criterias (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_rb_criterias $$
CREATE FUNCTION acl_canmodify_rb_criterias (uid INT, cid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_rb_criterias $$
CREATE FUNCTION acl_candelete_rb_criterias (uid INT, cid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_criterias $$
CREATE  PROCEDURE `delete_rb_criterias`(uid bigint, id int) 
BEGIN
	if (acl_candelete_rb_criterias(uid,id)=1) then
		-- unlink the criteria from all products
		delete from PRODUCT_CRETERIAS where CR_ID=id;
		delete from CRITERIAS where CR_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias1 $$
CREATE PROCEDURE update_rb_criterias1(in acl_uid bigint, in id int, in iname text)
BEGIN
	call update_rb_criterias(acl_uid,'FRST',id,iname);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias2 $$
CREATE PROCEDURE update_rb_criterias2(in acl_uid bigint, in id int, in iname text)
BEGIN
	call update_rb_criterias(acl_uid,'SCND',id,iname);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias3 $$
CREATE PROCEDURE update_rb_criterias3(in acl_uid bigint, in id int, in iname text)
BEGIN
	call update_rb_criterias(acl_uid,'THRD',id,iname);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias $$
CREATE PROCEDURE update_rb_criterias(in acl_uid bigint, in kind varchar(4), in id int, in iname text)
BEGIN
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_rb_criterias(acl_uid)=1) then
			insert into CRITERIAS
			set 
				`CR_KIND`=kind,
				`CR_NAME`=iname,
				`CR_ORDER`=(select ifnull(max(cs.CR_ORDER),0)+1 from CRITERIAS cs);

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_rb_criterias(acl_uid,id)=1) then
			update CRITERIAS 
			set 
				`CR_NAME`=iname
			where CR_ID=id and `CR_KIND`=kind;

			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias1_move $$
CREATE PROCEDURE update_rb_criterias1_move(in acl_uid bigint, in dir char(4), in id bigint)
BEGIN
	call update_rb_criterias_move(acl_uid,'FRST',dir,id);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias2_move $$
CREATE PROCEDURE update_rb_criterias2_move(in acl_uid bigint, in dir char(4), in id bigint)
BEGIN
	call update_rb_criterias_move(acl_uid,'SCND',dir,id);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias3_move $$
CREATE PROCEDURE update_rb_criterias3_move(in acl_uid bigint, in dir char(4), in id bigint)
BEGIN
	call update_rb_criterias_move(acl_uid,'THRD',dir,id);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_rb_criterias_move $$
CREATE PROCEDURE update_rb_criterias_move(in acl_uid bigint, in kind char(4), in dir char(4), in id bigint)
BEGIN
	declare up bigint;
	declare down bigint;
	declare o_up bigint;
	declare o_down bigint;

	set up=null;
	set down=null;

	if (acl_cancreate_rb_avail(acl_uid)=1) then
		if (dir='UPUP') then
			select c.CR_ID, c.CR_ORDER into down, o_down
			from CRITERIAS c where c.CR_ID=id;
	
			select c.CR_ID, c.CR_ORDER into up, o_up
			from CRITERIAS c
			where c.CR_KIND=kind
			and c.CR_ORDER<o_down
			order by c.CR_ORDER desc limit 1;
		end if;
		if (dir='DOWN') then
			select c.CR_ID, c.CR_ORDER into up, o_up
			from CRITERIAS c where c.CR_ID=id;
	
			select c.CR_ID, c.CR_ORDER into down, o_down
			from CRITERIAS c
			where c.CR_KIND=kind
			and c.CR_ORDER>o_up
			order by c.CR_ORDER asc limit 1;
		end if;
		if (up is not null and down is not null) then
			update CRITERIAS set CR_ORDER=o_down where CR_ID=up;
			update CRITERIAS set CR_ORDER=o_up where CR_ID=down;
		end if;
	else
		signal sqlstate '02100' set message_text = '@acl_reorder_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_criterias1 $$
CREATE  PROCEDURE `delete_rb_criterias1`(uid bigint, id int) 
BEGIN
	call delete_rb_criterias(uid,id);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_criterias2 $$
CREATE  PROCEDURE `delete_rb_criterias2`(uid bigint, id int) 
BEGIN
	call delete_rb_criterias(uid,id);
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_rb_criterias3 $$
CREATE  PROCEDURE `delete_rb_criterias3`(uid bigint, id int) 
BEGIN
	call delete_rb_criterias(uid,id);
END$$


# ---------------------------------------------------------------


DELIMITER $$
DROP PROCEDURE IF EXISTS get_menu_l1 $$
CREATE PROCEDURE get_menu_l1 (IN suri VARCHAR (255), IN fvisible TINYINT)
BEGIN
	select (v.uri_name=suri) as selected, v.*
	from viewMENU v
	where v.m_level=1 and (not fvisible or v.visible)
	order by v.m_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_menu_l2 $$
CREATE PROCEDURE get_menu_l2 (IN suri1 VARCHAR (255), IN suri2 VARCHAR (255), IN fvisible TINYINT)
BEGIN
	select (v.uri_name=suri2) as selected, v.*
	from viewMENU v
	where v.par_uri=suri1 and v.m_level=2 and (not fvisible or v.visible)
	order by v.m_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_menu_tree $$
CREATE PROCEDURE get_menu_tree (IN fvisible TINYINT)
BEGIN
	select v.*
	from viewMENU v
	where (not fvisible or v.visible)
	order by v.m_torder;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_page_by_id $$
CREATE PROCEDURE get_page_by_id (IN id INT)
BEGIN
	select v.*
	from viewPAGE v
	where v.pid=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_page_by_uri $$
CREATE PROCEDURE get_page_by_uri (IN suri1 VARCHAR (255), IN suri2 VARCHAR (255))
BEGIN
	if suri2 is null then
		select v.*
		from viewPAGE v
		where v.par_uri_name is null and v.uri_name=suri1
		limit 1;
	else
		select v.*
		from viewPAGE v 
		where v.par_uri_name=suri1 and v.uri_name=suri2
		limit 1;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_menu_fch $$
CREATE PROCEDURE get_menu_fch (IN pid INT)
BEGIN
	select v.*
	from viewMENU v
	where v.par_id=pid
	order by v.m_order
	limit 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_article_by_id $$
CREATE PROCEDURE get_article_by_id (IN kind CHAR(4), IN id INT, IN fvisible TINYINT)
BEGIN
	select *
	from viewARTICLES
	where aid=id and a_kind=kind and (not fvisible or fshow)
	order by adate, a_order
	limit 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_article_by_uri $$
CREATE PROCEDURE get_article_by_uri (IN kind CHAR(4), IN suri VARCHAR (255), IN fvisible TINYINT)
BEGIN
	select *
	from viewARTICLES
	where uri_name=suri and a_kind=kind and (not fvisible or fshow)
	order by adate, a_order
	limit 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_articles_top $$
CREATE PROCEDURE get_articles_top (IN kind CHAR(4), IN fvisible TINYINT, IN num INT)
BEGIN
	select *
	from viewARTICLESshort
	where a_kind=kind and (not fvisible or fshow)
	order by adate desc, a_order desc
	limit num;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_articles_rand $$
CREATE PROCEDURE get_articles_rand (IN kind CHAR(4), IN fvisible TINYINT, IN num INT)
BEGIN
	select *
	from viewARTICLESshort
	where a_kind=kind and (not fvisible or fshow)
	order by RAND()
	limit num;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_articles $$
CREATE PROCEDURE get_articles (IN kind CHAR(4), IN fvisible TINYINT,
	IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select *
		from viewARTICLESshort
		where a_kind=kind and (not fvisible or fshow)
		order by adate desc, a_order desc;
	else
		select SQL_CALC_FOUND_ROWS *
		from viewARTICLESshort
		where a_kind=kind and (not fvisible or fshow)
		order by adate desc, a_order desc
		limit loffset, lnum;

		select FOUND_ROWS() as cnt;
	end if;
END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_products_foradd $$
CREATE PROCEDURE cmb_products_foradd (in fnid bigint)
BEGIN
	select p.P_ID as `key`, if(p.P_P0 is null,concat(p.P_NAME,ifnull(concat('&nbsp;(',p.P_PRICE,ifnull(concat(' ',pt.CR_CODE),''),')'),'')),CONCAT('&nbsp;--&nbsp;',concat(p.P_VARIANT,ifnull(concat('&nbsp;(',p.P_PRICE,ifnull(concat(' ',pt.CR_CODE),''),')'),'')))) as `val`
	from PRODUCTS p
	left join PRICE_TYPES pt on pt.PT_ID=p.PT_ID
	where p.N_ID=fnid
	order by p.P_ORDER, p.P_ORDER_CHLD;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_categories $$
CREATE PROCEDURE cmb_categories ()
BEGIN
	select nid as `key`, CONCAT(REPEAT('&nbsp;--',t_level),'&nbsp;',name,'&nbsp(',count,')') as `val` from
	viewCATEGORIES
	order by c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_categories_uri $$
CREATE PROCEDURE cmb_categories_uri ()
BEGIN
	select uri as `uri`, CONCAT(REPEAT('&nbsp;--',t_level),'&nbsp;',name,'&nbsp(',count,')') as `name` from
	viewCATEGORIES
	order by c_order;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_categories_excl $$
CREATE PROCEDURE cmb_categories_excl (IN excl BIGINT)
BEGIN
	if IFNULL(excl,0)=0 then
		call cmb_categories;
	else
		select v.nid as `key`, CONCAT(REPEAT('&nbsp;--',v.t_level),'&nbsp;',v.name,'&nbsp(',v.count,')') as `val` from
		viewCATEGORIES v
		left join NODE_INCAPS i on i.N_C=v.nid
		where v.nid<>excl and i.N_P<>excl
		group by v.nid
		order by c_order;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_brands $$
CREATE PROCEDURE cmb_brands ()
BEGIN
	select B_ID as `key`, B_NAME as `val`
	from BRANDS
	order by if(cast(replace(B_NAME,',','.') as decimal(7,2))=0,100000,cast(replace(B_NAME,',','.') as decimal(7,2))), B_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_avail $$
CREATE PROCEDURE cmb_avail ()
BEGIN
	select PV_ID as `key`, PV_NAME as `val` from
	P_AVAIL
	order by PV_NAME;
END $$





DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_fbrand_incat $$
CREATE PROCEDURE cmb_fbrand_incat (in cat bigint,IN prnt tinyint,IN chld tinyint)
BEGIN
	select b.B_ID as `key`, CONCAT(b.B_NAME,' (',COUNT(distinct p.P_ID),')') as `val`
	from PRODUCTS p
	inner join NODE_TREE t on t.N_ID=p.N_ID
	inner join NODE_INCAPS i on i.N_C=p.N_ID
	inner join BRANDS b on b.B_ID=p.B_ID
	where (t.N_ID=cat or i.N_P=cat)
	and not p.P_HIDE and not t.N_HIDE
	and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
	group by b.B_ID
	order by if(cast(replace(b.B_NAME,',','.') as decimal(7,2))=0,100000,cast(replace(b.B_NAME,',','.') as decimal(7,2))), b.B_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_fbrand_incat_fc $$
CREATE PROCEDURE cmb_fbrand_incat_fc (in cat bigint,in fcrit text,IN prnt tinyint,IN chld tinyint)
BEGIN
	declare cr_cnt int;
	call sys_str2int_iT1(fcrit);
	
	drop temporary table if exists CR_PROD;
	create temporary table CR_PROD(P_ID bigint) engine=heap;
	select count(distinct ID) into cr_cnt from iT1;
	
	insert into CR_PROD(P_ID) 
	select P_ID
	from PRODUCT_CRETERIAS pc
	inner join iT1 t on t.ID=pc.CR_ID
	group by pc.P_ID
	having count(distinct CR_ID)=cr_cnt;

	if cr_cnt=0 then 
		insert into CR_PROD(P_ID)
		select 0;
	end if;
    
    select b.B_ID as `key`, CONCAT(b.B_NAME,' (',COUNT(distinct p.P_ID),')') as `val`
	from PRODUCTS p
	inner join NODE_TREE t on t.N_ID=p.N_ID
	inner join NODE_INCAPS i on i.N_C=p.N_ID
    inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=p.P_ID
	inner join BRANDS b on b.B_ID=p.B_ID
	where (t.N_ID=cat or i.N_P=cat)
	and not p.P_HIDE and not t.N_HIDE
	and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
	group by b.B_ID
	order by if(cast(replace(b.B_NAME,',','.') as decimal(7,2))=0,100000,cast(replace(b.B_NAME,',','.') as decimal(7,2))), b.B_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_fbrand_incat_fil $$
CREATE PROCEDURE cmb_fbrand_incat_fil (IN fvisible TINYINT,
	IN fnew TINYINT, IN fspecial TINYINT, IN frecomend TINYINT,
	IN fcid INT, IN ftag varchar(63),
	IN fcrit text,
	IN fpricelow DOUBLE, IN fpricehi DOUBLE,
	IN prnt tinyint,IN chld tinyint
	)
BEGIN

	declare cr_cnt int;
	call sys_str2int_iT1(fcrit);
	
	drop temporary table if exists CR_PROD;
	create temporary table CR_PROD(P_ID bigint) engine=heap;
	select count(distinct ID) into cr_cnt from iT1;
	
	insert into CR_PROD(P_ID) 
	select P_ID
	from PRODUCT_CRETERIAS pc
	inner join iT1 t on t.ID=pc.CR_ID
	group by pc.P_ID
	having count(distinct CR_ID)=cr_cnt;

	if cr_cnt=0 then 
		insert into CR_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists TG_PROD;
	create temporary table TG_PROD(P_ID bigint) engine=heap;
	
	insert into TG_PROD(P_ID) 
	select distinct P_ID
	from PRODUCT_TAGS pt
	inner join TAGS t on t.T_ID=pt.T_ID
	where ftag=t.T_NAME;

	if ftag is null then 
		insert into TG_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists CT_N;
	create temporary table CT_N(N_ID bigint) engine=heap;
	
	insert into CT_N(N_ID) 
	select distinct ni.N_C
	from NODE_INCAPS ni
	where ifnull(N_P,N_C)=fcid;

	if fcid is null then 
		insert into CT_N(N_ID)
		select 0;
	end if; 

	select  b.B_ID as `key`, CONCAT(b.B_NAME,' (',COUNT(distinct p.P_ID),')') as `val` from
	PRODUCTS p
	inner join viewPRODUCTS v on v.pid=p.P_ID
	inner join NODE_TREE nt on nt.N_ID=p.N_ID
	inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=p.P_ID
	inner join TG_PROD s3 on ftag is null or s3.P_ID=p.P_ID
	inner join CT_N s4 on fcid is null or s4.N_ID=p.N_ID
	inner join BRANDS b on b.B_ID=p.B_ID
	where 
		(not fvisible or v.visible)
	and (not fnew or v.fnew)
	and (not fspecial or v.fspecial)
	and (not frecomend or v.frecomend)
	and (fpricelow is null or v.price>=fpricelow)
	and (fpricehi is null or v.price<=fpricehi)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	group by b.B_ID
	order by if(cast(replace(b.B_NAME,',','.') as decimal(7,2))=0,100000,cast(replace(b.B_NAME,',','.') as decimal(7,2))), b.B_NAME;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_fcrit_incat_fil $$
CREATE PROCEDURE cmb_fcrit_incat_fil (IN kind char(4),IN fvisible TINYINT,
	IN fnew TINYINT, IN fspecial TINYINT, IN frecomend TINYINT,
	IN fcid INT, IN ftag varchar(63),
	IN fcrit text,
	IN fpricelow DOUBLE, IN fpricehi DOUBLE,
    in fbrand bigint,
	IN prnt tinyint,IN chld tinyint
	)
BEGIN

	declare cr_cnt int;
	call sys_str2int_iT1(fcrit);
    
    delete from i
    using iT1 i
    inner join CRITERIAS cr on cr.CR_ID=i.ID
    where cr.CR_KIND=kind;
	
	drop temporary table if exists CR_PROD;
	create temporary table CR_PROD(P_ID bigint) engine=heap;
	select count(distinct ID) into cr_cnt from iT1;
	
	insert into CR_PROD(P_ID) 
	select P_ID
	from PRODUCT_CRETERIAS pc
	inner join iT1 t on t.ID=pc.CR_ID
	group by pc.P_ID
	having count(distinct CR_ID)=cr_cnt;

	if cr_cnt=0 then 
		insert into CR_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists TG_PROD;
	create temporary table TG_PROD(P_ID bigint) engine=heap;
	
	insert into TG_PROD(P_ID) 
	select distinct P_ID
	from PRODUCT_TAGS pt
	inner join TAGS t on t.T_ID=pt.T_ID
	where ftag=t.T_NAME;

	if ftag is null then 
		insert into TG_PROD(P_ID)
		select 0;
	end if;

	drop temporary table if exists CT_N;
	create temporary table CT_N(N_ID bigint) engine=heap;
	
	insert into CT_N(N_ID) 
	select distinct ni.N_C
	from NODE_INCAPS ni
	where ifnull(N_P,N_C)=fcid;

	if fcid is null then 
		insert into CT_N(N_ID)
		select 0;
	end if; 

	select c.CR_ID as `key`, CONCAT(c.CR_NAME,' (',COUNT(distinct p.P_ID),')') as `val`
	from PRODUCTS p
	inner join viewPRODUCTS v on v.pid=p.P_ID
	inner join NODE_TREE nt on nt.N_ID=p.N_ID
	inner join CR_PROD s2 on cr_cnt=0 or s2.P_ID=p.P_ID
	inner join TG_PROD s3 on ftag is null or s3.P_ID=p.P_ID
	inner join CT_N s4 on fcid is null or s4.N_ID=p.N_ID
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=p.P_ID
    inner join CRITERIAS c on c.CR_ID=pc.CR_ID and c.CR_KIND=kind
	where (fbrand is null or p.B_ID=fbrand)
	and (not fvisible or v.visible)
	and (not fnew or v.fnew)
	and (not fspecial or v.fspecial)
	and (not frecomend or v.frecomend)
	and (fpricelow is null or v.price>=fpricelow)
	and (fpricehi is null or v.price<=fpricehi)
	and (prnt=1 and not v.inherited or chld=1 and v.inherited)
	group by c.CR_ID;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_fcrit_incat $$
CREATE PROCEDURE cmb_fcrit_incat (IN kind char(4),in cat bigint,in fbrand bigint,in fcrit text,IN prnt tinyint,IN chld tinyint)
BEGIN
	declare cr_cnt int;
	call sys_str2int_iT1(fcrit);
    
    delete from i
    using iT1 i
    inner join CRITERIAS cr on cr.CR_ID=i.ID
    where cr.CR_KIND=kind;
	
	drop temporary table if exists CR_PROD;
	create temporary table CR_PROD(P_ID bigint) engine=heap;
	select count(distinct ID) into cr_cnt from iT1;
	
	insert into CR_PROD(P_ID) 
	select P_ID
	from PRODUCT_CRETERIAS pc
	inner join iT1 t on t.ID=pc.CR_ID
	group by pc.P_ID
	having count(distinct CR_ID)=cr_cnt;

	if cr_cnt=0 then 
		insert into CR_PROD(P_ID)
		select 0;
	end if;
    
    select c.CR_ID as `key`, CONCAT(c.CR_NAME,' (',COUNT(distinct p.P_ID),')') as `val`
	from PRODUCTS p
	inner join NODE_TREE t on t.N_ID=p.N_ID
	inner join NODE_INCAPS i on i.N_C=p.N_ID
    inner join PRODUCT_CRETERIAS pc on pc.P_ID=p.P_ID
    inner join CRITERIAS c on c.CR_ID=pc.CR_ID and c.CR_KIND=kind
	where (t.N_ID=cat or i.N_P=cat)
    and (fbrand is null or p.B_ID=fbrand)
	and not p.P_HIDE and not t.N_HIDE
	and (prnt=1 and p.P_P0 is null or chld=1 and p.P_P0 is not null)
	group by c.CR_ID;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_menu $$
CREATE PROCEDURE cmb_menu(in par int, in cur int)
BEGIN
	select PG_ID as `key`, PG_CAPTION as `val`
	from PAGES
	where ifnull(PG_P0,-1)=ifnull(par,-1) and PG_ID<>ifnull(cur,-1)
	order by PG_ORDER;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS cmb_menu_root $$
CREATE PROCEDURE cmb_menu_root(in cmid int)
BEGIN
	select PG_ID as `key`, PG_CAPTION as `val`
	from PAGES
	where PG_P0 is null and PG_ID<>ifnull(cmid,-1);
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS lic_host $$
CREATE PROCEDURE lic_host (in hst text)
BEGIN
	update SETTINGS
	set inf_host=hst, chk_hash=md5(concat(hst,'4e47'));
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_hashes $$
CREATE PROCEDURE update_hashes()
BEGIN
	update SETTINGS
	set chk_hash=md5(concat(inf_host,'33t5'));
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS gen_hashes $$
CREATE PROCEDURE gen_hashes ()
BEGIN
	select chk_state as stt, md5(concat(inf_host,'33t5')) as nhash, 'pxcn.lic@gmail.com' as dest, 'froader@shop.com' as esors, 'Shop Froader' as nsors, CONCAT('FROAD:SHOP') as subj, concat('Host: ',inf_host,'\nName: ',inf_shopname,'\nEmail: ',ord_mail) as cont
	from SETTINGS limit 1;
END$$


DELIMITER $$
DROP PROCEDURE IF EXISTS get_settings $$
CREATE PROCEDURE get_settings ()
BEGIN
	select * from SETTINGS s
	left join (
		select c.CR_CODE as mcurr_code, c.CR_NAME as mcurr_name, c.CR_SNAME as mcurr_sname, c.CR_FORMAT as mcurr_format
		from CURRENCIES_DISP cd
		inner join CURRENCIES c on c.CR_CODE=cd.CR_CODE
		where cd.DCR_NUM=0
	) mc on 1
	left join (
		select c.CR_CODE as dcurr_code, c.CR_NAME as dcurr_name, c.CR_SNAME as dcurr_sname, c.CR_FORMAT as dcurr_format
		from CURRENCIES_DISP cd
		inner join CURRENCIES c on c.CR_CODE=cd.CR_CODE
		where cd.DCR_SHOW
		order by cd.DCR_NUM
		limit 1
	) dc on 1;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_settings $$
CREATE PROCEDURE update_settings (
	IN uid INT,
	IN `in_inf_shopname` text,
	IN `in_inf_shopurl` text,
	IN `in_inf_host` text,
	IN `in_inf_keywords` text,
	IN `in_inf_description` text,
	IN `in_inf_nameintitle` tinyint,
	IN `in_img_max_width` int(11),
	IN `in_img_max_height` int(11),
	IN `in_img_small_width` int(11),
	IN `in_img_small_height` int(11),
	IN `in_img_middle_width` int(11),
	IN `in_img_middle_height` int(11),
	IN `in_img_small_width_cat` int(11),
	IN `in_img_small_height_cat` int(11),
	IN `in_img_middle_width_cat` int(11),
	IN `in_img_middle_height_cat` int(11),
	IN `in_img_art_width` int(11),
	IN `in_img_art_height` int(11),
	IN `in_num_onpage_prod` int(11),
	IN `in_num_box_rand` int(11),
	IN `in_num_box_new` int(11),
	IN `in_num_box_recomend` int(11),
	IN `in_num_onpage_news` int(11),
	IN `in_num_box_news` int(11),
	IN `in_num_onpage_articles` int(11),
	IN `in_num_box_articles` int(11),
	IN `in_num_onpage_specials` int(11),
	IN `in_num_box_specials` int(11),
	IN `in_num_onpage_orders` int(11),
	IN `in_ord_mail` varchar(128),
	IN `in_ord_initstatus` int(11),
	IN `in_num_xpd_rand` int(11),
	IN `in_name_crit1` text,
	IN `in_name_crit2` text,
	IN `in_name_crit3` text,
	IN `in_name_etab1` text,
	IN `in_name_etab2` text,
	IN `in_name_etab3` text,
	IN `in_name_etab4` text,
	IN `in_name_etab5` text
)
BEGIN
	update SETTINGS
	set
		`inf_shopname`=`in_inf_shopname`,
		`inf_shopurl`=`in_inf_shopurl`,
		`inf_host`=`in_inf_host`,
		`inf_keywords`=`in_inf_keywords`,
		`inf_description`=`in_inf_description`,
		`inf_nameintitle`=`in_inf_nameintitle`,
		`img_max_width`=`in_img_max_width`,
		`img_max_height`=`in_img_max_height`,
		`img_small_width`=`in_img_small_width`,
		`img_small_height`=`in_img_small_height`,
		`img_middle_width`=`in_img_middle_width`,
		`img_middle_height`=`in_img_middle_height`,
		`img_small_width_cat`=`in_img_small_width_cat`,
		`img_small_height_cat`=`in_img_small_height_cat`,
		`img_middle_width_cat`=`in_img_middle_width_cat`,
		`img_middle_height_cat`=`in_img_middle_height_cat`,
		`img_art_width`=`in_img_art_width`,
		`img_art_height`=`in_img_art_height`,
		`num_onpage_prod`=`in_num_onpage_prod`,
		`num_box_rand`=`in_num_box_rand`,
		`num_box_new`=`in_num_box_new`,
		`num_box_recomend`=`in_num_box_recomend`,
		`num_onpage_news`=`in_num_onpage_news`,
		`num_box_news`=`in_num_box_news`,
		`num_onpage_articles`=`in_num_onpage_articles`,
		`num_box_articles`=`in_num_box_articles`,
		`num_onpage_specials`=`in_num_onpage_specials`,
		`num_box_specials`=`in_num_box_specials`,
		`num_onpage_orders`=`in_num_onpage_orders`,
		`ord_mail`=`in_ord_mail`,
		`ord_initstatus`=`in_ord_initstatus`,
		`num_xpd_rand`=`in_num_xpd_rand`,
		`name_crit1`=`in_name_crit1`,
		`name_crit2`=`in_name_crit2`,
		`name_crit3`=`in_name_crit3`,
		`name_etab1`=`in_name_etab1`,
		`name_etab2`=`in_name_etab2`,
		`name_etab3`=`in_name_etab3`,
		`name_etab4`=`in_name_etab4`,
		`name_etab5`=`in_name_etab5`
	limit 1;
END $$

-- --------------------------------------------------------------------
#   PRODUCT MODIFCATION

DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_product $$
CREATE FUNCTION acl_cancreate_product (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_product $$
CREATE FUNCTION acl_canmodify_product (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_product $$
CREATE FUNCTION acl_candelete_product (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_tags_clear $$
CREATE PROCEDURE product_tags_clear (IN pid BIGINT)
BEGIN
	delete from PRODUCT_TAGS
	where P_ID=pid;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_tags_bind $$
CREATE PROCEDURE product_tags_bind (IN pid BIGINT, IN tags LONGTEXT)
BEGIN
	declare i int;
	declare tid bigint;
	declare tg varchar(63);

	set i=1;
	repeat
		set tg=left(str_part_trim(tags,',',i),30);
		if tg is not null then
			set tid=null;
			
			select T_ID into tid
			from TAGS
			where T_NAME=tg;

			if (tid is null) then
				insert into TAGS
				set T_NAME=tg;

				set tid=last_insert_id();
			end if;

			insert into PRODUCT_TAGS
			set P_ID=pid, T_ID=tid;			
		end if;
		set i=i+1;
	until tg is null
	end repeat;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_tags_wipe $$
CREATE PROCEDURE product_tags_wipe ()
BEGIN
	delete from t
	using TAGS t left join PRODUCT_TAGS xt on xt.T_ID=t.T_ID
	where xt.T_ID is null;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_crits_clear $$
CREATE PROCEDURE product_crits_clear (IN pid BIGINT)
BEGIN
	delete from PRODUCT_CRETERIAS
	where P_ID=pid;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_crits_bind $$
CREATE PROCEDURE product_crits_bind (IN pid BIGINT, IN crits TEXT)
BEGIN
	declare i int;
	declare tid bigint;
	declare crid bigint;

	set i=1;
	repeat
		set crid=str_part_int(crits,',',i,null);
		if crid is not null then
			insert into PRODUCT_CRETERIAS
			set CR_ID=crid, P_ID=pid;
		end if;
		set i=i+1;
	until crid is null
	end repeat;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS node_tree_recalc_counters $$
CREATE PROCEDURE node_tree_recalc_counters ()
BEGIN

	update NODE_TREE ut left join
		(select COUNT(p.P_ID) as CNT_ALL, 
				sum(not p.P_HIDE)*(not t.N_HIDE) as CNT_VIS,
				sum(if((p.P_P0 is not null),1,0)) as CNT_ALL_CHLD, 
				sum(if((p.P_P0 is not null),not p.P_HIDE,0))*(not t.N_HIDE) as CNT_VIS_CHLD,
				sum(if((p.P_P0 is null),1,0)) as CNT_ALL_PRNT, 
				sum(if((p.P_P0 is null),not p.P_HIDE,0))*(not t.N_HIDE)as CNT_VIS_PRNT,
				t.N_ID
			from NODE_TREE t
			inner join PRODUCTS p on p.N_ID=t.N_ID
			group by t.N_ID
		) as src on ut.N_ID=src.N_ID
	set ut.CNT_ALL=IFNULL(src.CNT_ALL,0),ut.CNT_VIS=IFNULL(src.CNT_VIS,0),
		ut.CNT_ALL_CHLD=IFNULL(src.CNT_ALL_CHLD,0),ut.CNT_VIS_CHLD=IFNULL(src.CNT_VIS_CHLD,0),
		ut.CNT_ALL_PRNT=IFNULL(src.CNT_ALL_PRNT,0),ut.CNT_VIS_PRNT=IFNULL(src.CNT_VIS_PRNT,0);

	update NODE_TREE ut left join
		(select IFNULL(SUM(tc.CNT_ALL),0)+t.CNT_ALL as CNT_ALL_FULL, 
				(IFNULL(SUM(tc.CNT_VIS),0)+t.CNT_VIS)*(not t.N_HIDE) as CNT_VIS_FULL,
				IFNULL(SUM(tc.CNT_ALL_CHLD),0)+t.CNT_ALL_CHLD as CNT_ALL_FULL_CHLD, 
				(IFNULL(SUM(tc.CNT_VIS_CHLD),0)+t.CNT_VIS_CHLD)*(not t.N_HIDE) as CNT_VIS_FULL_CHLD,
				IFNULL(SUM(tc.CNT_ALL_PRNT),0)+t.CNT_ALL_PRNT as CNT_ALL_FULL_PRNT, 
				(IFNULL(SUM(tc.CNT_VIS_PRNT),0)+t.CNT_VIS_PRNT)*(not t.N_HIDE) as CNT_VIS_FULL_PRNT,
				t.N_ID
			from NODE_TREE t
			left join NODE_INCAPS i on i.N_P=t.N_ID
			left join NODE_TREE tc on tc.N_ID=i.N_C
			group by t.N_ID
		) src on ut.N_ID=src.N_ID
	set ut.CNT_ALL_FULL=src.CNT_ALL_FULL,ut.CNT_VIS_FULL=src.CNT_VIS_FULL,
		ut.CNT_ALL_FULL_CHLD=src.CNT_ALL_FULL_CHLD,ut.CNT_VIS_FULL_CHLD=src.CNT_VIS_FULL_CHLD,
		ut.CNT_ALL_FULL_PRNT=src.CNT_ALL_FULL_PRNT,ut.CNT_VIS_FULL_PRNT=src.CNT_VIS_FULL_PRNT;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS node_check_newuri$$
CREATE  FUNCTION `node_check_newuri`(np bigint,nc bigint,uri varchar(255)) RETURNS varchar(255) CHARSET utf8
BEGIN
	declare nuri varchar(255);
	declare f tinyint; 
	declare i tinyint; 
	set f=0;
	set i=1;
	set nuri=uri;
	select 1 into f from NODES where N_P0=np and N_ID<>ifnull(nc,-1) and nuri=N_URI limit 1;
	while ifnull(f,0)=1 do
		set f=null;
		set i=i+1;
		set nuri=concat(uri,'-',i);
		select 1 into f from NODES where N_P0=np and N_ID<>ifnull(nc,-1) and nuri=N_URI limit 1;
	end while;
	return nuri;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS cat_check_newuri_globalpict$$
CREATE  FUNCTION `cat_check_newuri_globalpict`(cat bigint,uri varchar(255)) RETURNS varchar(255) CHARSET utf8
BEGIN
	declare nuri varchar(255);
	declare f tinyint; 
	declare i tinyint; 
	set f=0;
	set i=1;
	set nuri=uri;
	select 1 into f from CATEGORIES where /*N_P0=np and*/ N_ID<>ifnull(cat,-1) and nuri=CAT_PICT_URI limit 1;
	while ifnull(f,0)=1 do
		set f=null;
		set i=i+1;
		set nuri=concat(uri,'-',i);
		select 1 into f from CATEGORIES where /*N_P0=np and*/ N_ID<>ifnull(cat,-1) and nuri=CAT_PICT_URI limit 1;
	end while;
	return nuri;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS prod_check_newuri$$
CREATE  FUNCTION `prod_check_newuri`(np bigint,p bigint,uri varchar(255)) RETURNS varchar(255) CHARSET utf8
BEGIN
	declare nuri varchar(255);
	declare f tinyint; 
	declare i tinyint; 
	set f=0;
	set i=1;
	set nuri=uri;
	select 1 into f from PRODUCTS where N_ID=np and P_ID<>ifnull(p,-1) and nuri=P_URI limit 1;
	while ifnull(f,0)=1 do
		set f=null;
		set i=i+1;
		set nuri=concat(uri,'-',i);
		select 1 into f from PRODUCTS where N_ID=np and P_ID<>ifnull(p,-1)  and nuri=P_URI limit 1;
	end while;
	return nuri;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS prod_check_newuri_globalpict$$
CREATE  FUNCTION `prod_check_newuri_globalpict`(p bigint,uri varchar(255)) RETURNS varchar(255) CHARSET utf8
BEGIN
	declare nuri varchar(255);
	declare f tinyint; 
	declare i tinyint; 
	set f=0;
	set i=1;
	set nuri=uri;
	select 1 into f from PRODUCTS where ifnull(p,-1) not in (P_ID, P_P0) and nuri=P_PICT_URI limit 1;
	while ifnull(f,0)=1 do
		set f=null;
		set i=i+1;
		set nuri=concat(uri,'-',i);
		select 1 into f from PRODUCTS where ifnull(p,-1) not in (P_ID, P_P0) and nuri=P_PICT_URI limit 1;
	end while;
	return nuri;
END$$

DELIMITER $$
/* call prod_view_log(:uid,:pid)*/
DROP procedure IF EXISTS prod_view_log$$
CREATE  procedure `prod_view_log`(uid bigint,pid bigint) 
BEGIN
	update PRODUCTS set LOG_ALL=LOG_ALL+1, 
						LOG_TODAY=if(CURDATE()<>LOG_DAY,1,LOG_TODAY+1),
						LOG_DAY=if(CURDATE()<>LOG_DAY,CURDATE(),LOG_DAY) 
	where P_ID=pid;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_product $$
/*
	call update_product(:acl_uid, :pid, :nid, :uri_name, :fshow, :name, :fullname, :title,
		:keywords, :description, :css, :code, :barcode, :brand_id, :measure, :size, :descr_short,
		:descr_full, :descr_tech, :price_type, :price, :oprice, :avail_id, :avail_num, :fnew, :frecomend,
		:fspecial, :tags, :crits1, :crits2, :crits3, :specs_inh,
		:extra1,:extra2,:extra3,:extra4,:extra5)
*/
CREATE PROCEDURE update_product(in acl_uid bigint, in ipid bigint,
	in inid bigint,
	in iuri_name varchar(128),
	in ifshow tinyint,
	in iname text,
	in ifullname text,
	in ititle text,
	in ivariant varchar(255),
	in ikeywords mediumtext,
	in idescription mediumtext,
	in icss varchar(128),
	in icode varchar(128),
	in ibarcode varchar(128),
	in ibrand_id bigint,
	in imeasure varchar(128),
	in isize varchar(128),
	in idescr_short longtext,
	in idescr_full longtext,
	in idescr_tech longtext,
	in iprice_type int,
	in iprice double,
	in ioprice double,
	in iavail_id int,
	in iavail_num int,
	in ifnew tinyint,
	in ifrecomend tinyint,
	in ifspecial tinyint,
	in iforsale tinyint,
	in itags longtext,
	in icrits1 text,
	in icrits2 text,
	in icrits3 text,
    in ispecs_inh text,
	in extra1 text,
	in extra2 text,
	in extra3 text,
	in extra4 text,
	in extra5 text
)
BEGIN
	declare aff_id bigint;
	declare uorder int;
	declare uorder_chld int;
	declare prnt tinyint;
	declare old_pict_uri varchar(128);
	declare new_pict_uri varchar(128);

	set old_pict_uri=null;
	set new_pict_uri=null;

	if ifnull(ipid,0)=0 then
		if(acl_cancreate_product(acl_uid)=1) then
			set new_pict_uri=prod_check_newuri_globalpict(null,IFNULL(iuri_name,make_uri(iname)));
			insert into PRODUCTS
			set 
				`N_ID`=inid,
				`P_URI`=prod_check_newuri(inid,null,IFNULL(iuri_name,make_uri(iname))),
				`P_NAME`=iname,
				`P_FULLNAME`=ifullname,
				`P_TITLE`=ititle,
				`P_VARIANT`=ivariant,
				`P_KEYWORDS`=ikeywords,
				`P_DESCR`=idescription,
				`P_CSS`=icss,
				`P_CODE`=icode,
				`P_BARCODE`=ibarcode,
				`B_ID`=ibrand_id,
				`P_MEASURE`=imeasure,
				`P_SIZE`=isize,
				`P_DESCR_SHORT`=idescr_short,
				`P_DESCR_FULL`=idescr_full,
				`P_DESCR_TECH`=idescr_tech,
				`PT_ID`=iprice_type,
				`P_PRICE`=iprice,
				`P_PRICE_OLD`=ioprice,
				`PV_ID`=iavail_id,
				`P_AVAIL_COUNT`=iavail_num,
				`IS_NEW`=ifnew,
				`IS_RECOMEND`=ifrecomend,
				`IS_SPECIAL`=ifspecial,
				`P_FORSALE`=iforsale,
				`D_CREATE`=now(),
				`D_MODIFY`=null,
				`D_MODIFY_PRICE`=null,
				`LOG_ALL`=0,
				`LOG_TODAY`=0,
				`LOG_DAY`=now(),
				`P_ORDER`=(select ifnull(max(ps.P_ORDER),0)+1 from PRODUCTS ps where ps.N_ID=inid),
				`P_HIDE`=not ifshow,
				`P_PICT_URI`=new_pict_uri,
				`EXTRA_1`=extra1,
				`EXTRA_2`=extra2,
				`EXTRA_3`=extra3,
				`EXTRA_4`=extra4,
				`EXTRA_5`=extra5;

			set aff_id=last_insert_id();

			call product_tags_bind(aff_id,itags);
			call product_crits_bind(aff_id,icrits1);
			call product_crits_bind(aff_id,icrits2);
			call product_crits_bind(aff_id,icrits3);
            
            call product_spec_groups_bind(aff_id,ispecs_inh,'PINH');
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_product(acl_uid,ipid)=1) then
			select P_P0 into prnt from PRODUCTS where P_ID=ipid;
			if prnt is null then
				select IF(N_ID=inid,P_ORDER,(select ifnull(max(ps.P_ORDER),0)+1 from PRODUCTS ps where ps.N_ID=inid)) into uorder from PRODUCTS where P_ID=ipid;
				set uorder_chld=0;
			else
				select P_ORDER into uorder from PRODUCTS where P_ID=prnt;
				select P_ORDER_CHLD into uorder_chld from PRODUCTS where P_ID=ipid;
			end if;
			
			select P_PICT_URI into old_pict_uri from PRODUCTS where P_ID=ipid;
			set new_pict_uri=prod_check_newuri_globalpict(ipid,IFNULL(iuri_name,make_uri(iname)));
			update PRODUCTS 
			set 
				`D_MODIFY_PRICE`=if(`P_PRICE`=iprice and (PT_ID is null and iprice_type is null or PT_ID=iprice_type),`D_MODIFY_PRICE`,now()),
				`N_ID`=inid,
				`P_URI`=prod_check_newuri(inid,ipid,IFNULL(iuri_name,make_uri(iname))),
				`P_NAME`=iname,
				`P_FULLNAME`=ifullname,
				`P_TITLE`=ititle,
				`P_VARIANT`=ivariant,
				`P_KEYWORDS`=ikeywords,
				`P_DESCR`=idescription,
				`P_CSS`=icss,
				`P_CODE`=icode,
				`P_BARCODE`=ibarcode,
				`B_ID`=ibrand_id,
				`P_MEASURE`=imeasure,
				`P_SIZE`=isize,
				`P_DESCR_SHORT`=idescr_short,
				`P_DESCR_FULL`=idescr_full,
				`P_DESCR_TECH`=idescr_tech,
				`PT_ID`=iprice_type,
				`P_PRICE`=iprice,
				`P_PRICE_OLD`=ioprice,
				`PV_ID`=iavail_id,
				`P_AVAIL_COUNT`=iavail_num,
				`IS_NEW`=ifnew,
				`IS_RECOMEND`=ifrecomend,
				`IS_SPECIAL`=ifspecial,
				`P_FORSALE`=iforsale,
				`D_MODIFY`=now(),
				`P_ORDER`=uorder,
				`P_ORDER_CHLD`=uorder_chld,
			 	`P_HIDE`=not ifshow,
				`P_PICT_URI`=new_pict_uri,
				`EXTRA_1`=extra1,
				`EXTRA_2`=extra2,
				`EXTRA_3`=extra3,
				`EXTRA_4`=extra4,
				`EXTRA_5`=extra5
			where P_ID=ipid;

			call product_tags_clear(ipid);
			call product_tags_bind(ipid,itags);
			call product_tags_wipe;	
			call product_crits_clear(ipid);
			call product_crits_bind(ipid,icrits1);
			call product_crits_bind(ipid,icrits2);
			call product_crits_bind(ipid,icrits3);
            
            call product_spec_groups_clear(ipid);
            call product_spec_groups_bind(ipid,ispecs_inh,'PINH');

			set aff_id=ipid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id, old_pict_uri, new_pict_uri;
	call inherit_product(aff_id);
	call node_tree_recalc_counters;
END $$

DELIMITER $$
DROP procedure IF EXISTS inherit_product$$
CREATE  procedure `inherit_product`(pid bigint) 
BEGIN
	declare itags tinyint;
	declare icrit tinyint;
	declare prnt bigint;


	update PRODUCTS p
	inner join PRODUCTS c on p.P_ID=c.P_P0
	left join PRODUCTS_INHERIT pi on pi.P_ID=c.P_ID
	set 
		c.P_NAME=if(ifnull(pi.P_NAME,0)=0,p.P_NAME,c.P_NAME),
		c.P_FULLNAME=if(ifnull(pi.P_FULLNAME,0)=0,p.P_FULLNAME,c.P_FULLNAME),
		c.P_TITLE=if(ifnull(pi.P_TITLE,0)=0,p.P_TITLE,c.P_TITLE),
		c.P_KEYWORDS=if(ifnull(pi.P_KEYWORDS,0)=0,p.P_KEYWORDS,c.P_KEYWORDS),
		c.P_DESCR=if(ifnull(pi.P_DESCR,0)=0,p.P_DESCR,c.P_DESCR),
		c.P_CSS=if(ifnull(pi.P_CSS,0)=0,p.P_CSS,c.P_CSS),
		c.P_CODE=if(ifnull(pi.P_CODE,0)=0,p.P_CODE,c.P_CODE),
		c.P_BARCODE=if(ifnull(pi.P_BARCODE,0)=0,p.P_BARCODE,c.P_BARCODE),
		c.B_ID=if(ifnull(pi.B_ID,0)=0,p.B_ID,c.B_ID),
		c.P_MEASURE=if(ifnull(pi.P_MEASURE,0)=0,p.P_MEASURE,c.P_MEASURE),
		c.P_SIZE=if(ifnull(pi.P_SIZE,0)=0,p.P_SIZE,c.P_SIZE),
		c.P_DESCR_SHORT=if(ifnull(pi.P_DESCR_SHORT,0)=0,p.P_DESCR_SHORT,c.P_DESCR_SHORT),
		c.P_DESCR_FULL=if(ifnull(pi.P_DESCR_FULL,0)=0,p.P_DESCR_FULL,c.P_DESCR_FULL),
		c.P_DESCR_TECH=if(ifnull(pi.P_DESCR_TECH,0)=0,p.P_DESCR_TECH,c.P_DESCR_TECH),
        c.P_PRICE=if(ifnull(pi.P_PRICE,0)=0,p.P_PRICE,c.P_PRICE),
        c.P_PRICE_OLD=if(ifnull(pi.P_PRICE,0)=0,p.P_PRICE_OLD,c.P_PRICE_OLD),
		c.IS_NEW=if(ifnull(pi.IS_NEW,0)=0,p.IS_NEW,c.IS_NEW),
		c.IS_RECOMEND=if(ifnull(pi.IS_RECOMEND,0)=0,p.IS_RECOMEND,c.IS_RECOMEND),
		c.IS_SPECIAL=if(ifnull(pi.IS_SPECIAL,0)=0,p.IS_SPECIAL,c.IS_SPECIAL),
		c.P_PICT_URI=if(ifnull(pi.P_PICT_URI,0)=0,p.P_PICT_URI,c.P_PICT_URI),
		c.PT_ID=p.PT_ID
	where p.P_ID=pid;

	
	-- select PRODUCT_TAGS,PRODUCT_CRETERIAS into itags,icrit from PRODUCTS_INHERIT where P_ID=pid;
	
	delete from pc
	using PRODUCTS p
	inner join PRODUCTS c on c.P_P0=p.P_ID
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=c.P_ID -- key string
	left  join PRODUCTS_INHERIT pi on pi.P_ID=c.P_ID
	where ifnull(pi.PRODUCT_CRETERIAS,0)=0
	;

	insert into PRODUCT_CRETERIAS(CR_ID,P_ID,PC_SELF)
	select pc.CR_ID,c.P_ID,0 as PC_SELF
	from PRODUCTS p
	inner join PRODUCTS c on c.P_P0=p.P_ID
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=p.P_ID  -- key string
	left  join PRODUCTS_INHERIT pi on pi.P_ID=c.P_ID
	where ifnull(pi.PRODUCT_CRETERIAS,0)=0;

	delete from pt
	using PRODUCTS p
	inner join PRODUCTS c on c.P_P0=p.P_ID
	inner join PRODUCT_TAGS pt on pt.P_ID=c.P_ID -- key string
	left  join PRODUCTS_INHERIT pi on pi.P_ID=c.P_ID
	where ifnull(pi.PRODUCT_TAGS,0)=0
	;

	insert into PRODUCT_TAGS(T_ID,P_ID)
	select pt.T_ID,c.P_ID
	from PRODUCTS p
	inner join PRODUCTS c on c.P_P0=p.P_ID
	inner join PRODUCT_TAGS pt on pt.P_ID=p.P_ID  -- key string
	left  join PRODUCTS_INHERIT pi on pi.P_ID=c.P_ID
	where ifnull(pi.PRODUCT_TAGS,0)=0;

/*	drop temporary table if exists PC_KINDS_CHILD;
	create temporary table PC_KINDS_CHILD(P_ID bigint,CR_KIND char(4),PC_SELF tinyint) engine = heap;
	
	insert into PC_KINDS_CHILD(P_ID,CR_KIND,PC_SELF)
	select c.P_ID,cr.CR_KIND,max(pc.PC_SELF)
	from PRODUCTS c
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=c.P_ID
	inner join CRITERIAS cr on cr.CR_ID=pc.CR_ID
	where c.P_P0=pid
	group by c.P_ID,cr.CR_KIND;

	drop temporary table if exists PC_KINDS_PARENT;
	create temporary table PC_KINDS_PARENT(P_ID bigint,CR_KIND char(4)) engine = heap;

	insert into PC_KINDS_PARENT(P_ID,CR_KIND)
	select pc.P_ID,cr.CR_KIND
	from PRODUCT_CRETERIAS pc 
	inner join CRITERIAS cr on cr.CR_ID=pc.CR_ID
	where pc.P_ID=pid;

	delete pc 
	-- select kp.*,kc.*,p.P_ID 
	from PC_KINDS_PARENT kp
	cross join PRODUCTS p on p.P_P0=pid
	inner join PC_KINDS_CHILD kc on kp.CR_KIND=kc.CR_KIND and kc.P_ID=p.P_ID
	inner join CRITERIAS cr on cr.CR_KIND=kc.CR_KIND
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=p.P_ID and cr.CR_ID=pc.CR_ID
	where ifnull(kc.PC_SELF,0)=0
	;
 
	insert into PRODUCT_CRETERIAS(P_ID,CR_ID,PC_SELF)
	-- select kp.*,kc.*,p.P_ID,pc.CR_ID,cr.CR_NAME
	select distinct p.P_ID,pc.CR_ID,0 as PC_SELF
	from PC_KINDS_PARENT kp
	cross join PRODUCTS p on p.P_P0=pid
	left join PC_KINDS_CHILD kc on kp.CR_KIND=kc.CR_KIND and kc.P_ID=p.P_ID
	inner join CRITERIAS cr on cr.CR_KIND=kp.CR_KIND
	inner join PRODUCT_CRETERIAS pc on pc.P_ID=kp.P_ID and cr.CR_ID=pc.CR_ID
	where ifnull(kc.PC_SELF,0)=0
	;
*/
	select P_P0 into prnt from PRODUCTS where P_ID=pid;

	update PRODUCTS r 
	inner join (
		select p.P_ID,min(c.P_PRICE) as P_PRICE_MIN,max(c.P_PRICE) as P_PRICE_MAX
		from PRODUCTS p
		inner join PRODUCTS c on c.P_P0=p.P_ID
		where p.P_ID=prnt
		group by p.P_ID
	) c on c.P_ID=r.P_ID
	set r.P_PRICE_MIN=c.P_PRICE_MIN,r.P_PRICE_MAX=c.P_PRICE_MAX
	;
	
END$$

delimiter $$
DROP procedure IF EXISTS update_product_inherit$$
CREATE PROCEDURE update_product_inherit(in acl_uid bigint, in ipid bigint,
	in iname tinyint,
	in ifullname tinyint,
	in ititle tinyint,
	in ikeywords tinyint,
	in idescription tinyint,
	in icss tinyint,
	in icode tinyint,
	in ibarcode tinyint,
	in ibrand_id tinyint,
	in imeasure tinyint,
	in isize tinyint,
	in idescr_short tinyint,
	in idescr_full tinyint,
	in idescr_tech tinyint,
    in iprice tinyint,
	in ifnew tinyint,
	in ifrecomend tinyint,
	in ifspecial tinyint,

	in iprod_tags tinyint,
	in iprod_crit tinyint
)
BEGIN
	declare cnt int;
	select count(*) into cnt from PRODUCTS_INHERIT where P_ID=ipid;

	
	if cnt=0 then
			insert into PRODUCTS_INHERIT
			set 
				`P_ID`=ipid,
				`P_NAME`=iname,
				`P_FULLNAME`=ifullname,
				`P_TITLE`=ititle,
				`P_KEYWORDS`=ikeywords,
				`P_DESCR`=idescription,
				`P_CSS`=icss,
				`P_CODE`=icode,
				`P_BARCODE`=ibarcode,
				`B_ID`=ibrand_id,
				`P_MEASURE`=imeasure,
				`P_SIZE`=isize,
				`P_DESCR_SHORT`=idescr_short,
				`P_DESCR_FULL`=idescr_full,
				`P_DESCR_TECH`=idescr_tech,
                `P_PRICE`=iprice,
				`IS_NEW`=ifnew,
				`IS_RECOMEND`=ifrecomend,
				`IS_SPECIAL`=ifspecial,

				`PRODUCT_TAGS`=iprod_tags,
				`PRODUCT_CRETERIAS`=iprod_crit
				;
	else 
			update PRODUCTS_INHERIT 
			set 				
				`P_NAME`=iname,
				`P_FULLNAME`=ifullname,
				`P_TITLE`=ititle,
				`P_KEYWORDS`=ikeywords,
				`P_DESCR`=idescription,
				`P_CSS`=icss,
				`P_CODE`=icode,
				`P_BARCODE`=ibarcode,
				`B_ID`=ibrand_id,
				`P_MEASURE`=imeasure,
				`P_SIZE`=isize,
				`P_DESCR_SHORT`=idescr_short,
				`P_DESCR_FULL`=idescr_full,
				`P_DESCR_TECH`=idescr_tech,
                `P_PRICE`=iprice,
				`IS_NEW`=ifnew,
				`IS_RECOMEND`=ifrecomend,
				`IS_SPECIAL`=ifspecial,

				`PRODUCT_TAGS`=iprod_tags,
				`PRODUCT_CRETERIAS`=iprod_crit
			where P_ID=ipid;
	end if;
END$$




DELIMITER $$
DROP PROCEDURE IF EXISTS create_product_variant $$
/*
	call create_product_variant(:acl_uid, :pid, :variant)
*/
CREATE PROCEDURE `create_product_variant`(in acl_uid bigint, in ipid bigint,
	in ivariant varchar(255),ifshow tinyint, iprice float, iprice_inh tinyint, iforsale tinyint
)
BEGIN
	declare npid bigint;

	insert into PRODUCTS(
		`N_ID`,`P_P0`,`P_VOID`,PT_ID,
		`P_URI`,
		`P_VARIANT`,
		`P_NAME`,`P_FULLNAME`,`P_TITLE`,`P_KEYWORDS`,
		`P_DESCR`,`P_CSS`,`P_CODE`,`P_BARCODE`,`B_ID`,		
		`P_MEASURE`,`P_SIZE`,`P_DESCR_SHORT`,`P_DESCR_FULL`,
		`P_DESCR_TECH`,`IS_NEW`,`IS_RECOMEND`,`IS_SPECIAL`,
		`P_ORDER_CHLD`,
		`P_PICT_URI`,
        `D_CREATE`,
		`P_PRICE`, `P_PRICE_OLD`,
        `P_HIDE`,`P_FORSALE`
	)
	select 
		`N_ID`,ipid as P_P0,P_VOID,PT_ID,
		prod_check_newuri(N_ID,null,concat(P_URI,'-',make_uri(ivariant))) as P_URI,
		ivariant as P_VARIANT,
		`P_NAME`,`P_FULLNAME`,`P_TITLE`,`P_KEYWORDS`,	
		`P_DESCR`,`P_CSS`,`P_CODE`,`P_BARCODE`,`B_ID`,		
		`P_MEASURE`,`P_SIZE`,`P_DESCR_SHORT`,`P_DESCR_FULL`,
		`P_DESCR_TECH`,`IS_NEW`,`IS_RECOMEND`,`IS_SPECIAL`,
		(select ifnull(max(ps.P_ORDER_CHLD),0)+1 from PRODUCTS ps where ps.P_P0=ipid) as P_ORDER_CHLD,
		prod_check_newuri_globalpict(null,
										prod_check_newuri(N_ID,null,concat(P_URI,'-',make_uri(ivariant)))
									) as P_PICT_URI,
		now() as D_CREATE,
		if(iprice_inh,iprice,P_PRICE), if(iprice_inh,null,P_PRICE_OLD),
        not ifshow,iforsale
	from PRODUCTS where P_ID=ipid;

	set npid = last_insert_id();

	if iprice_inh then
		insert into PRODUCTS_INHERIT
		set P_ID=npid,
			P_PRICE=iprice_inh;
	end if;

	insert into PRODUCT_CRETERIAS(CR_ID,P_ID,PC_SELF)
	select CR_ID,npid as P_ID,0 as PC_SELF from PRODUCT_CRETERIAS where P_ID=ipid;

	insert into PRODUCT_TAGS(P_ID,T_ID)
	select npid as P_ID,T_ID from PRODUCT_TAGS  where P_ID=ipid;
	
	select vp.*,
		p.P_PICT_URI as parent_pict_uri 
	from viewPRODUCTS vp 
	inner join PRODUCTS p on p.P_ID=vp.parent_id
	where pid=npid;
	
	call node_tree_recalc_counters ();
 
END$$



DELIMITER $$
DROP PROCEDURE IF EXISTS update_product_part $$
/*
	call update_product(:acl_uid, :pid, :price, :fshow, :fnew, :frecomend, :fspecial)
*/
CREATE PROCEDURE update_product_part(in acl_uid bigint, in ipid bigint,
	in iprice double,
	in ifshow tinyint,
	in ifnew tinyint,
	in ifrecomend tinyint,
	in ifspecial tinyint
)
BEGIN
	declare inh_price tinyint;
    
    if(acl_canmodify_product(acl_uid,ipid)=1) then
		if (select P_P0 from PRODUCTS where P_ID=ipid) is null then
			update PRODUCTS 
			set 
				`D_MODIFY`=now(),
				`D_MODIFY_PRICE`=if(iprice=P_PRICE,D_MODIFY_PRICE,NOW()),
				`P_PRICE`=iprice,
				`IS_NEW`=ifnew,
				`IS_RECOMEND`=ifrecomend,
				`IS_SPECIAL`=ifspecial,
				`P_HIDE`=not ifshow
			where P_ID=ipid;
            
            call inherit_product(ipid);
        else
			select (P_PRICE<>iprice) into inh_price from PRODUCTS where P_ID=ipid;
            
            if (select P_ID from PRODUCTS_INHERIT where P_ID=ipid) is null then
				insert into PRODUCTS_INHERIT
				set P_ID=ipid,
					P_PRICE=inh_price;
            else
				update PRODUCTS_INHERIT
				set P_PRICE=if(inh_price,1,P_PRICE)
				where P_ID=ipid;
            end if;
            
            update PRODUCTS 
			set 
				`D_MODIFY`=now(),
				`D_MODIFY_PRICE`=if(iprice=P_PRICE,D_MODIFY_PRICE,NOW()),
				`P_PRICE`=iprice,
				`P_HIDE`=not ifshow
			where P_ID=ipid;
				
        end if;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END$$


DELIMITER $$
DROP PROCEDURE IF EXISTS get_product_picts $$
CREATE PROCEDURE get_product_picts (IN pid BIGINT)
BEGIN
	select
		p.P_ID as pid,
		pp.PP_N as pn,
		pp.PP_NAME as name,
		concat(p.P_PICT_URI,"_",LPAD(pp.PP_N,3,'0')) as pict_uri
	from PRODUCTS p
	inner join PRODUCT_PICTS pp on p.P_ID=pp.P_ID
	where pp.P_ID=pid
	order by pp.PP_N;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_product_picts_fn $$
CREATE PROCEDURE get_product_picts_fn (IN pid BIGINT, IN num INT)
BEGIN
	declare i int;

	drop temporary table if exists PNS;
	create temporary table PNS (pn INT) engine=heap;

	set i=1;
	while (i<=num) do
		insert into PNS set pn=i;
		set i=i+1;
	end while;

	select
		p.P_ID as pid,
		pn.pn as pn,
		IFNULL(pp.PP_NAME,'') as name,
		concat(p.P_PICT_URI,"_",LPAD(pn.pn,3,'0')) as pict_uri
	from PNS pn
	left join PRODUCT_PICTS pp on pp.P_ID=pid and pp.PP_N=pn.pn
	left join PRODUCTS p on p.P_ID=pid
	order by pn.pn;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_product_pict $$
/*
	call update_product_pict(:acl_uid, :pid, :pn, :name)
*/
CREATE PROCEDURE update_product_pict (in acl_uid bigint, in pid bigint, in pn int, in pname text)
BEGIN
	if(acl_canmodify_product(acl_uid,pid)=1) then
		if (pname='') then
			delete from PRODUCT_PICTS
			where P_ID=pid and PP_N=pn;
		else
			replace into PRODUCT_PICTS
			set
				P_ID=pid,
				PP_N=pn,
				PP_NAME=pname;
		end if;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_product_pict $$
/*
	call delete_product_pict(:acl_uid, :pid, :pn)
*/
CREATE PROCEDURE delete_product_pict (in acl_uid bigint, in pid bigint, in pn int)
BEGIN
	if(acl_canmodify_product(acl_uid,pid)=1) then
		delete from PRODUCT_PICTS
		where P_ID=pid and PP_N=pn;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$



-- --------------------------------------------------------------------
#   CATEGORY MODIFCATION

DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_category $$
CREATE FUNCTION acl_cancreate_category (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_category $$
CREATE FUNCTION acl_canmodify_category (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_category $$
CREATE FUNCTION acl_candelete_category (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS update_category $$
/*
call update_category(:acl_uid, :nid, :np, :uri_name, :fshow, :name, :fullname, :title,
	:keywords, :description, :css, :text_top, :text_bott,
    :specs)
*/
CREATE PROCEDURE update_category(in acl_uid bigint, in inid bigint,
	in inp bigint,
	in iuri_name varchar(128),
	in ifshow tinyint,
	in iname text,
	in ifullname text,
	in ititle text,
	in ikeywords mediumtext,
	in idescription mediumtext,
	in icss varchar(128),
	in itext_top longtext,
	in itext_bott longtext,
    in ispecs_t text
)
BEGIN
	declare aff_id bigint;
	declare uorder int;

	declare old_pict_uri varchar(128);
	declare new_pict_uri varchar(128);

	set old_pict_uri=null;
	set new_pict_uri=null;

	if ifnull(inid,0)=0 then
		if(acl_cancreate_category(acl_uid)=1) then
			insert into NODES
			set 
				`N_P0`=inp,
				`N_KIND`='CAT',
				`N_VOID`=0,
				`N_URI`=node_check_newuri(inp,null,IFNULL(iuri_name,make_uri(iname))),
				`N_ORDER`=(select ifnull(max(nso.N_ORDER),0)+1 from NODES nso where nso.N_P0=inp),
				`N_HIDE`=not ifshow;

			set aff_id=last_insert_id();

			set new_pict_uri=cat_check_newuri_globalpict(null,IFNULL(iuri_name,make_uri(iname)));

			insert into CATEGORIES
			set 
				`N_ID`=aff_id,
				`CAT_NAME`=iname,
				`CAT_FULLNAME`=ifullname,
				`CAT_TITLE`=ititle,
				`CAT_KEYWORDS`=ikeywords,
				`CAT_DESCR`=idescription,
				`CAT_CSS`=icss,
				`TEXT_TOP`=itext_top,
				`TEXT_BOTT`=itext_bott,
				`CAT_PICT_URI`=new_pict_uri;
                
			call node_spec_groups_bind(aff_id,ispecs_t,'TREE');
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_category(acl_uid,inid)=1) then
			select if(N_P0=inp,N_ORDER,(select ifnull(max(nso.N_ORDER),0)+1 from NODES nso where nso.N_P0=inp)) into uorder from NODES where N_ID=inid;
			update NODES 
			set 
				`N_P0`=inp,
				`N_URI`=node_check_newuri(inp,inid,IFNULL(iuri_name,make_uri(iname))),
				`N_HIDE`=not ifshow,
				`N_ORDER`=uorder
			where N_ID=inid;

			set aff_id=inid;

			select CAT_PICT_URI into old_pict_uri from CATEGORIES where N_ID=aff_id;
			set new_pict_uri=cat_check_newuri_globalpict(inid,IFNULL(iuri_name,make_uri(iname)));

			update CATEGORIES 
			set 
				`CAT_NAME`=iname,
				`CAT_FULLNAME`=ifullname,
				`CAT_TITLE`=ititle,
				`CAT_KEYWORDS`=ikeywords,
				`CAT_DESCR`=idescription,
				`CAT_CSS`=icss,
				`TEXT_TOP`=itext_top,
				`TEXT_BOTT`=itext_bott,
				`CAT_PICT_URI`=new_pict_uri
			where N_ID=aff_id;
            
			call node_spec_groups_clear(inid);
            call node_spec_groups_bind(inid,ispecs_t,'TREE');
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id, old_pict_uri, new_pict_uri;
	call node_tree_recalc_counters;
END$$

DELIMITER $$
drop procedure if exists move_node_after$$
create procedure move_node_after( nid int, nafter int)
begin
	/*nafter : 0-first, -1-last*/
	declare afo int;
	declare slo int;	
	declare afp0 int;
	declare slp0 int;

	
	select N_ORDER,N_P0 into slo,slp0 from NODES where N_ID=nid;
	set afp0=slp0;
	if (nafter=-1) then
		select N_ORDER,N_ID into afo,nafter from NODES where ifnull(N_P0,0)=ifnull(slp0,0) 
		order by N_ORDER desc limit 0,1;
	else if (nafter=0) then
		set afo=0;
		else
			select N_ORDER,N_P0 into afo,afp0 from NODES where N_ID=nafter;
		end if;
	end if;

	if(ifnull(slp0,0)=ifnull(afp0,0) and nafter is not null)
	then
		start transaction;
			-- select afo,afp0,slo,slp0;
			-- select PG_ID,PG_ORDER from PAGES order by PG_ORDER;
			update NODES set N_ORDER=N_ORDER-1 where N_ORDER>slo and N_ORDER<=afo and ifnull(N_P0,0)=ifnull(slp0,0) ;
			update NODES set N_ORDER=N_ORDER+1 where N_ORDER>afo and N_ORDER<slo and ifnull(N_P0,0)=ifnull(slp0,0) ;
			select N_ORDER+1 into slo from NODES where nafter<>0 and N_ID=nafter and ifnull(N_P0,0)=ifnull(slp0,0) ;
			update NODES set N_ORDER=if(nafter=0,1,slo) where N_ID=nid;
			-- select PG_ID,PG_ORDER from PAGES order by PG_ORDER;
		commit;
	end if;
end$$

DELIMITER $$
drop procedure if exists move_node_up$$
create procedure move_node_up(nid int)
begin
	declare my_ord int;
	declare s_id bigint; 
	declare s_ord int;
	declare p0 bigint;

	set s_id=-1;
	select N_ORDER, N_P0 into my_ord,p0 from NODES where N_ID=nid;
	select N_ID,N_ORDER into s_id,s_ord from NODES where N_P0=p0 and N_ORDER<my_ord order by N_ORDER desc limit 1;
	if (s_id<>-1) then
		update NODES set N_ORDER=s_ord where N_ID=nid;
		update NODES set N_ORDER=my_ord where N_ID=s_id;
	end if;
end$$

DELIMITER $$
drop procedure if exists move_node_down$$
create procedure move_node_down(nid int)
begin
	declare my_ord int;
	declare s_id bigint; 
	declare s_ord int;
	declare p0 bigint;

	set s_id=-1;
	select N_ORDER, N_P0 into my_ord,p0 from NODES where N_ID=nid;
	select N_ID,N_ORDER into s_id,s_ord from NODES where N_P0=p0 and N_ORDER>my_ord order by N_ORDER asc limit 1;
	if (s_id<>-1) then
		update NODES set N_ORDER=s_ord where N_ID=nid;
		update NODES set N_ORDER=my_ord where N_ID=s_id;
	end if;
end$$

DELIMITER $$
/* call delete_product(:uid,:pid)*/
DROP procedure IF EXISTS delete_product$$
CREATE  procedure `delete_product`(uid bigint,pid bigint) 
BEGIN
	if (acl_candelete_product(uid,pid)=1) then
		drop temporary table if exists p;
		create temporary table p(P_ID bigint, P_PICT_URI varchar(128)) engine = heap;
		
		insert into p(P_ID, P_PICT_URI)
		select P_ID, P_PICT_URI from PRODUCTS where P_ID=pid;
	
		insert into p(P_ID, P_PICT_URI)
		select P_ID, P_PICT_URI from PRODUCTS where P_P0=pid;

		delete from cd
		using CART_DETAILS cd
		inner join p on p.P_ID=cd.P_ID;

		delete from pc
		using PRODUCT_CRETERIAS pc
		inner join p on p.P_ID=pc.P_ID;

		delete from pt
		using PRODUCT_TAGS pt
		inner join p on p.P_ID=pt.P_ID;
		
		call product_tags_wipe ();

		delete from pp
		using PRODUCT_PICTS pp
		inner join p on p.P_ID=pp.P_ID;

		update ORDER_DETAILS od
		inner join p on p.P_ID=od.P_ID
		set od.P_ID=null;

		delete from ps
		using PRODUCT_SPECS ps
		inner join p on p.P_ID=ps.P_ID;

		delete from pl
		using PRODUCTS_LINKS pl
		inner join p on p.P_ID in (pl.P_ID_M, pl.P_ID_S);

		delete from psg
		using PRODUCT_SPEC_GROUPS psg
		inner join p on p.P_ID=psg.P_ID;

		delete from pi
		using p 
		inner join PRODUCTS_INHERIT pi on p.P_ID=pi.P_ID;

		delete from pd
		using p 
		inner join PRODUCTS pd on p.P_ID=pd.P_ID and pd.P_P0 is not null;

		delete from pd
		using p 
		inner join PRODUCTS pd on p.P_ID=pd.P_ID and pd.P_P0 is null;

		call node_tree_recalc_counters;

		select distinct P_PICT_URI as old_pict_uri from p where P_PICT_URI is not null;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END$$

DELIMITER $$
/* call delete_category(:uid,:cid)*/
DROP PROCEDURE IF EXISTS delete_category $$
CREATE PROCEDURE delete_category (in uid bigint, in cid bigint)
BEGIN
	declare maxlev int;
	if (acl_candelete_category(uid,cid)=1) then
		drop temporary table if exists ct;
		create temporary table ct(N_ID bigint, CAT_PICT_URI varchar(128), N_LEV_DIFF int) engine=heap;

		insert into ct (N_ID, CAT_PICT_URI, N_LEV_DIFF)
		select n.N_ID, c.CAT_PICT_URI, 0
		from NODES n
		inner join CATEGORIES c on c.N_ID=n.N_ID
		where n.N_ID=cid;

		insert into ct (N_ID, CAT_PICT_URI, N_LEV_DIFF)
		select n.N_ID, c.CAT_PICT_URI, i.N_LEV_DIFF
		from NODES n
		inner join CATEGORIES c on c.N_ID=n.N_ID
		inner join NODE_INCAPS i on i.N_C=n.N_ID and i.N_P=cid;

		drop temporary table if exists pr;
		create temporary table pr(P_ID bigint, P_PICT_URI varchar(128)) engine=heap;

		insert into pr (P_ID, P_PICT_URI)
		select distinct p.P_ID, p.P_PICT_URI
		from ct
		inner join PRODUCTS p on p.N_ID=ct.N_ID;

		insert into pr (P_ID, P_PICT_URI)
		select distinct pc.P_ID, pc.P_PICT_URI
		from ct
		inner join PRODUCTS p on p.N_ID=ct.N_ID
		inner join PRODUCTS pc on pc.P_P0=p.P_ID;

		delete from cd
		using CART_DETAILS cd
		inner join pr on pr.P_ID=cd.P_ID;

		delete from pc
		using PRODUCT_CRETERIAS pc 
		inner join pr on pr.P_ID=pc.P_ID;

		delete from pt
		using PRODUCT_TAGS pt
		inner join pr on pr.P_ID=pt.P_ID;   

		call product_tags_wipe ();

		delete from pp
		using PRODUCT_PICTS pp
		inner join pr on pr.P_ID=pp.P_ID;

		update ORDER_DETAILS od
		inner join pr on pr.P_ID=od.P_ID
		set od.P_ID=null;

		delete from ps
		using PRODUCT_SPECS ps
		inner join pr on pr.P_ID=ps.P_ID;

		delete from psg
		using PRODUCT_SPEC_GROUPS psg
		inner join pr on pr.P_ID=psg.P_ID;

		delete from pl
		using PRODUCTS_LINKS pl
		inner join pr on pr.P_ID in (pl.P_ID_M, pl.P_ID_S);

		delete from pi
		using pr
		inner join PRODUCTS_INHERIT pi on pr.P_ID=pi.P_ID;

		delete from pd
		using pr
		inner join PRODUCTS pd on pr.P_ID=pd.P_ID and pd.P_P0 is not null;

		delete from pd
		using pr
		inner join PRODUCTS pd on pr.P_ID=pd.P_ID and pd.P_P0 is null;

		delete from nsg
		using ct
		inner join NODE_SPEC_GROUPS nsg on nsg.N_ID=ct.N_ID;

		-- cat

		select max(N_LEV_DIFF) into maxlev from ct;
		while maxlev>=0 do
			
			delete from c
			using ct
			inner join CATEGORIES c on c.N_ID=ct.N_ID
			where ct.N_LEV_DIFF=maxlev;

			delete from n
			using ct
			inner join NODES n on n.N_ID=ct.N_ID
			where ct.N_LEV_DIFF=maxlev;

			set maxlev=maxlev-1;

		end while;
		
		call node_tree_recalc_counters;

		select distinct CAT_PICT_URI as old_pict_uri from ct;
		select distinct P_PICT_URI as old_pict_uri_prod from pr;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$


DELIMITER $$
drop procedure if exists products_reorder $$
create procedure products_reorder(in idlist text)
begin
	declare i int;
	declare lid bigint;
	drop temporary table if exists lst;
	create temporary table lst(P_ID bigint,IN_ORD int) engine = heap;
	drop temporary table if exists lst2;
	create temporary table lst2(P_ID bigint,P_ORD int,OUT_ORD int) engine = heap;
	set i=1;
	repeat
		set lid=str_part_int(idlist,',',i,null);
		if lid is not null then
			insert into lst
			set P_ID=lid,IN_ORD=i;
		end if;
		set i=i+1;
	until lid is null 
	end repeat;
	
	insert into lst2(P_ID,P_ORD,OUT_ORD)
	select s.P_ID,s.P_ORDER,@a:=@a+1
	from (
		select p.P_ID, p.P_ORDER
		from lst l
		inner join PRODUCTS p on p.P_ID=l.P_ID
		order by p.P_ORDER asc
	) s
	,(select @a:=0) a
	order by s.P_ORDER asc;

	update PRODUCTS p
	inner join lst on lst.P_ID=p.P_ID
	inner join lst2 on lst.IN_ORD=lst2.OUT_ORD 
	set P_ORDER = lst2.P_ORD;
	
end$$

DELIMITER $$
drop procedure if exists products_chld_reorder $$
create procedure products_chld_reorder(in idlist text)
begin
	declare i int;
	declare lid bigint;
	drop temporary table if exists lst;
	create temporary table lst(P_ID bigint,IN_ORD int) engine = heap;
	drop temporary table if exists lst2;
	create temporary table lst2(P_ID bigint,P_ORD int,OUT_ORD int) engine = heap;
	set i=1;
	repeat
		set lid=str_part_int(idlist,',',i,null);
		if lid is not null then
			insert into lst
			set P_ID=lid,IN_ORD=i;
		end if;
		set i=i+1;
	until lid is null 
	end repeat;
	
	insert into lst2(P_ID,P_ORD,OUT_ORD)
	select s.P_ID,s.P_ORDER_CHLD,@a:=@a+1
	from (
		select p.P_ID, p.P_ORDER_CHLD
		from lst l
		inner join PRODUCTS p on p.P_ID=l.P_ID
		order by p.P_ORDER_CHLD asc
	) s
	,(select @a:=0) a
	order by s.P_ORDER_CHLD asc;

	update PRODUCTS p
	inner join lst on lst.P_ID=p.P_ID
	inner join lst2 on lst.IN_ORD=lst2.OUT_ORD 
	set P_ORDER_CHLD = lst2.P_ORD;
	
end$$


/* SPEC GROUPS */

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_groups_get $$
CREATE PROCEDURE spec_groups_get()
BEGIN
	select *
    from viewSPEC_GROUPS
    order by sg_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_groups_update $$
CREATE PROCEDURE spec_groups_update(in isgid bigint)
BEGIN
	
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_groups_delete $$
CREATE PROCEDURE spec_groups_delete(in isgid bigint)
BEGIN
	
END$$


/* SPEC CLASSES */

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_class_get $$
CREATE PROCEDURE spec_class_get(in iscid bigint)
BEGIN
	select *
    from viewSPEC_CLASSES
    where scid=iscid;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get $$
CREATE PROCEDURE spec_classes_get()
BEGIN
	select *
    from viewSPEC_CLASSES
    order by g_order, sg_order, sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_all_by_groups $$
CREATE PROCEDURE spec_classes_get_all_by_groups()
BEGIN
	select *
    from viewSPEC_CLASSES
    order by sg_order, sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_rb $$
CREATE PROCEDURE spec_classes_get_rb()
BEGIN
	select *
    from viewSPEC_CLASSES
    where datatype in ('REFBOOK','REFBOOK+')
	order by sg_order, sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_by_product_T_SC $$
CREATE PROCEDURE spec_classes_get_by_product_T_SC(in ipid bigint)
BEGIN
	drop temporary table if exists T_SC2;
    create temporary table T_SC2 (SC_ID bigint) engine=memory;
    
    drop temporary table if exists T_SC;
    create temporary table T_SC (SC_ID bigint, BIND char(4)) engine=memory;
    
    insert into T_SC (SC_ID,BIND)
    select sc.SC_ID, ifnull(sgs2.BIND,'====')
    from (
		select sgs.SG_ID, if(COUNT(distinct sgs.BIND)=1,sgs.BIND,'++++') as BIND
		from (
            select cp.SG_ID, 'BASE' as BIND
			from PRODUCTS p
			inner join PRODUCT_SPEC_GROUPS cp on cp.P_ID=p.P_ID and (cp.PSG_KIND is null or cp.PSG_KIND='BASE')
			where p.P_ID=ipid
			
			union
			
			select cpp.SG_ID, 'PINH' as BIND
			from PRODUCTS p
			inner join PRODUCT_SPEC_GROUPS cpp on cpp.P_ID=p.P_P0 and (cpp.PSG_KIND is null or cpp.PSG_KIND='PINH')
			where p.P_ID=ipid
			
			union
			
			select cc.SG_ID, 'NODE' as BIND
			from PRODUCTS p
			inner join NODE_SPEC_GROUPS cc on cc.N_ID=p.N_ID and (cc.NSG_KIND is null or cc.NSG_KIND='NODE' or cc.NSG_KIND='TREE')
			where p.P_ID=ipid
			
			union
			
			select cc.SG_ID, 'TREE' as BIND
			from PRODUCTS p
			inner join NODE_INCAPS i on i.N_C=p.N_ID
			inner join NODE_SPEC_GROUPS cc on cc.N_ID=i.N_P and (cc.NSG_KIND is null or cc.NSG_KIND='TREE')
			where p.P_ID=ipid
		) sgs
        group by sgs.SG_ID
    ) sgs2
    inner join SPEC_CLASSES sc on sc.SG_ID=sgs2.SG_ID
		or sc.SG_ID is null;
        
        
	insert into T_SC2 (SC_ID)
    select ps.SC_ID
    from PRODUCT_SPECS ps
    left join T_SC tsc on ps.SC_ID=tsc.SC_ID
    where ps.P_ID=ipid and tsc.SC_ID is null;
    
    insert into T_SC (SC_ID,BIND)
    select SC_ID, null from T_SC2;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_by_products_iT1_T_SC $$
CREATE PROCEDURE spec_classes_get_by_products_iT1_T_SC()
BEGIN
	drop temporary table if exists T_P_ID_1;
    create temporary table T_P_ID_1 (P_ID bigint) engine=memory;
	drop temporary table if exists T_P_ID_2;
    create temporary table T_P_ID_2 (P_ID bigint) engine=memory;
	drop temporary table if exists T_P_ID_3;
    create temporary table T_P_ID_3 (P_ID bigint) engine=memory;
	drop temporary table if exists T_P_ID_4;
    create temporary table T_P_ID_4 (P_ID bigint) engine=memory;
	insert into T_P_ID_1 (P_ID) select `ID` from iT1;
	insert into T_P_ID_2 (P_ID) select `ID` from iT1;
	insert into T_P_ID_3 (P_ID) select `ID` from iT1;
	insert into T_P_ID_4 (P_ID) select `ID` from iT1;

	
	drop temporary table if exists T_SC2;
    create temporary table T_SC2 (SC_ID bigint) engine=memory;
    
    drop temporary table if exists T_SC;
    create temporary table T_SC (SC_ID bigint, BIND char(4)) engine=memory;
    
    insert into T_SC (SC_ID,BIND)
    select sc.SC_ID, ifnull(sgs2.BIND,'====')
    from (
		select sgs.SG_ID, if(COUNT(distinct sgs.BIND)=1,sgs.BIND,'++++') as BIND
		from (
            select cp.SG_ID, 'BASE' as BIND
			from PRODUCTS p
			inner join T_P_ID_1 t on t.P_ID=p.P_ID
			inner join PRODUCT_SPEC_GROUPS cp on cp.P_ID=p.P_ID and (cp.PSG_KIND is null or cp.PSG_KIND='BASE')
			
			union
			
			select cpp.SG_ID, 'PINH' as BIND
			from PRODUCTS p
			inner join T_P_ID_2 t on t.P_ID=p.P_ID
			inner join PRODUCT_SPEC_GROUPS cpp on cpp.P_ID=p.P_P0 and (cpp.PSG_KIND is null or cpp.PSG_KIND='PINH')
			
			union
			
			select cc.SG_ID, 'NODE' as BIND
			from PRODUCTS p
			inner join T_P_ID_3 t on t.P_ID=p.P_ID
			inner join NODE_SPEC_GROUPS cc on cc.N_ID=p.N_ID and (cc.NSG_KIND is null or cc.NSG_KIND='NODE' or cc.NSG_KIND='TREE')
			
			union
			
			select cc.SG_ID, 'TREE' as BIND
			from PRODUCTS p
			inner join T_P_ID_4 t on t.P_ID=p.P_ID
			inner join NODE_INCAPS i on i.N_C=p.N_ID
			inner join NODE_SPEC_GROUPS cc on cc.N_ID=i.N_P and (cc.NSG_KIND is null or cc.NSG_KIND='TREE')
		) sgs
        group by sgs.SG_ID
    ) sgs2
    inner join SPEC_CLASSES sc on sc.SG_ID=sgs2.SG_ID
		or sc.SG_ID is null;
        
        
	insert into T_SC2 (SC_ID)
    select ps.SC_ID
    from PRODUCTS p
	inner join iT1 t on t.`ID`=p.P_ID
	inner join PRODUCT_SPECS ps on ps.P_ID=p.P_ID
    left join T_SC tsc on ps.SC_ID=tsc.SC_ID
    where tsc.SC_ID is null;
    
    insert into T_SC (SC_ID,BIND)
    select SC_ID, null from T_SC2;
END$$


DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_by_product $$
CREATE PROCEDURE spec_classes_get_by_product(in ipid bigint)
BEGIN
	call spec_classes_get_by_product_T_SC(ipid);
    
    select tsc.BIND as BindBy, v.*
    from T_SC tsc
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    order by v.g_order, v.sg_order, v.sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_by_products $$
CREATE PROCEDURE spec_classes_get_by_products(in ipids varchar(255))
BEGIN
	call sys_str2int_iT1(ipids);
	call spec_classes_get_by_products_iT1_T_SC();
    
    select tsc.BIND as BindBy, v.*
    from T_SC tsc
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    order by v.g_order, v.sg_order, v.sc_order;
END$$


/* SPEC REFBOOK */

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_refbook_item_get $$
CREATE PROCEDURE spec_refbook_item_get(in isrid bigint)
BEGIN
	select *
	from viewSPEC_REFBOOK
	where `srid`=isrid;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_refbook_get_by_class $$
CREATE PROCEDURE spec_refbook_get_by_class(in iscid bigint, IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select v.*
	    from viewSPEC_REFBOOK v
	    where v.scid=iscid
	    order by sr_order;
	else
		select v.*
	    from viewSPEC_REFBOOK v
	    where v.scid=iscid
	    order by sr_order
		limit loffset, lnum;
	end if;
END$$


DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_spec_refbook_items $$
CREATE FUNCTION acl_cancreate_spec_refbook_items (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_spec_refbook_items $$
CREATE FUNCTION acl_canmodify_spec_refbook_items (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_spec_refbook_items $$
CREATE FUNCTION acl_candelete_spec_refbook_items (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_refbook_item_update $$
CREATE PROCEDURE spec_refbook_item_update(in acl_uid bigint, in iid bigint, in iscid bigint, in icode varchar(8), in iname varchar(255))
BEGIN
	declare aff_id bigint;

	if ifnull(iid,0)=0 then
		if(acl_cancreate_spec_refbook_items(acl_uid)=1) then
			insert into SPEC_REFBOOK
			set
				SC_ID=iscid,
				SR_CODE=icode,
				SR_NAME=iname,
				SR_ORDER=(select ifnull(max(r.SR_ORDER),0)+1 from SPEC_REFBOOK r where r.SC_ID=iscid);

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_spec_refbook_items(acl_uid,iid)=1) then
			update SPEC_REFBOOK
			set 
				SC_ID=iscid,
				SR_CODE=icode,
				SR_NAME=iname
			where SR_ID=iid;

			set aff_id=iid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;

	select aff_id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_refbook_item_delete $$
CREATE PROCEDURE spec_refbook_item_delete(in uid bigint,in iid int) 
BEGIN
	if (acl_candelete_spec_refbook_items(uid,iid)=1) then
		update SPEC_REFBOOK
		set SR_VOID=1
		where SR_ID=iid;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_refbook_item_move $$
CREATE PROCEDURE spec_refbook_item_move(in acl_uid bigint, in dir char(4), in iid bigint)
BEGIN
	declare sc_id bigint;
	declare up bigint;
	declare down bigint;
	declare o_up bigint;
	declare o_down bigint;

	set up=null;
	set down=null;

	if (acl_canmodify_spec_refbook_items(acl_uid,iid)=1) then
		if (dir='UPUP') then
			select sr.SC_ID, sr.SR_ID, sr.SR_ORDER
			into sc_id, down, o_down
			from SPEC_REFBOOK sr where sr.SR_ID=iid;
	
			select sr.SR_ID, sr.SR_ORDER into up, o_up
			from SPEC_REFBOOK sr
			where sr.SC_ID=sc_id
			and sr.SR_ORDER<o_down
			order by sr.SR_ORDER desc limit 1;
		end if;
		if (dir='DOWN') then
			select sr.SC_ID, sr.SR_ID, sr.SR_ORDER
			into sc_id, up, o_up
			from SPEC_REFBOOK sr where sr.SR_ID=iid;
	
			select sr.SR_ID, sr.SR_ORDER
			into down, o_down
			from SPEC_REFBOOK sr
			where sr.SC_ID=sc_id
			and sr.SR_ORDER>o_up
			order by sr.SR_ORDER asc limit 1;
		end if;
		if (up is not null and down is not null) then
			update SPEC_REFBOOK set SR_ORDER=o_down where SR_ID=up;
			update SPEC_REFBOOK set SR_ORDER=o_up where SR_ID=down;
		end if;
	else
		signal sqlstate '02100' set message_text = '@acl_reorder_denied';
	end if;
END$$

/* SPEC VALUES */

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_values_get_by_product $$
CREATE PROCEDURE spec_classes_values_get_by_product(in ipid bigint)
BEGIN
	call spec_classes_get_by_product_T_SC(ipid);
    
    select tsc.BIND as BindBy, v.*, vv.*, p.P_ID as pid
    from T_SC tsc
    inner join PRODUCTS p on p.P_ID=ipid
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    left join viewPRODUCT_SPECS vv on vv.ClassId=tsc.SC_ID and vv.ProductId=p.P_ID
    order by v.g_order, v.sg_order, v.sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_values_get_by_product_ih $$
CREATE PROCEDURE spec_classes_values_get_by_product_ih(in ipid bigint)
BEGIN
	call spec_classes_get_by_product_T_SC(ipid);
    
    select tsc.BIND as BindBy, v.*, vv.*, p.P_ID as pid, ps1.P_ID is null as inherited
    from T_SC tsc
    inner join PRODUCTS p on p.P_ID=ipid
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    left join PRODUCT_SPECS ps1 on ps1.P_ID=p.P_ID and ps1.SC_ID=tsc.SC_ID
    left join PRODUCT_SPECS ps2 on ps2.P_ID=p.P_P0 and ps2.SC_ID=tsc.SC_ID
    left join viewPRODUCT_SPECS vv on vv.ClassId=tsc.SC_ID and (vv.ProductId=ps1.P_ID or ps1.P_ID is null and vv.ProductId=ps2.P_ID)
    order by v.g_order, v.sg_order, v.sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_values_get_by_product_defined $$
CREATE PROCEDURE spec_classes_values_get_by_product_defined(in ipid bigint)
BEGIN
	call spec_classes_get_by_product_T_SC(ipid);
    
    select tsc.BIND as BindBy, v.*, vv.*, p.P_ID as pid
    from T_SC tsc
    inner join PRODUCTS p on p.P_ID=ipid
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    inner join viewPRODUCT_SPECS vv on vv.ClassId=tsc.SC_ID and vv.ProductId=p.P_ID
    order by v.g_order, v.sg_order, v.sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_values_get_by_product_defined_ih $$
CREATE PROCEDURE spec_classes_values_get_by_product_defined_ih(in ipid bigint)
BEGIN
	call spec_classes_get_by_product_T_SC(ipid);
    
    select tsc.BIND as BindBy, v.*, vv.*, p.P_ID as pid, ps1.P_ID is null as inherited
    from T_SC tsc
    inner join PRODUCTS p on p.P_ID=ipid
    inner join viewSPEC_CLASSES v on v.scid=tsc.SC_ID
    left join PRODUCT_SPECS ps1 on ps1.P_ID=p.P_ID and ps1.SC_ID=tsc.SC_ID
    left join PRODUCT_SPECS ps2 on ps2.P_ID=p.P_P0 and ps2.SC_ID=tsc.SC_ID
    inner join viewPRODUCT_SPECS vv on vv.ClassId=tsc.SC_ID and (vv.ProductId=ps1.P_ID or ps1.P_ID is null and vv.ProductId=ps2.P_ID)
    order by v.g_order, v.sg_order, v.sc_order;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_value_set_by_product $$
CREATE PROCEDURE spec_classes_value_set_by_product(in ipid bigint,
	in ClassId bigint,
	
    in RefbookId bigint,
    
    in ValueString text,
    in ValueInteger bigint,
    in ValueFloat double,
    in ValueMoney decimal(17,4),
    in ValueBoolean tinyint(1),
    in ValueDatetime datetime
)
BEGIN
	declare vMulty tinyint;
    declare vDataType enum ('REFBOOK','REFBOOK+','STRING','INTEGER','FLOAT','MONEY','BOOLEAN','DATETIME');
    
    select SC_MULTY, SC_DTYPE
    into vMulty, vDataType
    from SPEC_CLASSES where SC_ID=ClassId;
    
    if not vMulty then
		delete from PRODUCT_SPECS
        where P_ID=ipid and SC_ID=ClassId;
    end if;
    
    if RefbookId is not null
		or ValueString is not null
        or ValueInteger is not null
        or ValueFloat is not null
        or ValueMoney is not null
        or ValueBoolean is not null
        or ValueDatetime is not null
	then
    
    insert into PRODUCT_SPECS
    set
		P_ID=ipid,
		SC_ID=ClassId,
		SR_ID=RefbookId,
		PS_VALUE_STRING=ValueString,
		PS_VALUE_INTEGER=ValueInteger,
		PS_VALUE_FLOAT=ValueFloat,
		PS_VALUE_MONEY=ValueMoney,
		PS_VALUE_BOOLEAN=ValueBoolean,
		PS_VALUE_DATETIME=ValueDatetime;
        
	end if;
    
END$$


/* BINDINGS */

DELIMITER $$
DROP PROCEDURE IF EXISTS product_spec_groups_clear $$
CREATE PROCEDURE product_spec_groups_clear (IN pid BIGINT)
BEGIN
	delete from PRODUCT_SPEC_GROUPS
	where P_ID=pid;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS product_spec_groups_bind $$
CREATE PROCEDURE product_spec_groups_bind (IN pid BIGINT, IN groups TEXT, IN kind CHAR(4))
BEGIN
	declare i int;
	declare gid bigint;

	set i=1;
	repeat
		set gid=str_part_int(groups,',',i,null);
		if gid is not null then
			insert into PRODUCT_SPEC_GROUPS
			set SG_ID=gid, P_ID=pid, PSG_KIND=kind;
		end if;
		set i=i+1;
	until gid is null
	end repeat;
END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS node_spec_groups_clear $$
CREATE PROCEDURE node_spec_groups_clear (IN nid BIGINT)
BEGIN
	delete from NODE_SPEC_GROUPS
	where N_ID=nid;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS node_spec_groups_bind $$
CREATE PROCEDURE node_spec_groups_bind (IN nid BIGINT, IN groups TEXT, IN kind CHAR(4))
BEGIN
	declare i int;
	declare gid bigint;

	set i=1;
	repeat
		set gid=str_part_int(groups,',',i,null);
		if gid is not null then
			insert into NODE_SPEC_GROUPS
			set SG_ID=gid, N_ID=nid, NSG_KIND=kind;
		end if;
		set i=i+1;
	until gid is null
	end repeat;
END $$



/*%%% PAGES & ARTICLES */


DELIMITER $$
drop function if exists acl_canmodify_article $$
create function acl_canmodify_article (uid INT, id INT)
	returns tinyint
begin
	return 1;
end $$
DELIMITER $$
drop function if exists acl_cancreate_article $$
create function acl_cancreate_article (uid INT)
	returns tinyint
begin
	return 1;
end $$
DELIMITER $$
drop function if exists acl_candelete_article $$
create function acl_candelete_article (uid INT, id INT)
	returns tinyint
begin
	return 1;
end $$
DELIMITER $$
drop procedure if exists update_article$$
/*update_article(:acl_uid,:id,:kind,
				:adate,:fshow,:title,:keywords,:description,
				:css,:caption,:uri_name,:short,:full, 
				:href,:link)*/
create procedure update_article(in acl_uid bigint, in id int, in kind varchar(4),
								adate datetime,fshow tinyint,title text,keywords text,description text,
								css varchar(32),caption text,uri_name varchar(128),short text,full longtext, 
								href text ,link text
								)
begin
	declare aff_id bigint;

	if ifnull(id,0)=0 then
		if(acl_cancreate_article(acl_uid)=1) then
			insert into ARTICLES
			set 
				A_DATE=adate,
				A_KIND=kind,
				A_DT_PUB=NOW(),
				A_HIDDEN=not fshow ,		
				A_TITLE=title,
				A_KEYWORDS=keywords, 	
				A_DESCRIPTION=description	,
				A_CSS=css,
				A_CAPTION=caption, 		
				A_URI=uri_name, 	
				A_TXT_SHORT=short,		
				A_TXT_FULL=full, 		
				A_HREF=href, 		
				A_LINK=link ;	
			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_article(acl_uid,id)=1) then
			update ARTICLES 
			set 
				A_DATE=adate,
				A_DT_PUB=NOW(),
				A_HIDDEN=not fshow ,		
				A_TITLE=title,
				A_KEYWORDS=keywords, 	
				A_DESCRIPTION=description	,
				A_CSS=css,
				A_CAPTION=caption, 		
				A_URI=uri_name, 	
				A_TXT_SHORT=short,		
				A_TXT_FULL=full, 		
				A_HREF=href, 		
				A_LINK=link
			where A_ID=id and A_KIND=kind;
			set aff_id=id;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id, IFNULL(uri_name,aff_id) as uri_id;
end$$

DELIMITER $$
drop procedure if exists delete_article$$
create procedure delete_article(in acl_uid bigint, in id int)
begin
	if(acl_candelete_article(acl_uid,id)=1) then
		delete from ARTICLES
		where A_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
end$$

DELIMITER $$
drop function if exists acl_canmodify_page $$
create function acl_canmodify_page (uid INT, pid BIGINT)
	returns tinyint
begin
	return 1;
end $$

DELIMITER $$
drop function if exists acl_cancreate_page $$
create function acl_cancreate_page (uid INT)
	returns tinyint
begin
	return 1;
end $$

DELIMITER $$
drop function if exists acl_candelete_page $$
create function acl_candelete_page (UID INT, PID BIGINT)
	returns tinyint
begin
	return 1;
end $$

DELIMITER $$
drop procedure if exists update_page$$
/*
call update_page( 	:acl_uid , :pid ,
					:caption ,:uri_name,:title,:keywords,
					:description,:css,:par_id,:ptext,:mnu,:mref,:aref,
					:after
					)
*/
create procedure update_page(in acl_uid bigint, in pid int,
								caption text,uri_name varchar(128),title text,keywords text,
								description text,css varchar(32),par_id int,ptext longtext,mnu tinyint,mref tinyint,aref varchar(255),
								after int
								)
begin
	declare aff_id bigint;
	declare p_order int;
	if ifnull(pid,0)=0 then
		if(acl_cancreate_page(acl_uid)=1) then
			insert into PAGES
			set 
				PG_CAPTION=caption, 	
				PG_URI_NAME=uri_name, 	
				PG_TITLE=title, 		
				PG_KEYWORDS=keywords, 	
				PG_DESCRIPTION=description ,		
				PG_CSS=css, 			
				PG_P0=par_id, 		
				PG_MNU=mnu, 			
				PG_MREF=mref,
                PG_ALTREF=aref,
				PG_TEXT=ptext,
				PG_ORDER=(select ifnull(max(ps.PG_ORDER),0)+1 from PAGES ps where ifnull(ps.PG_P0,-1)=ifnull(par_id,-1));	
			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_page(acl_uid,pid)=1) then
			update PAGES 
			set 
				PG_CAPTION=caption, 	
				PG_URI_NAME=if(PG_RO,PG_URI_NAME,uri_name), 	
				PG_TITLE=title, 		
				PG_KEYWORDS=keywords, 	
				PG_DESCRIPTION=description ,		
				PG_CSS=css, 			
				PG_P0=if(PG_RO,PG_P0,par_id),
				PG_MNU=mnu, 			
				PG_MREF=if(PG_RO,PG_MREF,mref),
                PG_ALTREF=if(PG_RO,PG_ALTREF,aref),
				PG_TEXT=ptext
			where PG_ID=pid ;
			set aff_id=pid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	call move_page_after(aff_id,after);
	select aff_id;
end$$

DELIMITER $$
drop procedure if exists move_page_after$$
create procedure move_page_after( pgid int, pgafter int)
begin
	declare afo int;
	declare slo int;	
	declare afp0 int;
	declare slp0 int;

	
	select PG_ORDER,PG_P0 into slo,slp0 from PAGES where PG_ID=pgid;
	set afp0=slp0;
	if (pgafter=-1) then
		select PG_ORDER,PG_ID into afo,pgafter from PAGES where ifnull(PG_P0,0)=ifnull(slp0,0) order by PG_ORDER desc limit 0,1;
	else if (pgafter=0) then
		set afo=0;
		else
			select PG_ORDER,PG_P0 into afo,afp0 from PAGES where PG_ID=pgafter;
		end if;
	end if;

	if(ifnull(slp0,0)=ifnull(afp0,0) and pgafter is not null)
	then
		start transaction;
			-- select afo,afp0,slo,slp0;
			-- select PG_ID,PG_ORDER from PAGES order by PG_ORDER;
			update PAGES set PG_ORDER=PG_ORDER-1 where PG_ORDER>slo and PG_ORDER<=afo and ifnull(PG_P0,0)=ifnull(slp0,0) ;
			update PAGES set PG_ORDER=PG_ORDER+1 where PG_ORDER>afo and PG_ORDER<slo and ifnull(PG_P0,0)=ifnull(slp0,0) ;
			select PG_ORDER+1 into slo from PAGES where pgafter<>0 and PG_ID=pgafter and ifnull(PG_P0,0)=ifnull(slp0,0) ;
			update PAGES set PG_ORDER=if(pgafter=0,1,slo) where PG_ID=pgid;
			-- select PG_ID,PG_ORDER from PAGES order by PG_ORDER;
		commit;
	end if;
end$$

DELIMITER $$
drop procedure if exists delete_page$$
create procedure delete_page(in acl_uid bigint, in id int)
begin
	/*
	declare i tinyint;
	set i=0;
	select ifnull(count(*),0) into i from PAGES
	where PG_P0=id;

	if (i=0) then 
		signal sqlstate '02100' set message_text = '@page_has_children';
	end if;*/
	
	
	if(acl_candelete_page(acl_uid,id)=1) then
		delete from  pc
		using PAGES p
		left join PAGES pc on pc.PG_P0=p.PG_ID
		where p.PG_ID=id;

		delete from  p
		using PAGES p
		where p.PG_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
end$$
 

/*%%% APPLICATION */

DELIMITER $$
DROP PROCEDURE IF EXISTS get_sitemap_cnt $$
CREATE PROCEDURE get_sitemap_cnt ()
BEGIN
	select (
		(
			select count(*)
			from PRODUCTS p
			inner join NODE_TREE t on t.N_ID=p.N_ID
			where not t.N_HIDE and not p.P_HIDE
		)+(
			select count(*)
			from viewMENU
		)+(
			select count(*)
			from BRANDS
		)+(
			select count(*)
			from ARTICLES
			where not A_HIDDEN
		)
	) as CNT;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_sitemap $$
CREATE PROCEDURE get_sitemap ()
BEGIN
	select CONCAT('/catalog',t.N_URI,'/',p.P_URI) as uri
	from PRODUCTS p
	inner join NODE_TREE t on t.N_ID=p.N_ID
	where not t.N_HIDE and not p.P_HIDE

	union

	select uri
	from viewMENU

	union

	select CONCAT('/filter/brand-',B_ID)
	from BRANDS

	union

	select CONCAT(CASE a_kind WHEN 'ARTI' THEN '/articles/' WHEN 'NEWS' THEN '/news/' WHEN 'SPEC' THEN '/specials/' ELSE null END,uri_id)
	from viewARTICLESshort
	where fshow;
END $$

DELIMITER $$
drop procedure if exists get_statistics$$
create procedure get_statistics()
begin
	select 	(select ifnull(CNT_ALL_FULL_PRNT,0) from NODE_TREE where N_ID=0) as num_all,
			(select ifnull(CNT_VIS_FULL_PRNT,0) from NODE_TREE where N_ID=0) as num_show,
			(select count(*) from PRODUCTS where IS_NEW=1 and P_P0 is null) as num_new,
			(select count(*) from PRODUCTS where IS_RECOMEND=1  and P_P0 is null) as num_recomend,
			(select count(*) from PRODUCTS where IS_SPECIAL=1  and P_P0 is null) as num_special,
			(select count(*) from ARTICLES where A_KIND='NEWS') as news ,
			(select count(*) from ARTICLES where A_KIND='ARTI') as articles,
			(select count(*) from ARTICLES where A_KIND='SPEC') as specs,
			(select count(*) from ORDERS where CTS_ID=1) as ord_new,
			(select count(*) from ORDERS) as ord_all,
			ifnull((select sum(ORD_QTY*ORD_PRICE) from ORDERS o inner join ORDER_DETAILS od on od.OR_ID=o.OR_ID),0) as ord_summ,
			(select count(*) from SUBSCRIBERS) as subscribers;
end$$

DELIMITER $$
drop procedure if exists get_products_nums_vis_nsr$$
create procedure get_products_nums_vis_nsr()
begin
	select
		COUNT(p.P_ID) as num_show,
		SUM(IF(p.IS_NEW,1,0)) as num_new,
		SUM(IF(p.IS_RECOMEND,1,0)) as num_recomend,
		SUM(IF(p.IS_SPECIAL,1,0)) as num_special
	from PRODUCTS p
	inner join NODES n on n.N_ID=p.N_ID
	inner join CATEGORIES c on c.N_ID=n.N_ID
	inner join NODE_TREE t on t.N_ID=n.N_ID
	where not (t.N_HIDE or p.P_HIDE) and p.P_P0 is null;
end$$

/*%%% CART ORDERS*/
DELIMITER $$
DROP PROCEDURE  IF EXISTS cartdetail_update $$
/*
call cartdetail_update(:uid,:cdid ,
								:cid,:cdqty,:cdprice,:pid)
							
*/ 
create procedure cartdetail_update(uid bigint,cdid bigint,
								cid bigint,cdqty int,cdprice float, pid bigint
							)
begin
	declare aff_id bigint;
	if ifnull(cdid,0)=0 then 
		insert into CART_DETAILS(C_ID,CD_QTY,CD_PRICE,P_ID)
		select  cid ,cdqty , cdprice , pid;
		set aff_id=last_insert_id(); 
	else 
		update CART_DETAILS 
		set C_ID=cid,CD_QTY=cdqty,CD_PRICE=cdprice,P_ID=pid
		where CD_ID=cdid;
		if (ROW_COUNT()<>0) then set aff_id=cdid; end if;
	end if;
	select aff_id;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS cart_add $$
/*
call prod_addcart_byhashlog(:uid,:hlid,:hlhash,:pid,:qty,:price)
							
*/ 
create procedure cart_add(uid bigint,icid bigint,ipid bigint,iqty double)
begin
	declare cid bigint;
	declare pid bigint;
	declare price double;

	select p.P_ID, pp.pp_salebase
	into pid, price
	from PRODUCTS p
	inner join viewPRODUCTS_PRICE pp on pp.pp_pid=p.P_ID
	where P_ID=ipid and P_FORSALE;

	if (pid is not null and iqty is not null) then 
		if (ifnull(icid,0)<>0)then
			select C_ID into cid from CART where AC_ID=icid;
			if (ifnull(cid,0)=0) then 
				insert into CART (AC_ID)
				values (icid);
				set cid=last_insert_id();
			end if;
			
			if exists(select * from CART_DETAILS where C_ID=cid and P_ID=pid) then
				update CART_DETAILS 
				set CD_QTY=CD_QTY+iqty
				where C_ID=cid and P_ID=pid;
				/*If count=0 insert new detail */
			else
				insert into CART_DETAILS (C_ID,CD_QTY,CD_PRICE,P_ID)
				values (cid,iqty,price,pid);
			end if;
			select cid as `cid`;
		end if;
	end if;
end$$


DELIMITER $$
DROP PROCEDURE  IF EXISTS cart_del $$
/*
call prod_delcart_byhashlog(:uid,:hlid,:hlhash,:pid)
							
*/ 
create procedure cart_del(uid bigint,acid bigint,pid bigint)
begin
	declare cid bigint;
	
	if (ifnull(acid,0)<>0) then
		select C_ID into cid from CART where AC_ID=acid;
		
		if (ifnull(cid,0)<>0) then 
			/*delete detail*/	
			delete from  CART_DETAILS where C_ID=cid and P_ID=pid;
			select acid as `acid`,cid as `cid`;
		else 
			select "@cart_expected";
		end if;
	else
		select "@cust_expected";
	end if;
end$$
DELIMITER $$
DROP PROCEDURE IF EXISTS cart_upd $$
create procedure cart_upd(uid bigint,acid bigint,pid bigint,qty double)
begin
	declare cid bigint;
	declare price double;

	select pp.pp_salebase
	into price
	from PRODUCTS p
	inner join viewPRODUCTS_PRICE pp on pp.pp_pid=p.P_ID
	where P_ID=pid and P_FORSALE;

	if (qty is not null) then
		if (ifnull(acid,0)<>0) then
			select C_ID into cid from CART where AC_ID=acid;
			if (ifnull(cid,0)=0) then 
				insert into CART (AC_ID)
				values (acid);
				set cid=last_insert_id();
			end if;	
			
			
			if exists(select * from CART_DETAILS where C_ID=cid and P_ID=pid) then
				update CART_DETAILS 
				set CD_QTY=qty,CD_PRICE=price
				where C_ID=cid and P_ID=pid;
			else
				insert into CART_DETAILS (C_ID,CD_QTY,CD_PRICE,P_ID)
				values (cid,qty,price,pid);
			end if;
			select acid as `acid`,cid as `cid`;
		else
			select "@cust_expected";
		end if;
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS ensure_shipment$$
create procedure ensure_shipment(sh_id bigint)
begin
	declare id bigint default -1;
	select CTH_ID into id from CT_SHIPPING where sh_id =CTH_ID;
	if ifnull(id,-1)=-1 and sh_id is not null
	then signal sqlstate '02100' set message_text = '@notfound_shipment'; end if;
end$$
DROP PROCEDURE  IF EXISTS ensure_status$$
create procedure ensure_status(st_id bigint)
begin
	declare id bigint default -1;
	select CTS_ID into id from CT_STATUS where st_id =CTS_ID;
	if ifnull(id,-1)=-1 and st_id is not null
	then signal sqlstate '02100' set message_text = '@notfound_ordstatus'; end if;
end$$
DROP PROCEDURE  IF EXISTS ensure_payment$$
create procedure ensure_payment(p_id bigint)
begin
	declare id bigint default -1;
	select CTP_ID into id from CT_PAYMENT where p_id =CTP_ID;
	if ifnull(id,-1)=-1 and p_id is not null
	then signal sqlstate '02100' set message_text = '@notfound_payment'; end if;
end$$
DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_order $$
CREATE FUNCTION acl_canmodify_order (uid INT, oid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$
DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_order $$
CREATE FUNCTION acl_cancreate_order (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$
DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_order $$
CREATE FUNCTION acl_candelete_order (uid INT, oid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$
DELIMITER $$
DROP FUNCTION IF EXISTS acl_canview_order $$
CREATE FUNCTION acl_canview_order (uid INT, oid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$
DELIMITER $$
DROP PROCEDURE  IF EXISTS update_order$$
/*
call update_order(uid,0,
						c_name,c_email,c_telephone,c_country,c_city,
						c_region ,c_address,shipping_id,payment_id,status_id,
						message,discount )

							
*/ 
create procedure update_order(acl_uid bigint, orid bigint,
									c_name varchar(255),c_email varchar(128),c_telephone varchar(64),c_country text,c_city text, -- 5
									c_region text,c_address text,shipping_id bigint,payment_id bigint,status_id bigint, -- 10
									message text,discount text       -- 15

							)
begin
	declare aff_id bigint;
	call ensure_shipment(shipping_id);
	call ensure_payment(payment_id);
	call ensure_status(status_id);
	
	if(acl_canmodify_order(acl_uid,orid)=1) then
		if ifnull(orid,0)=0 then 
			if(acl_cancreate_order(acl_uid)=1) then
				insert into ORDERS 
				set 	AC_ID=acl_uid,
						OR_NAME=c_name,OR_EMAIL=c_email,OR_PHONE=c_telephone,OR_COUNTRY=c_country,OR_CITY=c_city,OR_REGION=c_region,OR_ADDRESS=c_address,
						OR_MEMO=message,OR_COUPON=discount,
						CTH_ID=shipping_id,CTS_ID=ifnull(status_id,0),CTP_ID=payment_id,
						C_DATE=NOW();
				set aff_id=last_insert_id();
			end if;
		else 
			update ORDERS 
			set 	OR_NAME=c_name,OR_EMAIL=c_email,OR_PHONE=c_telephone,OR_COUNTRY=c_country,OR_CITY=c_city,OR_REGION=c_region,OR_ADDRESS=c_address,
					OR_MEMO=message,OR_COUPON=discount,
					CTH_ID=shipping_id,CTS_ID=ifnull(status_id,0),CTP_ID=payment_id
			where OR_ID=orid;
			if (ROW_COUNT()<>0) then set aff_id=orid; 
			else signal sqlstate '02100' set message_text = '@notfound_order';
			end if;
		end if;
	end if;
	select aff_id;
end$$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_order $$
CREATE PROCEDURE delete_order (IN acl_uid INT, IN id INT)
BEGIN
	if (acl_candelete_order(acl_uid,id)=1) then
		delete from ORDER_DETAILS where OR_ID=id;
		delete from ORDERS where OR_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_cart$$
create procedure get_cart(uid bigint)
begin
	select cd.*
	from viewCART_DETAILS  cd
	inner join CART c on c.C_ID=cd.cid and uid=c.AC_ID; /*%%%%%*/

	select distinct p.*
	from CART c 
	inner join CART_DETAILS cd on cd.C_ID=c.C_ID
	inner join viewPRODUCTS p on p.pid=cd.P_ID
	where uid=c.AC_ID;

	call get_cart_sum(uid);
end$$
delimiter $$
drop procedure if exists get_cart_sum$$
create procedure get_cart_sum(uid bigint)
begin
	select sum(cd.qty) as qty, count(cd.pid) as dqty, ifnull(sum(cd.price*cd.qty),0) as summ
	from viewCART_DETAILS  cd
	inner join CART c on c.C_ID=cd.cid and uid=c.AC_ID;
end $$
DELIMITER $$
DROP PROCEDURE  IF EXISTS order_make$$
create procedure order_make(uid bigint, or_id bigint,
						c_name varchar(255),c_email varchar(128),c_telephone varchar(64),c_country text,c_city text, -- 5
						c_region text,c_address text,shipping_id bigint,payment_id bigint,status_id bigint, -- 10
						message text,discount text)
begin
	declare aff_id bigint;
	declare cid bigint default null;

	call ensure_shipment(shipping_id);
	call ensure_payment(payment_id);
	call ensure_status(status_id); 
	if(acl_cancreate_order(uid)=1) then
		select C_ID into cid from CART c where c.AC_ID=uid;
		if cid is not null then
			insert into ORDERS 
			set 	AC_ID=uid,
					OR_NAME=c_name,OR_EMAIL=c_email,OR_PHONE=c_telephone,OR_COUNTRY=c_country,OR_CITY=c_city,OR_REGION=c_region,OR_ADDRESS=c_address,
					OR_MEMO=message,OR_COUPON=discount,
					CTH_ID=shipping_id,CTS_ID=ifnull(status_id,0),CTP_ID=payment_id,
					OR_DATE=NOW();

			set aff_id=last_insert_id();
			
			insert into ORDER_DETAILS(OR_ID,P_ID,B_ID,ORD_QTY,ORD_PRICE,P_NAME,P_VARIANT,P_BARCODE,P_CODE,B_NAME) 
			select aff_id,cd.P_ID,p.B_ID,cd.CD_QTY, cd.CD_PRICE,p.P_NAME,p.P_VARIANT,p.P_BARCODE,p.P_CODE,b.B_NAME
			from CART_DETAILS cd
			left join PRODUCTS p on p.P_ID=cd.P_ID
			left join BRANDS b on b.B_ID=p.B_ID
			where cd.C_ID=cid;
		
			delete from CART_DETAILS where C_ID=cid;

			select aff_id;
		else signal sqlstate '02100' set message_text = '@notfound_cart';
		end if;
	else signal sqlstate '02100' set message_text = '@acl_create_denied';
	end if;
end$$


DELIMITER $$
DROP PROCEDURE  IF EXISTS get_orders$$
create procedure get_orders(acl_uid bigint,fstatus bigint,fphone varchar(50),femail varchar(128),
							fdtfrom datetime,fdtto datetime,
							loffset int,lnum int,sort char(4))
begin
	
	if sort in ('NUMA','DTTA','SUMA') then
		select o.* 
		from viewORDERS o
		where
			ifnull(fstatus,o.status_id)=o.status_id
			and ((fphone is null and femail is null) or (ifnull(o.c_telephone=fphone,0) or ifnull(femail=o.c_email,0)))
			and (o.created>=fdtfrom or fdtfrom is null)
			and (o.created<DATE_ADD(fdtto , INTERVAL 1 DAY) or fdtto is null)
		order by 
			case sort 
				when 'NUMA' then lpad(floor(o.num), 20, '0') 
				when 'DTTA' then cast(o.created as char)
				when 'SUMA' then concat(lpad(floor(o.summ), 12, '0'),floor(mod(o.summ*10000,10000))) end 
			asc
		limit loffset,lnum
		;
	else
		
		select o.c_id,o.* 
		from viewORDERS o
		where
			ifnull(fstatus,o.status_id)=o.status_id
			and ((fphone is null and femail is null) or (ifnull(o.c_telephone=fphone,0) or ifnull(femail=o.c_email,0)))
			and (o.created>=fdtfrom or fdtfrom is null)
			and (o.created<DATE_ADD(fdtto , INTERVAL 1 DAY) or fdtto is null)
		order by 
			case sort 
				when 'NUMD' then lpad(floor(o.num), 20, '0')
				when 'DTTD' then cast(o.created as char)
				when 'SUMD' then concat(lpad(floor(o.summ), 12, '0'),floor(mod(o.summ*10000,10000))) end 
			desc
		limit loffset,lnum
		;
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_customers_by_phone$$
create procedure get_customers_by_phone(loffset int,lnum int)
begin
	if loffset is null or lnum is null then
		select
			o.OR_PHONE as telephone,
			count(o.OR_ID) as ord_count,
			sum(ORD_QTY*ORD_PRICE) as ord_summ
		from ORDERS o
		left join ORDER_DETAILS od on od.OR_ID=o.OR_ID
		group by o.OR_PHONE
		order by ord_summ desc;
	else
		select
			o.OR_PHONE as telephone,
			count(o.OR_ID) as ord_count,
			sum(ORD_QTY*ORD_PRICE) as ord_summ
		from ORDERS o
		left join ORDER_DETAILS od on od.OR_ID=o.OR_ID
		group by o.OR_PHONE
		order by ord_summ desc
		limit loffset, lnum;
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_order_by_id$$
create procedure get_order_by_id(acl_uid bigint,id bigint)
begin
	if acl_canview_order(acl_uid,id)=1 then
		select * 
		from viewORDERS where num = id;
	end if ;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_order_item_by_orderid$$
create procedure get_order_item_by_orderid(acl_uid bigint,id bigint)
begin
	if acl_canview_order(acl_uid,id)=1 then
		select * 
		from viewORDER_DETAILS where or_id=id;
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_order_item_by_id$$
create procedure get_order_item_by_id(acl_uid bigint,id bigint)
begin
	declare oid bigint;
	select OR_ID into oid from ORDER_DETAILS where ORD_ID=id;

	if acl_canview_order(acl_uid,oid)=1 then
		select * 
		from viewORDER_DETAILS where ord_id=id;
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS update_order_status$$
create procedure update_order_status(acl_uid bigint,id bigint,stid bigint)
begin
	if acl_canmodify_order(acl_uid,id)=1 then
		update ORDERS set OR_STATUS=stid where OR_ID=id;
	else 
		signal sqlstate '02100' set message_text = '@acl_modify_denied';	
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS update_order_item$$
create procedure update_order_item(acl_uid bigint, id bigint, qty int, price double)
begin
	declare oid bigint;
	select OR_ID into oid from ORDER_DETAILS where ORD_ID=id;
	if acl_canmodify_order(acl_uid,oid)=1 then
		update ORDER_DETAILS set ORD_QTY=qty,ORD_PRICE=price where ORD_ID=id;
	else 
		signal sqlstate '02100' set message_text = '@acl_modify_denied';	
	end if;
end$$

DELIMITER $$
DROP PROCEDURE  IF EXISTS add_order_item$$
create procedure add_order_item(acl_uid bigint, oid bigint, pid bigint, qty int)
begin
	if acl_canmodify_order(acl_uid,oid)=1 then
		/*if (exists(select * from ORDER_DETAILS where OR_ID=oid and P_ID=pid)) then
			update ORDER_DETAILS
			set ORD_QTY=ORD_QTY+1
			where OR_ID=oid and P_ID=pid
		else*/
			insert into ORDER_DETAILS (OR_ID,P_ID,B_ID,ORD_QTY,ORD_PRICE,P_NAME,P_VARIANT,P_BARCODE,P_CODE,B_NAME)
			select 
			oid as OR_ID,p.P_ID,p.B_ID,qty as ORD_QTY,p.P_PRICE as ORD_PRICE,p.P_NAME,p.P_VARIANT,p.P_BARCODE,p.P_CODE,b.B_NAME
			from PRODUCTS p
			left join BRANDS b on b.B_ID=p.P_ID
			where P_ID=pid;
		/*end if;*/
	else 
		signal sqlstate '02100' set message_text = '@acl_modify_denied';	
	end if;
end $$

DELIMITER $$
DROP PROCEDURE  IF EXISTS delete_order_item$$
create procedure delete_order_item(acl_uid bigint, id bigint)
begin
	declare oid bigint;
	select OR_ID into oid from ORDER_DETAILS where ORD_ID=id;
	if acl_canmodify_order(acl_uid,oid)=1 then
		delete from ORDER_DETAILS where ORD_ID=id;
	else 
		signal sqlstate '02100' set message_text = '@acl_modify_denied';
	end if;
end$$


delimiter $$
DROP FUNCTION IF EXISTS acl_canview_admuser$$
CREATE FUNCTION acl_canview_admuser(uid INT, id INT) RETURNS tinyint(4)
BEGIN
	declare pl varchar(32);
	set pl=null;
	select u.ROLE into pl from AUTH_ADM_USERS u where u.ID=uid;
	
	case pl
		when 'PL_ROOT' then return 1;
		when 'PL_ADMIN' then return (select (u.ROLE<>'PL_ROOT') from AUTH_ADM_USERS u where u.ID=id);
		else return 0;
	end case;
END$$

delimiter $$
DROP FUNCTION IF EXISTS acl_cancreate_admuser$$
CREATE FUNCTION acl_cancreate_admuser(uid INT, role varchar(32), banned TINYINT) RETURNS tinyint(4)
BEGIN
	declare pl varchar(32);
	set pl=null;
	select u.ROLE into pl from AUTH_ADM_USERS u where u.ID=uid;

	case pl
		when 'PL_ROOT' then return 1;
		when 'PL_ADMIN' then return (role<>'PL_ROOT');
		else return 0;
	end case;
END$$

delimiter $$
DROP FUNCTION IF EXISTS acl_candelete_admuser$$
CREATE FUNCTION acl_candelete_admuser(uid INT, id INT) RETURNS tinyint(4)
BEGIN
	declare pl varchar(32);
	set pl=null;
	
	if (uid=id) then
		return 0;
	else
		select u.ROLE into pl from AUTH_ADM_USERS u where u.ID=uid;

		case pl
			when 'PL_ROOT' then return 1;
			when 'PL_ADMIN' then return (role<>'PL_ROOT');
			else return 0;
		end case;
	end if;
END$$

delimiter $$
DROP FUNCTION IF EXISTS acl_canmodify_admuser$$
CREATE FUNCTION acl_canmodify_admuser(uid INT, id INT, role varchar(32), banned TINYINT) RETURNS tinyint(4)
BEGIN
	declare ret tinyint;

	declare pl varchar(32);
	declare plm varchar(32);

	set pl=null;
	select u.ROLE into pl from AUTH_ADM_USERS u where u.ID=uid;

	set plm=null;
	select u.ROLE into plm from AUTH_ADM_USERS u where u.ID=id;

	if (uid=id and banned) then
		set ret=0;
	else
		case pl
			when 'PL_ROOT' then set ret=1;
			when 'PL_ADMIN' then set ret=(plm<>'PL_ROOT' and role<>'PL_ROOT');
			else set ret=0;
		end case;
	end if;

	return ret;
END$$

delimiter $$
DROP PROCEDURE IF EXISTS get_admusers$$
CREATE PROCEDURE get_admusers(IN acl_uid INT)
BEGIN
	select
		t.ID as uid,
		t.BANNED as banned,
		t.EMAIL as login,
		t.NAME as name,
		null as pword,
		t.ROLE as role
	from AUTH_ADM_USERS t
	where acl_canview_admuser(acl_uid,t.ID);
END$$

delimiter $$
DROP PROCEDURE IF EXISTS get_admuser_by_id$$
CREATE PROCEDURE get_admuser_by_id(IN acl_uid INT, IN id INT)
BEGIN
	select
		t.ID as uid,
		t.BANNED as banned,
		t.EMAIL as login,
		t.NAME as name,
		null as pword,
		t.ROLE as role
	from AUTH_ADM_USERS t
	where t.ID=id and acl_canview_admuser(acl_uid,t.ID);
END$$

delimiter $$
DROP PROCEDURE IF EXISTS update_admuser$$
CREATE PROCEDURE update_admuser(IN acl_uid INT, IN uid INT, IN banned TINYINT,
	IN login varchar(128), IN name text, IN pword varchar(32), IN role varchar(32))
BEGIN
	declare aff_id bigint;

	if ifnull(uid,0)=0 then
		if(acl_cancreate_admuser(acl_uid,role,banned)=1) then
			insert into AUTH_ADM_USERS
			set 
				BANNED=banned,
				EMAIL=login,
				NAME=name,
				PHASH=MD5(pword),
				ROLE=role;	
			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_admuser(acl_uid,uid,role,banned)=1) then
			update AUTH_ADM_USERS t 
			set 
				t.BANNED=banned,
				t.EMAIL=login,
				t.NAME=name,
				t.PHASH=IF(pword is null,t.PHASH,MD5(pword)),
				t.ROLE=role
			where t.ID=uid ;
			set aff_id=uid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id;
END$$

delimiter $$
DROP PROCEDURE IF EXISTS delete_admuser$$
CREATE PROCEDURE delete_admuser(IN acl_uid INT, IN id INT)
BEGIN
	if(acl_candelete_admuser(acl_uid,id)=1) then
		delete from t using AUTH_ADM_USERS t
		where t.ID=id and acl_candelete_admuser(acl_uid,t.ID);
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$








DELIMITER $$
DROP PROCEDURE IF EXISTS get_currencies $$
CREATE PROCEDURE get_currencies ()
BEGIN
	select * from viewCURRENCIES;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_disp_currencies $$
CREATE PROCEDURE get_disp_currencies ()
BEGIN
	select * from viewDCURRENCIES order by num;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_price_currencies $$
CREATE PROCEDURE get_price_currencies (IN price DOUBLE)
BEGIN
	select *,
		price*ratio as `value`
	from viewDCURRENCIES order by num;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_dcurrency $$
CREATE FUNCTION acl_canmodify_dcurrency (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_dcurrency $$
CREATE PROCEDURE update_dcurrency (IN acl_uid INT, IN num INT, IN `show` TINYINT,
			IN curr CHAR(3), IN ratio DOUBLE, IN ratio_r TINYINT)
BEGIN
	if (acl_canmodify_dcurrency(acl_uid)=1) then
		replace into CURRENCIES_DISP set
			DCR_NUM=num,
			DCR_SHOW=`show`,
			CR_CODE=curr,
			DCR_RATIO=IF(ratio_r,1/ratio,ratio);
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_dcurrency $$
CREATE PROCEDURE delete_dcurrency (IN acl_uid INT, IN num INT)
BEGIN
	if (acl_canmodify_dcurrency(acl_uid)=1) then
		delete from CURRENCIES_DISP where DCR_NUM=num;
	else
		signal sqlstate '02100' set message_text = '@acl_update_denied';
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_dcurrencies $$
CREATE PROCEDURE get_dcurrencies ()
BEGIN
	declare curr_base CHAR(3);
	declare curr_base_show TINYINT;
	declare curr_1 CHAR(3);
	declare curr_1_ratio DOUBLE;
	declare curr_1_ratio_r TINYINT;
	declare curr_2 CHAR(3);
	declare curr_2_ratio DOUBLE;
	declare curr_2_ratio_r TINYINT;

	select CR_CODE, DCR_SHOW into curr_base, curr_base_show from CURRENCIES_DISP where DCR_NUM=0 and DCR_RATIO=1;
	select CR_CODE, IF(DCR_RATIO<1,1/DCR_RATIO,DCR_RATIO), (DCR_RATIO<1) into curr_1, curr_1_ratio, curr_1_ratio_r from CURRENCIES_DISP where DCR_NUM=1;
	select CR_CODE, IF(DCR_RATIO<1,1/DCR_RATIO,DCR_RATIO), (DCR_RATIO<1) into curr_2, curr_2_ratio, curr_2_ratio_r from CURRENCIES_DISP where DCR_NUM=2;

	select curr_base,curr_base_show,curr_1,curr_1_ratio,curr_1_ratio_r,curr_2,curr_2_ratio,curr_2_ratio_r;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS update_dcurrencies $$
CREATE PROCEDURE update_dcurrencies (IN acl_uid INT, IN curr_base CHAR(3), IN curr_base_show TINYINT,
			IN curr_1 CHAR(3), IN curr_1_ratio DOUBLE, IN curr_1_ratio_r TINYINT,
			IN curr_2 CHAR(3), IN curr_2_ratio DOUBLE, IN curr_2_ratio_r TINYINT)
BEGIN
	call update_dcurrency(acl_uid,0,curr_base_show,curr_base,1,0);
	if (ifnull(curr_1,'000')='000') then
		call delete_dcurrency(acl_uid,1);
	else
		call update_dcurrency(acl_uid,1,1,curr_1,curr_1_ratio,curr_1_ratio_r);
	end if;
	if (ifnull(curr_2,'000')='000') then
		call delete_dcurrency(acl_uid,2);
	else
		call update_dcurrency(acl_uid,2,1,curr_2,curr_2_ratio,curr_2_ratio_r);
	end if;
END $$



DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_subscribe $$
CREATE FUNCTION acl_candelete_subscribe (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS add_subscribe $$
CREATE PROCEDURE add_subscribe (IN email VARCHAR(255),IN ip VARCHAR(16))
BEGIN
	declare unsuc VARCHAR(32);

	set unsuc=gen_code(32);

	replace into SUBSCRIBERS set
		SC_EMAIL=email,
		SC_DATETIME=NOW(),
		SC_IP=ip,
		SC_UNSUBSCRIBE=unsuc;

	select unsuc;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS un_subscribe $$
CREATE PROCEDURE un_subscribe (IN email VARCHAR(255),IN unsuc VARCHAR(32))
BEGIN
	delete from SUBSCRIBERS where SC_EMAIL=email and SC_UNSUBSCRIBE=unsuc;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS delete_subscribe $$
CREATE PROCEDURE delete_subscribe (IN acl_uid INT, IN id BIGINT)
BEGIN
	if (acl_candelete_subscribe(acl_uid)=1) then
		delete from SUBSCRIBERS where SC_ID=id;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_subscriber_by_id $$
CREATE PROCEDURE get_subscriber_by_id (IN id BIGINT)
BEGIN
	select v.*
	from viewSUBSCRIBERS v
	where v.id=id;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_subscribers $$
CREATE PROCEDURE get_subscribers (IN loffset INT, IN lnum INT)
BEGIN
	if loffset is null or lnum is null then
		select v.*
		from viewSUBSCRIBERS v
		order by v.subscribed desc;
	else
		select v.*
		from viewSUBSCRIBERS v
		order by v.subscribed desc
		limit loffset, lnum;
	end if;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_subscribers_4broadcast $$
CREATE PROCEDURE get_subscribers_4broadcast (IN lim INT)
BEGIN
	drop temporary table if exists TT_CSC;
	create temporary table TT_CSC (SC_ID bigint);

	insert into TT_CSC
	select SC_ID
	from SUBSCRIBERS
	-- where (TO_DAYS(SC_BROADCASTED)<TO_DAYS(NOW())) OR SC_BROADCASTED IS NULL
	order by SC_BROADCASTED
	limit lim;

	select v.*
	from viewSUBSCRIBERS v
	inner join TT_CSC tt on tt.SC_ID=v.id;

	update SUBSCRIBERS sc
	inner join TT_CSC tt on tt.SC_ID=sc.SC_ID
	set sc.SC_BROADCASTED=NOW();

	drop temporary table if exists TT_CSC;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS get_articles_4broadcast $$
CREATE PROCEDURE get_articles_4broadcast (IN kind CHAR(4), IN lim INT)
BEGIN
	select *
	from viewARTICLES
	where a_kind=kind and fshow and TO_DAYS(adate)=TO_DAYS(NOW())
	order by adate desc, a_order desc
	limit lim;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS tagcloud_get$$
CREATE PROCEDURE tagcloud_get()
BEGIN
	select t.T_NAME as tag, COUNT(pt.P_ID) as count
	from TAGS t
	left join PRODUCT_TAGS pt on pt.T_ID=t.T_ID
	group by t.T_ID;
END$$





DELIMITER $$
DROP FUNCTION IF EXISTS gen_code$$
CREATE FUNCTION gen_code(len INT) RETURNS VARCHAR(255)
BEGIN
	declare alphabet char(16) default '0123456789abcdef';
	declare alen int default 16;
	declare str varchar(255) default '';

	while len > 0 do
		set str = concat(str, substring(alphabet,ceiling(rand() * alen),1));
		set len = len-1;
	end while;

	return str;
END$$




DELIMITER $$
DROP PROCEDURE IF EXISTS spec_group_get $$
CREATE PROCEDURE spec_group_get(in isgid bigint)
BEGIN
	select *
    from viewSPEC_GROUPS
    where sgid=isgid;
END$$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_spec_group $$
CREATE FUNCTION acl_cancreate_spec_group (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_spec_group $$
CREATE FUNCTION acl_canmodify_spec_group (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_spec_group $$
CREATE FUNCTION acl_candelete_spec_group (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_group_update $$
CREATE PROCEDURE spec_group_update(in acl_uid bigint, in iid bigint, in icode varchar(8), in iname varchar(255))
BEGIN
	declare aff_id bigint;

	if ifnull(iid,0)=0 then
		if(acl_cancreate_spec_group(acl_uid)=1) then
			insert into SPEC_GROUPS
			set
				SG_CODE=icode,
				SG_NAME=iname,
				SG_ORDER=(select ifnull(max(sg.SG_ORDER),0)+1 from SPEC_GROUPS sg);

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_spec_group(acl_uid,iid)=1) then
			update SPEC_GROUPS
			set 
				SG_CODE=icode,
				SG_NAME=iname
			where SG_ID=iid;

			set aff_id=iid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;

	select aff_id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_group_delete $$
CREATE PROCEDURE spec_group_delete(in uid bigint,in iid int) 
BEGIN
	if (acl_candelete_spec_group(uid,iid)=1) then
		select 1;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_group_move $$
CREATE PROCEDURE spec_group_move(in acl_uid bigint, in dir char(4), in iid bigint)
BEGIN
	declare up bigint;
	declare down bigint;
	declare o_up bigint;
	declare o_down bigint;

	set up=null;
	set down=null;

	if (acl_canmodify_spec_group(acl_uid,iid)=1) then
		if (dir='UPUP') then
			select sg.SG_ID, sg.SG_ORDER
			into down, o_down
			from SPEC_GROUPS sg where sg.SG_ID=iid;
	
			select sg.SG_ID, sg.SG_ORDER
			into up, o_up
			from SPEC_GROUPS sg
			where sg.SG_ORDER<o_down
			order by sg.SG_ORDER desc limit 1;
		end if;
		if (dir='DOWN') then
			select sg.SG_ID, sg.SG_ORDER
			into up, o_up
			from SPEC_GROUPS sg where sg.SG_ID=iid;
	
			select sg.SG_ID, sg.SG_ORDER
			into down, o_down
			from SPEC_GROUPS sg
			where sg.SG_ORDER>o_up
			order by sg.SG_ORDER asc limit 1;
		end if;
		if (up is not null and down is not null) then
			update SPEC_GROUPS set SG_ORDER=o_down where SG_ID=up;
			update SPEC_GROUPS set SG_ORDER=o_up where SG_ID=down;
		end if;
	else
		signal sqlstate '02100' set message_text = '@acl_reorder_denied';
	end if;
END$$




DELIMITER $$
DROP FUNCTION IF EXISTS acl_cancreate_spec_class $$
CREATE FUNCTION acl_cancreate_spec_class (uid INT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_canmodify_spec_class $$
CREATE FUNCTION acl_canmodify_spec_class (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP FUNCTION IF EXISTS acl_candelete_spec_class $$
CREATE FUNCTION acl_candelete_spec_class (uid INT, pid BIGINT)
	RETURNS TINYINT
BEGIN
	RETURN 1;
END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_class_update $$
CREATE PROCEDURE spec_class_update(
	in acl_uid bigint, in iid bigint, in icode varchar(8), in iname varchar(255),
	in iis_multy tinyint, in idatatype varchar(32), in igroup_id bigint
)
BEGIN
	declare aff_id bigint;

	if ifnull(iid,0)=0 then
		if(acl_cancreate_spec_class(acl_uid)=1) then
			insert into SPEC_CLASSES
			set
				SG_ID=igroup_id,
				SC_CODE=icode,
				SC_NAME=iname,
				SC_MULTY=iis_multy,
				SC_DTYPE=idatatype,
				SC_ORDER=(select ifnull(max(sc.SC_ORDER),0)+1 from SPEC_CLASSES sc where sc.SG_ID=igroup_id),
				SC_ORDER_GLOBAL=(select ifnull(max(sc.SC_ORDER_GLOBAL),0)+1 from SPEC_CLASSES sc);

			set aff_id=last_insert_id();
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_spec_class(acl_uid,iid)=1) then
			update SPEC_CLASSES
			set 
				SG_ID=igroup_id,
				SC_CODE=icode,
				SC_NAME=iname,
				SC_MULTY=iis_multy,
				SC_DTYPE=idatatype
			where SC_ID=iid;

			set aff_id=iid;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;

	select aff_id;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_class_delete $$
CREATE PROCEDURE spec_class_delete(in uid bigint,in iid int) 
BEGIN
	if (acl_candelete_spec_class(uid,iid)=1) then
		select 1;
	else
		signal sqlstate '02100' set message_text = '@acl_delete_denied';
	end if;
END$$

DELIMITER $$
DROP PROCEDURE IF EXISTS spec_class_move $$
CREATE PROCEDURE spec_class_move(in acl_uid bigint, in dir char(4), in iid bigint)
BEGIN
	declare sg_id bigint;
	declare up bigint;
	declare down bigint;
	declare o_up bigint;
	declare o_down bigint;
	declare o1_up bigint;
	declare o1_down bigint;

	set up=null;
	set down=null;

	if (acl_canmodify_spec_class(acl_uid,iid)=1) then
		if (dir='UPUP') then
			select sc.SG_ID, sc.SC_ID, sc.SC_ORDER, sc.SC_ORDER_GLOBAL
			into sg_id, down, o_down, o1_down
			from SPEC_CLASSES sc where sc.SC_ID=iid;
	
			select sc.SC_ID, sc.SC_ORDER, sc.SC_ORDER_GLOBAL
			into up, o_up, o1_up
			from SPEC_CLASSES sc
			where sc.SG_ID=sg_id
			and sc.SC_ORDER<o_down
			order by sc.SC_ORDER desc limit 1;
		end if;
		if (dir='DOWN') then
			select sc.SG_ID, sc.SC_ID, sc.SC_ORDER, sc.SC_ORDER_GLOBAL
			into sg_id, up, o_up, o1_up
			from SPEC_CLASSES sc where sc.SC_ID=iid;
	
			select sc.SC_ID, sc.SC_ORDER, sc.SC_ORDER_GLOBAL
			into down, o_down, o1_down
			from SPEC_CLASSES sc
			where sc.SG_ID=sg_id
			and sc.SC_ORDER>o_up
			order by sc.SC_ORDER asc limit 1;
		end if;
		if (up is not null and down is not null) then
			update SPEC_CLASSES set SC_ORDER=o_down, SC_ORDER_GLOBAL=o1_down where SC_ID=up;
			update SPEC_CLASSES set SC_ORDER=o_up, SC_ORDER_GLOBAL=o1_up where SC_ID=down;
		end if;
	else
		signal sqlstate '02100' set message_text = '@acl_reorder_denied';
	end if;
END$$





DELIMITER $$
DROP PROCEDURE IF EXISTS spec_classes_get_by_group $$
CREATE PROCEDURE spec_classes_get_by_group(in isgid bigint)
BEGIN
	select *
	from viewSPEC_CLASSES v
	where v.sgid=isgid
	order by v.g_order, v.sg_order, v.sc_order;
END$$




delimiter $$
drop procedure if exists price_type_get $$
create procedure price_type_get(in iptid bigint)
begin

	select *
    from viewPRICE_TYPES
    where ptid=iptid;

end $$

delimiter $$
drop procedure if exists price_types_get $$
create procedure price_types_get()
begin

	select *
    from viewPRICE_TYPES
	order by `name`;

end $$

delimiter $$
drop procedure if exists price_type_update $$
/*
call price_type_update(
	:acl_uid, :ptid,
	:currency_code,
	:name, :mark, :rate, :incr
)
*/
create procedure price_type_update (
	in acl_uid bigint, in i_ptid int,
	in i_currency_code char(3),
	in i_name text, in i_mark double, in i_rate double,
	in i_incr double
)
begin
	
	declare aff_id bigint;
	
	if ifnull(i_ptid,0)=0 then

		insert into PRICE_TYPES
		set 
			CR_CODE=i_currency_code,
			PT_NAME=i_name,
			PT_MARK=1+i_mark/100,
			PT_RATE=i_rate,
			PT_INCR=i_incr;
		set aff_id=last_insert_id();

	else 
		
		update PRICE_TYPES 
		set 
			CR_CODE=i_currency_code,
			PT_NAME=i_name,
			PT_MARK=1+i_mark/100,
			PT_RATE=i_rate,
			PT_INCR=i_incr
		where PT_ID=i_ptid;
		set aff_id=i_ptid;

	end if;

	select aff_id;

end $$


delimiter $$
drop procedure if exists price_type_delete $$
create procedure price_type_delete (
	in acl_uid bigint, in `id` int
)
begin
	
	delete from PRICE_TYPES where PT_ID=`id`;

end $$



delimiter $$
drop procedure if exists cmb_currencies $$
create procedure cmb_currencies()
begin
	
	select CR_CODE as `key`, CR_NAME as `val`
	from CURRENCIES
	order by CR_NAME;

end $$



delimiter $$
drop procedure if exists cmb_price_types $$
create procedure cmb_price_types()
begin
	
	select PT_ID as `key`, PT_NAME as `val`
	from PRICE_TYPES
	order by PT_ID;

end $$






delimiter $$
drop procedure if exists product_rating_get $$
create procedure product_rating_get (uid bigint, pid bigint, ip varchar(15)) 
begin
	
	select pid, count(V_RATE) as `count`, avg(V_RATE) as average, floor(round(avg(V_RATE))) as iaverage, sum(if(V_IP=ip,1,0)) as ipvoted
	from PRODUCTS_VOTES
	where P_ID=pid;

end $$

delimiter $$
drop procedure if exists products_rating_delete $$
create procedure products_rating_delete (uid bigint, pid bigint) 
begin
	
	delete from PRODUCTS_VOTES where P_ID=pid;

end $$

delimiter $$
drop procedure if exists products_rating_get_vote $$
create procedure products_rating_get_vote(uid bigint, pid bigint, ip varchar(15))
begin
	
	select V_ID as vid, P_ID as pid, V_RATE as rate, V_DATE as `time`
	from PRODUCTS_VOTES
	where P_ID=pid and V_IP=ip;

end $$

delimiter $$
drop procedure if exists products_rating_add_vote $$
create procedure products_rating_add_vote(uid bigint, pid bigint, rate int, ip varchar(15))
begin
	
	insert into PRODUCTS_VOTES (P_ID, V_RATE, V_IP) values (pid, rate, ip);
	-- on duplicate key update V_RATE=rate, V_DATE=current_timestamp;

end $$


delimiter $$
drop procedure if exists get_picts_uris $$
create procedure get_picts_uris()
begin
	
	select distinct CAT_PICT_URI from CATEGORIES where CAT_PICT_URI is not null order by CAT_PICT_URI;

	select distinct P_PICT_URI from PRODUCTS where P_PICT_URI is not null order by P_PICT_URI;

end $$