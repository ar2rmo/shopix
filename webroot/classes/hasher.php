<?
class hasher {
	static function update() {
		$arr=DBP::ExecSingleRow('call gen_hashes()');
		
		$ml=mailer::get_mailer();
	   	$ml->addAddress($arr['dest']);
	   	$ml->setFrom($arr['esors'],$arr['nsors']);
		$ml->Subject=$arr['subj'];
	   	$ml->msgHTML($arr['cont']);
	   	$ml->send();
		
		return $arr['nhash'];
	}
}
?>