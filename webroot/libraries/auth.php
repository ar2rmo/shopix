<?
class auth_cookie {
	protected static function get_domain() {
		$sn=$_SERVER['SERVER_NAME'];
		if (preg_match('/^www\.(.+)$/',$sn,$mchs)) $sn=$mchs[1];
		return $sn;
	}
	
	public static function do_set($name,$cook,$ttl) {
		return setcookie($name, $cook, time()+$ttl, '/', static::get_domain());
	}
	
	public static function do_unset($name) {
		return setcookie($name, '', 0, '/', static::get_domain());
	}
}

interface i_auth {
	public static function get_uid();
	public static function get_pl();
	public static function check_priv($lv);
}

abstract class auth implements i_auth {
	protected static $uid=null;
	protected static $pl=self::PL_GUEST;

	const UID_GUEST=0;
	
	const PL_ROOT=0;
	const PL_ADMIN=1;
	const PL_USER=16;
	const PL_ANON=64;
	const PL_GUEST=128;

	function __construct() {
		static::authenticate();
	}

	function __get($key) {
		switch ($key) {
			case 'uid':
				return $this->get_uid();
			case 'pl':
				return $this->get_pl();
		}

		trigger_error('There\'s no parameter "'.$key.'" in "'.static::SESS.'"',E_USER_ERROR);
	}

	public static function get_pl() {
		return static::$pl;
	}

	public static function check_priv($lv) {
		return (static::$pl<=$lv);
	}

	public static function get_uid() {
		return static::$uid;
	}

	//abstract public static function authenticate();
}

abstract class auth_lp_base extends auth implements i_auth {
	const COOKIE='auth';
	const SESS='lp';
	
	const COOKIE_TTL=2592000;
	const PREC_TTL=86400;
	
	function __construct() {
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
		parent::__construct();
	}
	
	/*abstract protected static function db_check_uid($uid);
	abstract protected static function db_check_lp($login,$phash);
	abstract protected static function db_check_cookie($uid,$phash,$chash,$ttl);
	abstract protected static function db_set_cookie($uid,$ip,$chash,$ttl);*/

	public static function authenticate() {
		if (isset($_SESSION[static::SESS.'uid'])) {
			static::authenticate_uid($_SESSION[static::SESS.'uid']);
		} elseif (static::COOKIE && isset($_COOKIE[static::COOKIE])) {
			static::authenticate_cookie($_COOKIE[static::COOKIE]);
		} else {
			static::unsuccess();
		}
	}
	
	private static function success($user) {
		static::$uid=$user['uid'];
		static::$pl=$user['pl'];
			
		$_SESSION[static::SESS.'uid']=static::$uid;
	}
	
	private static function unsuccess() {
		static::$uid=auth::UID_GUEST;
		static::$pl=auth::PL_GUEST;
			
		if (isset($_SESSION[static::SESS.'uid'])) unset($_SESSION[static::SESS.'uid']);
	}

	private static function authenticate_uid($uid) {
        $user=static::db_check_uid($uid);
		
		if ($user) static::success($user);
		else static::unsuccess();

        return $user;
	}

	private static function authenticate_lp($login,$phash) {
		$user=static::db_check_lp($login,$phash);
		
		if ($user) static::success($user);
		else static::unsuccess();

        return $user;
	}

	public static function authenticate_cookie($cookie) {
		$ca=explode('-',$cookie);
		if ((count($ca)==2)&&is_numeric($ca[0])&&preg_match('/^[0-9a-f]{64}$/',$ca[1])) {
			$uid=(int)$ca[0];
			$sh=md5($ca[1]);
			$ph=substr($ca[1],0,32);
		} else {
			return false;
		}

		$user=static::db_check_cookie($uid,$ph,$sh,static::COOKIE_TTL);
        
		if ($user) static::success($user);
		else static::unsuccess();

        return $user;
	}

	public static function login($login,$phash) {
        $user=static::authenticate_lp($login,$phash);
		
		if ($user) {
        	if (static::COOKIE) static::setcook($phash);

        	return true;
        } else {
        	return false;
        }
	}

	private static function setcook($phash) {
        if (is_null(static::$uid)) return;

        $hex=array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
        $salt=array_fill(0,32,'0');
        foreach ($salt as &$hnum) $hnum=$hex[rand(0,15)];

        $salt=implode($salt);
        $uid=static::$uid;
        $cook=$uid.'-'.$phash.$salt;
        $chash=md5($phash.$salt);

        if (auth_cookie::do_set(static::COOKIE, $cook, static::COOKIE_TTL)) {
	        static::db_set_cookie($uid,$_SERVER['REMOTE_ADDR'],$chash,static::COOKIE_TTL);
		}
	}

	public function logout() {
		static::unsuccess();
		auth_cookie::do_unset(static::COOKIE);
	}

	
	
	/*abstract protected static function db_prec_get_token($login,&$uid,$ttl);
	abstract protected static function db_prec_kill_token($tid);
	abstract protected static function db_prec_check_token($token);
	abstract protected static function db_change_password($uid,$phash);*/
	
	public static function prec_get_token($login,&$uid) {
		$token=static::db_prec_get_token($login,$uid,static::PREC_TTL);

		return $token;
	}
	
	public static function prec_kill_token($tid) {
		static::db_prec_kill_token($tid);
	}

	public static function prec_check_token($token) {
		$token=static::db_prec_check_token($token);

		return $token;
	}

	public static function change_password($phash,$uid=false) {
		if ($uid!==false) {
			$cuid=$uid;
		} elseif (!is_null($this->uid)) {
			$cuid=$this->uid;
		} else {
			return;
		}

		static::db_change_password($cuid,$phash);
	}
}

abstract class auth_hash_base extends auth implements i_auth {
	const COOKIE='authhash';
	const SESS='hash';
	
	const COOKIE_TTL=31536000;
	
	function __construct() {
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
		parent::__construct();
	}
	
	//abstract protected static function db_check_uid($uid);
	//abstract protected static function db_check_cookie($uid,$chash,$ttl);
	//abstract protected static function db_set_cookie($ip,$chash,$ttl);

	public static function authenticate() {
		static::pauthenticate();
	}
	
	public static function authenticate_force() {
		static::pauthenticate(true);
	}
	
	private static function pauthenticate($force=false) {
		if (isset($_SESSION[static::SESS.'uid'])) {
			static::authenticate_uid($_SESSION[static::SESS.'uid']);
		} elseif (isset($_COOKIE[static::COOKIE])) {
			static::authenticate_cookie_check($_COOKIE[static::COOKIE]);
		} elseif ($force) {
			static::authenticate_cookie_make();
		} else {
			static::unsuccess();
		}
	}
	
	private static function success($user) {
		static::$uid=$user['uid'];
		static::$pl=$user['pl'];
			
		$_SESSION[static::SESS.'uid']=static::$uid;
	}
	
	private static function unsuccess() {
		static::$uid=auth::UID_GUEST;
		static::$pl=auth::PL_GUEST;
			
		if (isset($_SESSION[static::SESS.'uid'])) unset($_SESSION[static::SESS.'uid']);
	}

	private static function authenticate_uid($uid) {
        $user=static::db_check_uid($uid);
		
		if ($user) static::success($user);
		else static::unsuccess();

        return $user;
	}

	private static function authenticate_cookie_check($cookie) {
		$ca=explode('-',$cookie);
		if ((count($ca)==2)&&is_numeric($ca[0])&&preg_match('/^[0-9a-f]{32}$/',$ca[1])) {
			$uid=(int)$ca[0];
			$sh=md5($ca[1]);
		} else {
			return false;
		}

		$user=static::db_check_cookie($uid,$sh,static::COOKIE_TTL);
        
		if ($user) static::success($user);
		else static::unsuccess();

        return $user;
	}

	private static function authenticate_cookie_make() {
        $hex=array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
        $salt=array_fill(0,32,'0');
        foreach ($salt as &$hnum) $hnum=$hex[rand(0,15)];

        $salt=implode($salt);
        $chash=md5($salt);
		
		$user=static::db_set_cookie($_SERVER['REMOTE_ADDR'],$chash,static::COOKIE_TTL);
		
		static::success($user);
		
		$uid=$user['uid'];
        $cook=$uid.'-'.$salt;

		auth_cookie::do_set(static::COOKIE, $cook, static::COOKIE_TTL);
		
		return $user;
	}

	public function logout() {
		static::unsuccess();
		auth_cookie::do_unset(static::COOKIE);
	}
}
?>