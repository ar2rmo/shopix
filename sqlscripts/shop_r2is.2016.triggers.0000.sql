replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-triggs','2016.0000');

DELIMITER $$
DROP TRIGGER IF EXISTS UTRIG_PRODUCTS_BEFORE$$
create trigger UTRIG_PRODUCTS_BEFORE before update on PRODUCTS for each row
begin
	if (IFNULL(old.P_CODE,0)<>IFNULL(new.P_CODE,0)) then
		set new.P_SRCH_CODE=prep_code(new.P_CODE);
	end if;
end$$

DELIMITER $$
DROP TRIGGER IF EXISTS ITRIG_PRODUCTS_BEFORE$$
create trigger ITRIG_PRODUCTS_BEFORE before insert on PRODUCTS for each row
begin
	set new.P_SRCH_CODE=prep_code(new.P_CODE);
end$$

DELIMITER $$
DROP TRIGGER IF EXISTS ITRIG_PRODUCTS_AFTER$$
create trigger ITRIG_PRODUCTS_AFTER after insert on PRODUCTS for each row
begin
	if (new.P_DESCR_FULL<>'' or new.P_DESCR_SHORT<>'') then
		insert into TEXTS (ID, TX_TYPE, TX_TEXT)
		select new.P_ID,'PDSC',IF(new.P_DESCR_FULL='',new.P_DESCR_SHORT,new.P_DESCR_FULL);
	end if;

	insert into TEXTS (ID, TX_TYPE, TX_TEXT)
	select new.P_ID,'PNAM',new.P_NAME;
end$$

DELIMITER $$
DROP TRIGGER IF EXISTS DTRIG_PRODUCTS_AFTER$$
create trigger DTRIG_PRODUCTS_AFTER after delete on PRODUCTS for each row
begin
	-- (ID, TX_TYPE, TX_TEXT)
	delete from  TEXTS 
	where old.P_ID=ID and (TX_TYPE='PDSC' or TX_TYPE='PNAM');
end$$

DELIMITER $$ 
DROP TRIGGER IF EXISTS UTRIG_PRODUCTS_AFTER$$
create trigger UTRIG_PRODUCTS_AFTER after update on PRODUCTS for each row
begin
	-- (ID, TX_TYPE, TX_TEXT)
	if (old.P_NAME<>new.P_NAME or old.P_DESCR_SHORT<>new.P_DESCR_SHORT or old.P_DESCR_FULL<>new.P_DESCR_FULL) then
		delete from  TEXTS 
		where old.P_ID=ID and (TX_TYPE='PDSC' or TX_TYPE='PNAM');

		if (new.P_DESCR_FULL<>'' or new.P_DESCR_SHORT<>'') then
			insert into TEXTS (ID, TX_TYPE, TX_TEXT)
			select new.P_ID,'PDSC',IF(new.P_DESCR_FULL='',new.P_DESCR_SHORT,new.P_DESCR_FULL);
		end if;

		insert into TEXTS (ID, TX_TYPE, TX_TEXT)
		select new.P_ID,'PNAM',new.P_NAME;
	end if;
end$$

DELIMITER $$
DROP TRIGGER IF EXISTS UTRIG_SETTINGS_BEFORE$$
create trigger UTRIG_SETTINGS_BEFORE before update on SETTINGS for each row
begin
	set new.chk_state=0;
	if new.chk_hash=md5(concat(new.inf_host,'33t5')) then
		set new.chk_state=1;
	end if;
	if new.chk_hash=md5(concat(new.inf_host,'4e47')) then
		set new.chk_state=2;
	end if;
end$$