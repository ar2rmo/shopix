-- altering lib Birra
alter table NODES AUTO_INCREMENT = 1;

alter table NODE_TREE
add  `CNT_ALL_FULL_CHLD` int(11) DEFAULT NULL after CNT_VIS,
add  `CNT_ALL_CHLD` int(11) DEFAULT NULL after CNT_ALL_FULL_CHLD,
add  `CNT_VIS_FULL_CHLD` int(11) DEFAULT NULL after CNT_ALL_CHLD,
add  `CNT_VIS_CHLD` int(11) DEFAULT NULL after CNT_VIS_FULL_CHLD,
add  `CNT_ALL_FULL_PRNT` int(11) DEFAULT NULL after CNT_VIS_CHLD,
add  `CNT_ALL_PRNT` int(11) DEFAULT NULL after CNT_ALL_FULL_PRNT,
add  `CNT_VIS_FULL_PRNT` int(11) DEFAULT NULL after CNT_ALL_PRNT,
add  `CNT_VIS_PRNT` int(11) DEFAULT NULL after CNT_VIS_FULL_PRNT;

create table VERSIONS (
	V_MODULE varchar(16) not null,
    V_VERSION  varchar(16) not null,
    
    primary key (V_MODULE)
) ENGINE=InnoDB;

insert into VERSIONS (V_MODULE, V_VERSION)
values ('shop-r2is', '2015.0001');

create table if not exists AUTH_ADM_USERS (

	ID int not null auto_increment,
	BANNED tinyint not null,
	EMAIL varchar(128) collate utf8_general_ci not null,
	NAME text not null,
	PHASH varchar(32) collate utf8_bin not null,
	ROLE varchar(32) collate utf8_bin null default null,
	
	primary key (`ID`),

	unique key UQ_EMAIL (EMAIL)

) engine=InnoDB;

create table if not exists AUTH_ADM_COOKIES (

	ID int not null auto_increment,
	UID int not null,
	CREATED datetime not null,
	FROMIP varchar(15) collate utf8_bin not null,
	USED int not null default 0,
	LASTUSED datetime null default NULL,
	EXPIRATION datetime not null,
	CHASH varchar(32) collate utf8_bin not null,

	primary key (`ID`),

	constraint FK_CK_UID foreign key (UID) references AUTH_ADM_USERS (ID)

) engine=InnoDB;

# alter table AUTH_ADM_COOKIES add EXPIRATION datetime not null after LASTUSED;

create table if not exists AUTH_ADM_PRECOVER (

	ID int not null auto_increment,
	UID int not null,
	TOKEN char(32) collate utf8_bin not null,
	EXPIRATION datetime not null,

	primary key (`ID`),

	index I_TOKEN (TOKEN),
	index I_EXPIRATION (EXPIRATION),

	constraint FK_PR_UID foreign key (UID) references AUTH_ADM_USERS (ID)

) engine=InnoDB;

CREATE TABLE IF NOT EXISTS `AUTH_CUST_COOKIES` (
  `ID` BIGINT NOT NULL AUTO_INCREMENT,
  `CREATED` datetime NOT NULL,
  `FROMIP` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `USED` INT NOT NULL DEFAULT 0,
  `LASTUSED` datetime DEFAULT NULL,
  `EXPIRATION` datetime not null,
  `CHASH` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB;

CREATE TABLE if not exists `CURRENCIES` (

	`CR_CODE`              CHAR(3)               NOT NULL,
	`CR_NAME`              VARCHAR(128)          NOT NULL,
	`CR_SNAME`             VARCHAR(32)           NOT NULL,
	`CR_FORMAT`            VARCHAR(64)           NOT NULL,

	PRIMARY KEY          (`CR_CODE`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `CURRENCIES_DISP` (

	`DCR_NUM`              DOUBLE                NOT NULL,
	`DCR_SHOW`             TINYINT               NOT NULL,
	`CR_CODE`              CHAR(3)               NOT NULL,
	`DCR_RATIO`            DOUBLE                NOT NULL,

	PRIMARY KEY          (`DCR_NUM`),

	constraint FK_SHOP_CURRENCIES_CR_CODE_CURRENCIES foreign key (CR_CODE) references CURRENCIES(CR_CODE)

) ENGINE=InnoDB;

CREATE TABLE if not exists `BRANDS` (

	`B_ID`                 BIGINT                NOT NULL    AUTO_INCREMENT,
	`B_NAME`               TEXT                  NOT NULL,

	PRIMARY KEY          (`B_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `P_AVAIL` (

	`PV_ID`              	INT                   NOT NULL    AUTO_INCREMENT,
	`PV_NAME`               TEXT                  NOT NULL,
	PRIMARY KEY          (`PV_ID`)

) ENGINE=InnoDB;

create table if not exists PRICE_TYPES (

	PT_ID int not null auto_increment,
	PT_NAME text not null,
	CR_CODE char(3) not null,

	PT_RATE double,
	PT_MARK double,
	PT_INCR double,

	primary key (PT_ID),

	constraint FK_PRICE_TYPES_CR_CODE foreign key (CR_CODE) references CURRENCIES (CR_CODE)

) engine=InnoDB;

CREATE TABLE if not exists `PRODUCTS` (
	
	`P_ID`               BIGINT                NOT NULL AUTO_INCREMENT,
	`N_ID`               BIGINT                NOT NULL,
	`P_P0`               BIGINT                    NULL,
	
	`P_VOID`			tinyint not null default 0,
	`P_URI`             varchar(128)                 NULL,
	`P_NAME`               TEXT                  NOT NULL,
	`P_VARIANT`         varchar(255),
	`P_FULLNAME`           TEXT                      NULL,
	`P_TITLE`              TEXT                      NULL    DEFAULT NULL,
	`P_KEYWORDS`         MEDIUMTEXT            NOT NULL,
	`P_DESCR`        	MEDIUMTEXT            NOT NULL,
	`P_CSS`             VARCHAR(128)              NULL    DEFAULT NULL,

	`P_CODE`            VARCHAR(128)              NULL    DEFAULT NULL,
	`P_SRCH_CODE`       VARCHAR(128)              NULL    DEFAULT NULL,
	`P_BARCODE`         VARCHAR(128)              NULL    DEFAULT NULL,
	`B_ID`              BIGINT                    NULL    DEFAULT NULL,
	`P_MEASURE`         VARCHAR(128)          NOT NULL    DEFAULT '',
	`P_SIZE`            VARCHAR(128)          NOT NULL    DEFAULT '',
	
	`P_DESCR_SHORT`        LONGTEXT              NOT NULL,
	`P_DESCR_FULL`         LONGTEXT              NOT NULL,
	`P_DESCR_TECH`         LONGTEXT              NOT NULL,

	`P_PRICE`              DOUBLE                    NULL    DEFAULT NULL,
	`P_PRICE_OLD`          DOUBLE                    NULL    DEFAULT NULL,
	`P_PRICE_MIN`          DOUBLE                    NULL,
	`P_PRICE_MAX`          DOUBLE                    NULL,

	`PT_ID`                INT                       NULL,
	`P_PRICE_TV`           DOUBLE                    NULL,

	`PV_ID`         	INT                       NULL    DEFAULT NULL COMMENT 'FK to P_AVAIL  -- availability type',
	`P_AVAIL_COUNT`     INT                       NULL    DEFAULT NULL,
	
	`IS_NEW`               TINYINT               NOT NULL    DEFAULT 0,
	`IS_RECOMEND`          TINYINT               NOT NULL    DEFAULT 0,
	`IS_SPECIAL`           TINYINT               NOT NULL    DEFAULT 0,

	`P_FORSALE`            TINYINT               NOT NULL    DEFAULT 0,
	
	`D_CREATE`      DATETIME                  NULL    DEFAULT NULL,
	`D_MODIFY`   DATETIME                  NULL    DEFAULT NULL,
	`D_MODIFY_PRICE`   DATETIME                  NULL    DEFAULT NULL,
	
	`LOG_ALL`            INT                   NOT NULL,
	`LOG_TODAY`          INT                   NOT NULL,
	`LOG_DAY`            DATE                  NOT NULL,
	
	`P_ORDER` int NOT NULL,
	`P_ORDER_CHLD` int NULL,
	`P_HIDE` tinyint NOT NULL,

	`P_PICT_URI` varchar(128) default null,
	
	`EXTRA_1` text NULL,
	`EXTRA_2` text NULL,
	`EXTRA_3` text NULL,
	`EXTRA_4` text NULL,
	`EXTRA_5` text NULL,
	
	`EXTRA_INT` bigint NULL,
	`EXTRA_REAL` double NULL,

	PRIMARY KEY          (`P_ID`),

	INDEX                `IX_P_SRCH_CODE` (`P_SRCH_CODE`),
	
	UNIQUE INDEX         UIX_PRODUCTS_N_ID_P_URI (N_ID,P_URI),

	CONSTRAINT           `FK_PRODUCTS_N_ID_NODES`
	                     FOREIGN KEY    (`N_ID`)
	                     REFERENCES     `NODES` (N_ID),

	CONSTRAINT           `FK_PRODUCTS_B_ID_BRAND`
	                     FOREIGN KEY    (`B_ID`)
	                     REFERENCES     `BRANDS` (B_ID),

	CONSTRAINT           `FK_PRODUCTS_PV_ID_P_AVAIL`
	                     FOREIGN KEY    (`PV_ID`)
	                     REFERENCES     `P_AVAIL` (PV_ID),
						 
	CONSTRAINT           `FK_PRODUCTS_P_P0_PRODUCTS`
	                     FOREIGN KEY    (`P_P0`)
	                     REFERENCES     `PRODUCTS` (P_ID),

	CONSTRAINT           `FK_PRODUCTS_PT_ID`
	                     FOREIGN KEY    (`PT_ID`)
	                     REFERENCES     `PRICE_TYPES` (`PT_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `PRODUCTS_INHERIT` (
	`P_ID` bigint(20) NOT NULL,
	`P_NAME` tinyint,
	`P_FULLNAME` tinyint,
	`P_TITLE` tinyint,
	`P_KEYWORDS` tinyint,
	`P_DESCR` tinyint,
	`P_CSS` tinyint,
	`P_CODE` tinyint,
	`P_BARCODE` tinyint,
	`B_ID` tinyint,
	`P_MEASURE` tinyint,
	`P_SIZE` tinyint,
	`P_DESCR_SHORT` tinyint,
	`P_DESCR_FULL` tinyint,
	`P_DESCR_TECH` tinyint,
	`P_PRICE` tinyint,
	`IS_NEW` tinyint,
	`IS_RECOMEND` tinyint,
	`IS_SPECIAL` tinyint,
	`PRODUCT_TAGS` tinyint,
	`PRODUCT_CRETERIAS` tinyint,
	`P_PICT_URI` tinyint,
	primary key (P_ID),
	constraint FK_PRODUCTS_INHERIT_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS(P_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists `PRODUCTS_LINKS` (

	`PL_KIND`            CHAR(4)               NOT NULL,
	`P_ID_M`             BIGINT                NOT NULL,
	`P_ID_S`             BIGINT                NOT NULL,
	`PL_ORDER`           INT                   NULL,

	PRIMARY KEY          (`PL_KIND`,`P_ID_M`,`P_ID_S`),

	INDEX                `IX_PRODUCTS_LINKS_P_ID_M` (`PL_KIND`,`P_ID_M`),
	INDEX                `IX_PRODUCTS_LINKS_P_ID_S` (`PL_KIND`,`P_ID_S`),

	CONSTRAINT           `FK_PRODUCTS_LINKS_P_ID_M_PRODUCTS`
	                     FOREIGN KEY    (`P_ID_M`)
	                     REFERENCES     `PRODUCTS` (P_ID),

	CONSTRAINT           `FK_PRODUCTS_LINKS_P_ID_S_PRODUCTS`
	                     FOREIGN KEY    (`P_ID_S`)
	                     REFERENCES     `PRODUCTS` (P_ID),

	INDEX `IX_PRODUCTS_LINKS_PL_ORDER` (`PL_ORDER`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `CATEGORIES` (

	`N_ID`               BIGINT                NOT NULL,
	
	`CAT_NAME`               TEXT                  NOT NULL,
	`CAT_FULLNAME`           TEXT                      NULL,
	`CAT_TITLE`              TEXT                      NULL    DEFAULT NULL,
	`CAT_KEYWORDS`           MEDIUMTEXT            NOT NULL,
	`CAT_DESCR`        	MEDIUMTEXT            NOT NULL,
	`CAT_CSS`                VARCHAR(128)              NULL    DEFAULT NULL,

	`TEXT_TOP`           LONGTEXT              NOT NULL,
	`TEXT_BOTT`          LONGTEXT              NOT NULL,

	`CAT_PICT_URI` 		varchar(128) null,

	UNIQUE INDEX         `IX_CAT_PICT_URI` (`CAT_PICT_URI`),

	CONSTRAINT           `FK_CATEGORIES_N_ID_NODES`
	                     FOREIGN KEY    (`N_ID`)
	                     REFERENCES     `NODES` (N_ID),
	constraint NN primary key(N_ID)

) ENGINE=InnoDB;

CREATE TABLE if not exists `TAGS` (

	`T_ID`                 BIGINT                NOT NULL    AUTO_INCREMENT,
	`T_NAME`               VARCHAR (63)          NOT NULL,

	PRIMARY KEY          (`T_ID`),

	UNIQUE INDEX `IDX_T_NAME` (`T_NAME`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `PRODUCT_TAGS` (

	`P_ID`            BIGINT                NOT NULL,
	`T_ID`            BIGINT                NOT NULL,

	CONSTRAINT           `FK_PRODUCT_TAGS_P_ID_PRODUCTS`
	                     FOREIGN KEY    (`P_ID`)
	                     REFERENCES     `PRODUCTS` (P_ID),

	CONSTRAINT           `FK_PRODUCT_TAGS_T_ID_TAGS`
	                     FOREIGN KEY    (`T_ID`)
	                     REFERENCES     `TAGS` (T_ID)
) ENGINE=InnoDB;

CREATE TABLE if not exists `CRITERIAS` (

	`CR_ID`                 BIGINT                NOT NULL    AUTO_INCREMENT,
	`CR_NAME`               VARCHAR (255)         NOT NULL,
	`CR_KIND`               char (8)          	NOT NULL collate utf8_bin comment '8 symbol kind example ''apsphere''',
	`CR_ORDER` int(11) NOT NULL,

	PRIMARY KEY          (`CR_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `PRODUCT_CRETERIAS` (

	`CR_ID`             BIGINT                NOT NULL,
	`P_ID`            	BIGINT                NOT NULL,
	`PC_SELF`           TINYINT               NOT NULL    DEFAULT 1,

	CONSTRAINT           `FK_PRODUCT_CRETERIAS_P_ID_PRODUCTS`
	                     FOREIGN KEY    (`P_ID`)
	                     REFERENCES     `PRODUCTS` (P_ID),

	CONSTRAINT           `FK_PRODUCT_CRETERIAS_CR_ID_CRITERIAS`
	                     FOREIGN KEY    (`CR_ID`)
	                     REFERENCES     `CRITERIAS` (CR_ID)
) ENGINE=InnoDB;

CREATE TABLE if not exists `PRODUCT_PICTS` (

	`P_ID`            BIGINT                NOT NULL,
	`PP_N`            INT                   NOT NULL,
	`PP_NAME`         TEXT                      NULL,

	UNIQUE KEY UQ (`P_ID`, `PP_N`),

	CONSTRAINT           `FK_PRODUCT_PICTS_P_ID_PRODUCTS`
	                     FOREIGN KEY    (`P_ID`)
	                     REFERENCES     `PRODUCTS` (P_ID)

) ENGINE=InnoDB;



CREATE TABLE SPEC_GROUPS (

	SG_ID bigint not null AUTO_INCREMENT,
	SG_CODE varchar(8) collate utf8_bin,
	SG_NAME varchar(255),
    SG_ORDER int not null default 0,
	
	PRIMARY KEY (SG_ID),
	UNIQUE INDEX (SG_CODE)

) ENGINE=InnoDB;

CREATE TABLE SPEC_CLASSES (

	SC_ID bigint not null AUTO_INCREMENT,
	SG_ID bigint null,
    
	SC_CODE varchar(8) collate utf8_bin,
	SC_NAME varchar(255) not null,
    
    SC_MULTY tinyint not null default 0,
	SC_DTYPE enum ('REFBOOK','REFBOOK+','STRING','INTEGER','FLOAT','MONEY','BOOLEAN','DATETIME') not null,
    
	SC_ORDER int not null default 0,
	SC_ORDER_GLOBAL int null,

	PRIMARY KEY (SC_ID),
	UNIQUE INDEX (SG_ID,SC_CODE),
    
	constraint FK_SPEC_CLASSES_SG_ID_SPEC_GROUPS foreign key (SG_ID) references SPEC_GROUPS(SG_ID)

) ENGINE=InnoDB;

/*
alter table SPEC_CLASSES add SC_ORDER_GLOBAL int null;
*/

CREATE TABLE SPEC_REFBOOK (

	SR_ID bigint not null AUTO_INCREMENT,
	SC_ID bigint not null,
    
    SR_VOID tinyint not null default 0,
	SR_CODE varchar(8) collate utf8_bin,
	SR_ORDER int not null default 0,
    
	SR_NAME varchar(255),
	
	PRIMARY KEY (SR_ID),
	UNIQUE INDEX (SC_ID,SR_CODE),
    
	constraint FK_SPEC_REFBOOK_SC_ID_SPEC_CLASSES foreign key (SC_ID) references SPEC_CLASSES(SC_ID)

) ENGINE=InnoDB;

CREATE TABLE PRODUCT_SPECS (

    P_ID bigint not null,
	SC_ID bigint not null,
    
	SR_ID bigint null,
	PS_VALUE_STRING text null,
	PS_VALUE_INTEGER bigint null,
	PS_VALUE_FLOAT double null,
	PS_VALUE_MONEY decimal(17,4) null,
	PS_VALUE_BOOLEAN tinyint(1) null,
	PS_VALUE_DATETIME datetime null,

	INDEX (P_ID),
	INDEX (P_ID,SC_ID),

	constraint FK_PRODUCT_SPECS_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS(P_ID),
	constraint FK_PRODUCT_SPECS_SC_ID_SPEC_CLASS foreign key (SC_ID) references SPEC_CLASSES(SC_ID),
	constraint FK_PRODUCT_SPECS_SR_ID_SPEC_REFBOOK foreign key (SR_ID) references SPEC_REFBOOK(SR_ID)
    
) ENGINE=InnoDB;

CREATE TABLE NODE_SPEC_GROUPS (

	N_ID bigint not null,
	SG_ID bigint not null,
    NSG_KIND char(4),
	
	PRIMARY KEY (N_ID,SG_ID),
    
	constraint FK_NODE_SPEC_GROUPS_SC_ID_SPEC_SPEC_GROUPS foreign key (SG_ID) references SPEC_GROUPS(SG_ID),
	constraint FK_NODE_SPEC_GROUPS_N_ID_CATEGORIES foreign key (N_ID) references CATEGORIES(N_ID)
    
) ENGINE=InnoDB;

CREATE TABLE PRODUCT_SPEC_GROUPS (

	P_ID bigint not null,
	SG_ID bigint not null,
    PSG_KIND char(4),
	
	PRIMARY KEY (P_ID,SG_ID),
    
	constraint FK_PRODUCT_SPEC_GROUPS_SC_ID_SPEC_SPEC_GROUPS foreign key (SG_ID) references SPEC_GROUPS(SG_ID),
	constraint FK_PRODUCT_SPEC_GROUPS_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS(P_ID)
    
) ENGINE=InnoDB;


# PAGES & MENUS

CREATE TABLE if not exists `PAGES` (

  `PG_ID`                      INT                  NOT NULL    AUTO_INCREMENT,
  `PG_URI_NAME`                VARCHAR(128)         NOT NULL,
  `PG_CAPTION`                 TEXT                 NOT NULL,
  `PG_TITLE`                   TEXT                     NULL    DEFAULT NULL,
  `PG_KEYWORDS`                TEXT                     NULL    DEFAULT NULL,
  `PG_DESCRIPTION`             TEXT                     NULL    DEFAULT NULL,
  `PG_CSS`                     VARCHAR(32)              NULL    DEFAULT NULL,
  `PG_P0`                      INT                      NULL    DEFAULT NULL,
  `PG_RO`                      TINYINT              NOT NULL    DEFAULT 0,
  `PG_MNU`                     TINYINT              NOT NULL    DEFAULT 1,
  `PG_MREF`                    TINYINT              NOT NULL    DEFAULT 0,
  `PG_ALTREF`                  VARCHAR(255)             NULL    DEFAULT NULL,
  `PG_ORDER`                   INT                  NOT NULL,
  `PG_TEXT`                    LONGTEXT             NOT NULL,

  PRIMARY KEY            (`PG_ID`),

  UNIQUE KEY             `UQ_URI_P0` (PG_P0, PG_URI_NAME),

  CONSTRAINT             `FK_PAGES_PG_P0_PAGES`
	                     FOREIGN KEY    (`PG_P0`)
	                     REFERENCES     `PAGES` (`PG_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `ARTICLES` (

  `A_ID`                      INT                  NOT NULL    AUTO_INCREMENT,
  `A_KIND`                    CHAR(4)              NOT NULL    collate utf8_bin,
  `A_DATE`                    DATE                 NOT NULL,
  `A_DT_PUB`                  DATETIME             NOT NULL,
  `A_HIDDEN`                  TINYINT              NOT NULL    DEFAULT 0,
  `A_TITLE`                   TEXT                     NULL    DEFAULT NULL,
  `A_KEYWORDS`                TEXT                     NULL    DEFAULT NULL,
  `A_DESCRIPTION`             TEXT                     NULL    DEFAULT NULL,
  `A_CSS`                     VARCHAR(32)              NULL    DEFAULT NULL,
  `A_CAPTION`                 TEXT                 NOT NULL,
  `A_URI`                     VARCHAR(128)             NULL,
  `A_TXT_SHORT`               TEXT                 NOT NULL,
  `A_TXT_FULL`                LONGTEXT                 NULL    DEFAULT NULL,
  `A_HREF`                    TEXT                     NULL    DEFAULT NULL,
  `A_LINK`                    TEXT                     NULL    DEFAULT NULL,

  PRIMARY KEY                      (`A_ID`),
  
  INDEX IX_KIND (A_KIND),

  UNIQUE KEY UQ_A_URI (A_KIND,A_URI)

) ENGINE=InnoDB;

# APPLICATION

CREATE TABLE if not exists `SETTINGS` (

  `inf_shopname`                TEXT                 NOT NULL,
  `inf_shopurl`                 TEXT                 NOT NULL,
  `inf_host`                    TEXT                 NOT NULL default '',
  `inf_keywords`                TEXT                 NOT NULL,
  `inf_description`             TEXT                 NOT NULL,
  `inf_nameintitle`             TINYINT              NOT NULL default 1,
  `img_max_width`               INT                  NOT NULL,
  `img_max_height`              INT                  NOT NULL,
  `img_small_width`             INT                  NOT NULL,
  `img_small_height`            INT                  NOT NULL,
  `img_middle_width`            INT                  NOT NULL,
  `img_middle_height`           INT                  NOT NULL,
  `img_small_width_cat`         INT                  NOT NULL,
  `img_small_height_cat`        INT                  NOT NULL,
  `img_middle_width_cat`        INT                  NOT NULL,
  `img_middle_height_cat`       INT                  NOT NULL,
  `img_art_width`               INT                  NOT NULL,
  `img_art_height`              INT                  NOT NULL,
  `num_onpage_prod`             INT                  NOT NULL,
  `num_box_rand`                INT                  NOT NULL,
  `num_box_new`                 INT                  NOT NULL,
  `num_box_recomend`            INT                  NOT NULL,
  `num_onpage_news`             INT                  NOT NULL,
  `num_box_news`                INT                  NOT NULL,
  `num_onpage_articles`         INT                  NOT NULL,
  `num_box_articles`            INT                  NOT NULL,
  `num_onpage_specials`         INT                  NOT NULL,
  `num_box_specials`            INT                  NOT NULL,
  `num_onpage_orders`           INT                  NOT NULL,
  `ord_mail`                    VARCHAR(128)         NOT NULL,
  `ord_initstatus`              INT                  NOT NULL,
  `ed_wysiwyg`                  TINYINT              NOT NULL default 1,
  `chk_hash`                    VARCHAR(32)          NOT NULL,
  `chk_state`                   INT                  NOT NULL default 0,
  `num_xpd_rand`                INT                  NOT NULL,
  `name_crit1`                  TEXT                     NULL,
  `name_crit2`                  TEXT                     NULL,
  `name_crit3`                  TEXT                     NULL,
  `name_etab1`                  TEXT                     NULL,
  `name_etab2`                  TEXT                     NULL,
  `name_etab3`                  TEXT                     NULL,
  `name_etab4`                  TEXT                     NULL,
  `name_etab5`                  TEXT                     NULL

) ENGINE=InnoDB;

CREATE TABLE if not exists `SUBSCRIBERS` (

	`SC_ID`                BIGINT                NOT NULL    AUTO_INCREMENT,
	`SC_EMAIL`             VARCHAR(255)          NOT NULL,
	`SC_DATETIME`          DATETIME              NOT NULL,
	`SC_IP`                VARCHAR(16)           NOT NULL,
	`SC_UNSUBSCRIBE`       VARCHAR(32)           NOT NULL,
	`SC_BROADCASTED`       DATETIME                  NULL    DEFAULT NULL,

	PRIMARY KEY          (`SC_ID`),
	UNIQUE KEY           (`SC_EMAIL`),
	INDEX                (`SC_UNSUBSCRIBE`),
	INDEX                (`SC_BROADCASTED`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `TEXTS` (

	`ID`               BIGINT                NOT NULL,
	`TX_TYPE`          VARCHAR(4)            NOT NULL,

	`TX_TEXT`          LONGTEXT              NOT NULL,

	PRIMARY KEY (TX_TYPE,ID),

	FULLTEXT INDEX (TX_TEXT)

) ENGINE=MyISAM;

/* %%% CART ORDERS*/
CREATE TABLE if not exists AGENTS (
	AG_ID bigint not null AUTO_INCREMENT,
	/*G_P0 bigint null,*/
	AG_NAME varchar(255) null,
	AG_FNAME varchar(255) null,
	AG_LNAME varchar(255) null,
	AG_PHONE varchar(255) null,
	AG_EMAIL varchar(512) null,
	
	PRIMARY KEY (`AG_ID`)/*,
	constraint FK_AGENTS_AG_P0_AGENTS foreign key (AG_P0) references AGENTS(AG_ID)*/
) ENGINE=InnoDB AUTO_INCREMENT=100;

CREATE TABLE if not exists CART (
	C_ID bigint not null AUTO_INCREMENT,
	AC_ID bigint null,
	AG_ID bigint null,
	PRIMARY KEY (`C_ID`),
	constraint FK_CART_AG_ID_AGENTS foreign key (AG_ID) references AGENTS(AG_ID),
	constraint FK_CART_AC_ID_AUTH_CUST_COOKIES foreign key (AC_ID) references AUTH_CUST_COOKIES(ID),
	unique index UIX_CART_AC_ID(AC_ID)
/*
	
	alter table CART drop FOREIGN KEY FK_CART_HL_ID_HASH_LOGINS;
	alter table CART drop index UIX_CART_HL_ID;
	alter table CART change column HL_ID AC_ID bigint null;
	alter table CART add constraint FK_CART_AC_ID_AUTH_CUST_COOKIES foreign key (AC_ID) references AUTH_CUST_COOKIES(ID);
	alter table CART add unique index UIX_CART_AC_ID(AC_ID);
*/
) ENGINE=InnoDB;



CREATE TABLE if not exists CART_DETAILS (
	CD_ID bigint not null AUTO_INCREMENT,
	C_ID bigint not null,
	CD_QTY int not null,
	CD_PRICE double null,
	P_ID bigint not null,
	PRIMARY KEY (`CD_ID`),
	unique index UIX_CART_DETAILS_C_ID_P_ID(C_ID,P_ID),
	constraint FK_CART_DETAILS_C_ID_CART foreign key (C_ID) references CART(C_ID),
	constraint FK_CART_DETAILS_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS(P_ID)
/*
	alter table CART_DETAILS modify column CD_PRICE double not null;

*/
) ENGINE=InnoDB;

CREATE TABLE if not exists `CT_SHIPPING` (
	CTH_ID INT NOT NULL AUTO_INCREMENT,
	CTH_VOID tinyint not null default 0,
	CTH_NAME varchar(255) not null,
	CTH_TITLE varchar(255) null,
	CTH_HINT text null,
	CTH_DESCR text null,
	PRIMARY KEY (`CTH_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists `CT_STATUS` (
	CTS_ID INT NOT NULL AUTO_INCREMENT,
	CTS_VOID tinyint not null default 0,
	CTS_NAME varchar(255) not null,
	CTS_TITLE varchar(255) null,
	CTS_HINT text null,
	CTS_DESCR text null,
	PRIMARY KEY (`CTS_ID`)

) ENGINE=InnoDB;


CREATE TABLE if not exists `CT_PAYMENT` (
	CTP_ID INT NOT NULL AUTO_INCREMENT,
	CTP_VOID tinyint not null default 0,
	CTP_NAME varchar(255) not null,
	CTP_TITLE varchar(255) null,
	CTP_HINT text null,
	CTP_DESCR text null,
	PRIMARY KEY (`CTP_ID`)

) ENGINE=InnoDB;

CREATE TABLE if not exists ORDERS (
	OR_ID bigint not null AUTO_INCREMENT,
	AC_ID bigint null,
	AG_ID bigint null,
	
	CTH_ID INT NULL DEFAULT NULL, 
	CTP_ID INT NULL DEFAULT NULL, 
	CTS_ID INT not null, 
	
	OR_DATE DATETIME NOT NULL,
	OR_NAME VARCHAR(255) NOT NULL,
	OR_PHONE VARCHAR(64) NOT NULL,
	OR_COUNTRY TEXT NULL,
    OR_CITY TEXT NULL,
	OR_REGION TEXT NULL,
	OR_ADDRESS TEXT NULL,
	OR_EMAIL VARCHAR(128) NULL    DEFAULT NULL,
	OR_MEMO TEXT NULL,
	OR_COUPON TEXT NULL    DEFAULT NULL,

	PRIMARY KEY (`OR_ID`),
	constraint FK_ORDERS_AG_ID_AGENTS foreign key (AG_ID) references AGENTS(AG_ID),
	constraint FK_ORDERS_AC_ID_AUTH_CUST_COOKIES foreign key (AC_ID) references AUTH_CUST_COOKIES(ID),

	constraint FK_ORDERS_CTH_ID_CT_SHIPPING foreign key (CTH_ID) references CT_SHIPPING(CTH_ID),
	constraint FK_ORDERS_CTP_ID_CT_PAYMENT foreign key (CTP_ID) references CT_PAYMENT(CTP_ID),
	constraint FK_ORDERS_CTS_ID_CT_STATUS foreign key (CTS_ID) references CT_STATUS(CTS_ID)
) ENGINE=InnoDB;
/*
	alter table ORDERS change column OR_NAM  OR_NAME VARCHAR(255) NOT NULL
	alter table ORDERS drop FOREIGN KEY FK_ORDERS_HL_ID_HASH_LOGINS;
	alter table ORDERS change column HL_ID AC_ID bigint null;
	alter table ORDERS add constraint FK_ORDERS_AC_ID_AUTH_CUST_COOKIES foreign key (AC_ID) references AUTH_CUST_COOKIES(ID);
*/



CREATE TABLE if not exists ORDER_DETAILS (
	ORD_ID bigint not null AUTO_INCREMENT,
	OR_ID bigint not null,
	
	P_ID bigint null,
	B_ID bigint null, 	

	ORD_QTY double not null,
	ORD_PRICE double not null,
  
	P_NAME varchar(255) null,
	P_VARIANT varchar(255) null,
	P_CODE varchar(255) null,
	P_BARCODE varchar(255) null,
	B_NAME varchar(255) null,

	PRIMARY KEY (`ORD_ID`),
	constraint FK_ORDER_DETAILS_OR_ID_ORDERS foreign key (OR_ID) references ORDERS(OR_ID),
	constraint FK_ORDER_DETAILS_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS(P_ID),
	constraint FK_ORDER_DETAILS_B_ID_BRANDS foreign key (B_ID) references BRANDS(B_ID)

) ENGINE=InnoDB;


create table if not exists PRODUCTS_VOTES (

	V_ID bigint not null auto_increment,
	P_ID bigint not null,

	V_RATE int not null,

	V_IP varchar(15),
	V_DATE timestamp not null default current_timestamp,
	
	primary key (V_ID),

	constraint FK_VOTES_P_ID_PRODUCTS foreign key (P_ID) references PRODUCTS (P_ID),

	unique key UQ_PRODUCTS_VOTES_P_ID_V_IP (P_ID, V_IP)

) engine=InnoDB;

replace into VERSIONS (V_MODULE, V_VERSION) values
('shop-r2is-struct','2016.0000');