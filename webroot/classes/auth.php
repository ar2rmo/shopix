<?
require_once LIBRARIES_PATH.'auth.php';
require_once LIBRARIES_PATH.'dbproxy.php';

class auth_lp_adm extends auth_lp_base implements i_auth {
	const COOKIE=null;
	const SESS='auth_adm';
	
	const COOKIE_TTL=7200;
	const PREC_TTL=86400;
	
	protected static function db_check_uid($uid) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_CHECK_UID(:uid)',array('uid'=>$uid));
		
		if ($dt) {
			return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
		}
		
		return false;
	}
	
	protected static function db_check_lp($login,$phash) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_CHECK_LP(:login,:phash)',array('login'=>$login,'phash'=>$phash));
		
        if ($dt) {
        	return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
        }
		
		return false;
	}
	
	protected static function db_check_cookie($uid,$phash,$chash,$ttl) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_CHECK_COOKIE(:uid,:phash,:chash,:ttl)',array('uid'=>$uid,'phash'=>$phash,'chash'=>$chash,'ttl'=>$ttl));
		
        if ($dt) {
        	return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
        }
		
		return false;
	}
	
	protected static function db_set_cookie($uid,$ip,$chash,$ttl) {
		DBP::Exec('CALL AUTH_ADM_SET_COOKIE(:uid,:ip,:chash,:ttl)',array('uid'=>$uid,'ip'=>$ip,'chash'=>$chash,'ttl'=>$ttl));
	}
	
	protected static function db_prec_get_token($login,&$uid,$ttl) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_PREC_GET_TOKEN(:login,:ttl)',array('login'=>$login,'ttl'=>$ttl));
		
		if ($dt) {
			$uid=$dt['UID'];

			return $dt['TOKEN'];
		}

		return false;
	}
	
	protected static function db_prec_kill_token($tid) {
		DBP::Exec('CALL AUTH_ADM_PREC_KILL_TOKEN(:tid)',array('tid'=>$tid));
	}
	
	protected static function db_prec_check_token($token) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_PREC_GET_TOKEN(:token)',array('token'=>$token));

		if ($dt) {
			return array('tid'=>$dt['TID'],'uid'=>$dt['UID']);
		} else {
			return false;
		}
	}
	
	protected static function db_change_password($uid,$phash) {
		DBP::Exec('CALL AUTH_ADM_CHANGE_PWORD(:uid,:phash)',array('uid'=>$uid,'phash'=>$phash));
	}
}

class auth_hash_cust extends auth_hash_base implements i_auth {
	const COOKIE='auth_cust';
	const SESS='auth_cust';
	
	protected static function db_check_uid($uid) {
		$dt=DBP::ExecSingleRow('CALL AUTH_CUST_CHECK_UID(:uid)',array('uid'=>$uid));
		
		if ($dt) {
			return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
		}
		
		return false;
	}
	
	protected static function db_check_cookie($uid,$chash,$ttl) {
		$dt=DBP::ExecSingleRow('CALL AUTH_CUST_CHECK_COOKIE(:uid,:chash,:ttl)',array('uid'=>$uid,'chash'=>$chash,'ttl'=>$ttl));
		
        if ($dt) {
        	return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
        }
		
		return false;
	}
	
	protected static function db_set_cookie($ip,$chash,$ttl) {
		$dt=DBP::ExecSingleRow('CALL AUTH_CUST_SET_COOKIE(:ip,:chash,:ttl)',array('ip'=>$ip,'chash'=>$chash,'ttl'=>$ttl));
		
		if ($dt) {
        	return array('uid'=>$dt['UID'],'pl'=>constant('auth::'.$dt['PL']));
        }
		
		return false;
	}
	
	protected static function db_prec_get_token($login,&$uid) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_PREC_GET_TOKEN(:login)',array('login'=>$login));
		
		if ($dt) {
			$uid=$dt['UID'];

			return $dt['TOKEN'];
		}

		return false;
	}
	
	protected static function db_prec_kill_token($tid) {
		DBP::Exec('CALL AUTH_ADM_PREC_KILL_TOKEN(:tid)',array('tid'=>$tid));
	}
	
	protected static function db_prec_check_token($token) {
		$dt=DBP::ExecSingleRow('CALL AUTH_ADM_PREC_GET_TOKEN(:token)',array('token'=>$token));

		if ($dt) {
			return array('tid'=>$dt['TID'],'uid'=>$dt['UID']);
		} else {
			return false;
		}
	}
	
	protected static function db_change_password($uid,$phash) {
		DBP::Exec('CALL AUTH_ADM_CHANGE_PWORD(:uid,:phash)',array('uid'=>$uid,'phash'=>$phash));
	}
}
?>