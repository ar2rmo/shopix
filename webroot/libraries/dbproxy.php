<?
	class DBP {
	    const db='default';
		
		protected static $link = null;
		protected static $err_mode = null;

	    public static function getLink() {
	        if (!is_null(static::$link)) {
	            return static::$link ;
	        }
			
	        $parse = parse_ini_file (CONFIG_PATH.'db.ini', true);

			$cnnCred = $parse[static::db];
	        $user = $cnnCred ["db_user"] ;
	        $password = $cnnCred ["db_password"] ;
            $dsn = $cnnCred ["db_dsn"];
	        $options = $parse ["db_options"] ;

	        $_opt=array();
            foreach($options as $k=>$v){
            	$_opt[constant('PDO::'.$k)]=$v;
            }
			
	        static::$link = new PDO($dsn, $user, $password, $_opt) ;
			static::$link -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			
	        return static::$link ;
	    }
		
		protected static function getErrMode() {
			if (!is_null(static::$err_mode)) {
	            return static::$err_mode;
	        }
			
			static::ErrorMode('TRIGGER');
			return static::$err_mode;
		}
		
		protected static function process_error($query,$err) {
			switch (static::getErrMode()) {
				case 'IGNORE':
				break;
				case 'TRIGGER':
					trigger_error('Error while executing query: "'.$query.'": '.$err[2].'.',E_USER_WARNING);
				break;
				case 'EXCEPTION':
				break;
			}
		}
		
		protected static function bind_params(PDOStatement $q, &$params) {
			foreach ($params as $k=>&$v) {
				if (is_null($v)) {
					$pn = PDO::PARAM_NULL;
				} elseif (is_object($v)) {
					if ($v instanceof DateTime) {
						$v=$v->format('Y-m-d H:i:s');
						$pn=false;
					}
				} elseif (is_numeric($v)) {
					$pn=PDO::PARAM_INT;
				} elseif (is_array($v)) {
					$v=implode(',',$v);
					$pn=PDO::PARAM_STR;
				} else {
					$pn=false;
				}
					
				$q->bindParam($k,$v,$pn);
			}
		}
		
		public static function ErrorMode($mode) {
			static::$err_mode=$mode;
		}

	    public static function __callStatic ( $name, $args ) {
	        $callback = array ( static:: getLink (), $name ) ;
	        return call_user_func_array ( $callback , $args ) ;
	    }

		public static function LnExec($link,$query,$param=null,$onread=null,$onend=null,$onerror=null) {
			if (is_null($param)) {
				$ts=microtime(true);
				$q=$link->query($query);
				if ($q===false) {
					if (!is_null($onerror) && is_callable($onerror)) {
						if (!$onerror($link->errorInfo()))
							static::process_error($query,$link->errorInfo());
					} else {
						static::process_error($query,$link->errorInfo());
					}
					return null;
				}
				$te=microtime(true);
			} else {
				$q=$link->prepare($query);
				if (is_callable($param)) {
					$param($q);
				} elseif (is_array($param)) {
					static::bind_params($q,$param);
				}

				$ts=microtime(true);
				if ($q->execute()===false) {
					if (!is_null($onerror) && is_callable($onerror)) {
						if (!$onerror($q->errorInfo()))
							static::process_error($query,$q->errorInfo());
					} else {
						static::process_error($query,$q->errorInfo());
					}
					return null;
				}
				$te=microtime(true);
			}
			
			if (defined('DEBUG_DBLOG')) {
				$st=array_reverse(debug_backtrace());
				$sp=array();
				foreach ($st as $s) $sp[]=$s['file'].':'.$s['line'];
				$ls=date('Y-m-d H:i:s  ');
				$ls.=str_pad(number_format($te-$ts,3),8,' ',STR_PAD_LEFT).' s   ';
				$ls.='"'.$query.'" - ';
				$ls.='"'.$_SERVER['REQUEST_URI'].'" - ';
				$ls.='"'.implode(', ',$sp).'"';
				$ls.="\n";
				//echo $ls;
				file_put_contents(DEBUG_DBLOG,$ls,FILE_APPEND | LOCK_EX);
			}
			
			if (!is_null($onread)){
				if ($onread===true) {
					$onread=$q->fetchAll(PDO::FETCH_ASSOC);
				} elseif (is_callable($onread)) {
					while ($row=$q->fetch(PDO::FETCH_ASSOC)) {
						$onread($row);
					}
				}
			}
			
			if (!is_null($onend) && is_callable($onend)){
				$onend($q);
			}
			
			$q->closeCursor();
			
			if (!is_null($onread) && is_array($onread)){
				return $onread;
			} else {
				return true;
			}
		}

		public static function Exec($query,$param=null,$onread=null,$onend=null,$onerror=null) {
			$ln=static::getLink();
			return static::LnExec($ln,$query,$param,$onread,$onend,$onerror);
		}
		
		public static function LnMExec($link,$query,$param=null,$onread=null,$onend=null,$onerror=null) {
			if (is_null($param)) {
				$ts=microtime(true);
				$q=$link->query($query);
				if ($q===false) {
					if (!is_null($onerror) && is_callable($onerror)) {
						if (!$onerror($link->errorInfo()))
							static::process_error($query,$link->errorInfo());
					} else {
						static::process_error($query,$link->errorInfo());
					}
					return null;
				}
				$te=microtime(true);
			} else {
				$q=$link->prepare($query);
				if (is_callable($param)) {
					$param($q);
				} elseif (is_array($param)) {
					static::bind_params($q,$param);
				}

				$ts=microtime(true);
				if ($q->execute()===false) {
					if (!is_null($onerror) && is_callable($onerror)) {
						if (!$onerror($q->errorInfo()))
							static::process_error($query,$q->errorInfo());
					} else {
						static::process_error($query,$q->errorInfo());
					}
					return null;
				}
				$te=microtime(true);
			}
			
			if (defined('DEBUG_DBLOG')) {
				$st=array_reverse(debug_backtrace());
				$sp=array();
				foreach ($st as $s) $sp[]=$s['file'].':'.$s['line'];
				$ls=date('Y-m-d H:i:s  ');
				$ls.=str_pad(number_format($te-$ts,3),8,' ',STR_PAD_LEFT).' s   ';
				$ls.='"'.$query.'" - ';
				$ls.='"'.$_SERVER['REQUEST_URI'].'" - ';
				$ls.='"'.implode(', ',$sp).'"';
				$ls.="\n";
				//echo $ls;
				file_put_contents(DEBUG_DBLOG,$ls,FILE_APPEND | LOCK_EX);
			}
			
			$r=0;
			$rread;
			
			do{
				if (!is_null($onread) && is_array($onread) && isset($onread[$r])){
					$rread=&$onread[$r];
					if (is_array($rread)) {
						$rread=$q->fetchAll(PDO::FETCH_ASSOC);
					} elseif (is_callable($rread)) {
						//var_dump($rread);
						while ($row=$q->fetch(PDO::FETCH_ASSOC)) {
							$rread($row);
						}
					}
				}
				$r++;
			} while ($q->nextRowset());
			if (!is_null($onend) && is_callable($onend)){
				$onend($q);
			}
			
			$q->closeCursor();
			
			if (!is_null($onread) && is_array($onread)){
				return $onread;
			} else {
				return true;
			}
		}

		public static function MExec($query,$param=null,$onread=null,$onend=null,$onerror=null) {
			$ln=static::getLink();
			return static::LnMExec($ln,$query,$param,$onread,$onend,$onerror);
		}		
		
		public static function ExecSingleRow($query,$param=null,$onerror=null) {
			$ret=static::Exec($query,$param,true,null,$onerror);
			if (is_null($ret)) {
				return null;
			} else {
				if (count($ret)>0) {
					return $ret[0];
				} else {
					return false;
				}
			}
		}
		
		public static function ExecSingleVal($query,$param=null,$onerror=null) {
			$ret=static::Exec($query,$param,true,null,$onerror);
			if (is_null($ret)) {
				return null;
			} else {
				if (count($ret)>0) {
					$row=array_values($ret[0]);
					if (count($row)>0) return $row[0];
					else return false;
				} else {
					return false;
				}
			}
		}
	}
?>