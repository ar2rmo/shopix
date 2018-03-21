<?
require_once LIBRARIES_PATH.'3rdparty/kcaptcha/kcaptcha.php';

class mod_captcha extends module {
	function body() {
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
		$captcha = new KCAPTCHA();
		$_SESSION['kcaptcha'] = $captcha->getKeyString();
	}
}
?>