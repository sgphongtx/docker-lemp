<?php
if ($_SERVER['HTTP_HOST'] == 'nhathepsaigon.vn') {
    define('DOMAIN', 'nhathepsaigon.vn');
} else {
    define('DOMAIN', 'nhadepsaithanh.com');
}
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('STATIC_URL', SITE_URL.'/static');
define('CONFIG',1);
define('PERMISSION_VIEW',1);
define('PERMISSION_ADD_EDIT',2);
define('PERMISSION_DEL',3);
define('DEFAULT_OFFSET', 0);
define('DEFAULT_LIMIT', 30);
define('STATUS_XOA',0);
define('STATUS_DA_DUYET',1);
define('STATUS_CHO_DUYET',2);
define('STATUS_LUU_NHAP',3);
define('STATUS_CRAWLER',4);
define('PAGES_STATUS_DELETE',0);
define('PAGES_STATUS_ACTIVE',1);
define('PAGES_STATUS_INACTIVE',2);
//Define limit top news
define('LIMIT_LIST_NEWS', 45);
define('LIMIT_TOP_NEWS', 2);
define('LIMIT_TOP_NEWS_CATE', 2);
define('LIMIT_TOP_NEWS_BOTTOM', 6);
define('LIMIT_24h_NEWS', 10);
define('LIMIT_LIST_TAGS', 30);
define('LIMIT_NEWS_ON_HOME', 45);
define('LIMIT_NEWS_ON_CATE_HOME', 6);
define('LIMIT_LIST_OTHER_NEWS',15);
define('LIMIT_LIST_TOP_VIEWS_NEWS',10);
define('LIMIT_LIST_TOP_COMMENT_NEWS',10);
define('LIMIT_LIST_CATE_NEWS',5);
define('LIMIT_LIST_DETAIL_NEWS',5);
define('LIMIT_WIDGET',6);
define('MAX_SIZE_IMG', 5120); //Max size 5M
define('IMAGE_UPLOAD_DIR', '/home/www/nhathepsaigon.vn/public/static/uploads');
define('IMAGE_CAPTCHA_DIR', '/home/www/nhathepsaigon.vn/public/captcha');
define('THUMBNAIL_SHARE_DEFAULT', STATIC_URL.'/fe/images/logo_default.png');
define('GROUP_ID_FONTEND', 4); //Group User fontend
define('CODE_MA_HOA_CHUOI','NhaThepSaiGon');
//Token telegram SMS
define('TOKEN_TELEGRAM_SMS','bot224638940:AAHmPrS4GeybzY-U0Ng2XNxnNh7BaMeawW0');
//Group telegram SMS NhaThepSaiGon
define('GROUP_ID_TELEGRAM','-141008052');
//Cate video
define('CATE_VIDEO',47);
?>