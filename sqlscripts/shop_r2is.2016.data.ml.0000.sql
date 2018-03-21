update SETTINGS
set ord_mail='boss@medialine.com.ua';

update AUTH_ADM_USERS
set BANNED=1
where EMAIL='admin';

insert into AUTH_ADM_USERS (BANNED, EMAIL, NAME, PHASH, ROLE) values
(0, 'artur.moshkola@gmail.com', 'Артур Мошкола', MD5('qweqwe'), 'PL_ROOT'),
(0, 'boss@medialine.com.ua', 'Руслан Горюк', MD5('qweqwe'), 'PL_ROOT');