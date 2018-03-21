replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-dbprog','2016.0001');

DELIMITER $$
DROP PROCEDURE  IF EXISTS get_orders$$
create procedure get_orders(acl_uid bigint,fstatus bigint,fphone varchar(50),femail varchar(128),
							fdtfrom datetime,fdtto datetime,
							loffset int,lnum int,sort char(4))
begin
	
	if sort in ('NUMA','DTTA','SUMA') then
		select SQL_CALC_FOUND_ROWS o.* 
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
		
		select SQL_CALC_FOUND_ROWS o.c_id,o.* 
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
	select FOUND_ROWS() as cnt;
end$$


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

	declare old_pict_uri varchar(128);
	declare new_pict_uri varchar(128);

	set old_pict_uri=null;
	set new_pict_uri=null;

	if ifnull(id,0)=0 then
		if(acl_cancreate_article(acl_uid)=1) then
			set new_pict_uri=uri_name;
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
				A_URI=new_pict_uri, 	
				A_TXT_SHORT=short,		
				A_TXT_FULL=full, 		
				A_HREF=href, 		
				A_LINK=link ;	
			set aff_id=last_insert_id();
			if new_pict_uri is null then set new_pict_uri=concat('icon-',aff_id); end if;
		else
			signal sqlstate '02100' set message_text = '@acl_create_denied';
		end if;
	else 
		if(acl_canmodify_article(acl_uid,`id`)=1) then
			select if(A_URI is null,concat('icon-',A_ID),A_URI) into old_pict_uri from ARTICLES where A_ID=`id`;
			set new_pict_uri=uri_name;
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
				A_URI=new_pict_uri, 	
				A_TXT_SHORT=short,		
				A_TXT_FULL=full, 		
				A_HREF=href, 		
				A_LINK=link
			where A_ID=`id` and A_KIND=kind;
			set aff_id=`id`;
				if new_pict_uri is null then set new_pict_uri=concat('icon-',aff_id); end if;
		else
			signal sqlstate '02100' set message_text = '@acl_update_denied';
		end if;
	end if;
	select aff_id, IFNULL(uri_name,aff_id) as uri_id, old_pict_uri, new_pict_uri;
end $$