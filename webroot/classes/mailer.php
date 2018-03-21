<?
require_once LIBRARIES_PATH.'3rdparty/PHPMailer/PHPMailerAutoload.php';
require_once CLASSES_PATH.'settings.php';

class mailer {
	public static function get_mailer() {
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		
		return $mail;
	}
	
	protected static function send($ml) {
		$ml->send();
	}
	
	public static function feedback(feedback $fbk) {
		$urlprefix='http://'.$_SERVER['HTTP_HOST'];
		$setts=setts::get_obj();

		$ml=static::get_mailer();

		$subj='*** Сообщение с сайта '.$setts->tx_inf_shopname;
       	$cont=$fbk->ht_message;

       	$ml->Sender=$setts->tx_ord_mail;
		$ml->setFrom($fbk->tx_email,$fbk->tx_name);
       	$ml->addAddress($setts->tx_ord_mail,'Администратор');
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
		
		static::send($ml);
	}
	
	public static function order_admin(order $order) {
		$urlprefix='http://'.$_SERVER['HTTP_HOST'];
		$setts=setts::get_obj();
		
		$ml=static::get_mailer();

		$subj='Заказ № '.$order->tx_num.' - '.$setts->tx_inf_shopname;

       	$tpl=new template('mail/order_admin');
       	$tpl->set('order',$order);
       	$tpl->set('shopname',$setts->ht_inf_shopname);
		$tpl->set('shopurl',$setts->ht_inf_shopurl);
       	$cont=$tpl->execute();

       	$ml->Sender=$setts->tx_ord_mail;
		$ml->setFrom('no-reply@'.str_replace('www.','',$setts->tx_inf_shopurl),$setts->tx_inf_shopname);
       	$ml->addAddress($setts->tx_ord_mail,'Администратор');
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
       	
		static::send($ml);
	}
	
	public static function order_supervisor(order $order,$email) {
		$urlprefix='http://'.$_SERVER['HTTP_HOST'];
		$setts=setts::get_obj();
		
		$ml=static::get_mailer();

		$subj='Заказ № '.$order->tx_num.' - '.$setts->tx_inf_shopname;

       	$tpl=new template('mail/order_admin');
       	$tpl->set('order',$order);
       	$tpl->set('shopname',$setts->ht_inf_shopname);
		$tpl->set('shopurl',$setts->ht_inf_shopurl);
       	$cont=$tpl->execute();

       	$ml->Sender=$setts->tx_ord_mail;
		$ml->setFrom('no-reply@'.str_replace('www.','',$setts->tx_inf_shopurl),$setts->tx_inf_shopname);
       	$ml->addAddress($email,'Supervisor');
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
       	
		static::send($ml);
	}

	public static function order_customer(order $order) {
		$urlprefix='http://'.$_SERVER['HTTP_HOST'];
		$setts=setts::get_obj();
		
		$ml=static::get_mailer();

		$subj='Заказ № '.$order->tx_num.' - '.$setts->tx_inf_shopname;

       	$ml->Sender=$setts->tx_ord_mail;
		$tpl=new template('mail/order_customer');
       	$tpl->set('order',$order);
       	$tpl->set('shopname',$setts->ht_inf_shopname);
		$tpl->set('shopurl',$setts->ht_inf_shopurl);
       	$cont=$tpl->execute();

       	//$ml->set_from('no-reply@'.str_replace('www.','',$this->get_setting('shopurl')),$this->get_setting('shopname'));
       	$ml->setFrom($setts->tx_ord_mail,$setts->tx_inf_shopname);
       	$ml->addAddress($order->tx_c_email,$order->tx_c_name);
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
       	
		static::send($ml);
	}
	
	public static function subscribed(subscribe $subs) {
		$urlprefix='http://'.$_SERVER['HTTP_HOST'];
		$setts=setts::get_obj();
		
		$ml=static::get_mailer();

		$subj='Вы подписались на рассылку '.$setts->tx_inf_shopname;

       	$tpl=new template('mail/subscribe');
       	$tpl->set('order',$order);
       	$tpl->set('shopname',$setts->ht_inf_shopname);
		$tpl->set('shopurl',$setts->ht_inf_shopurl);
       	$cont=$tpl->execute();

       	$ml->Sender=$setts->tx_ord_mail;
		//$ml->set_from('no-reply@'.str_replace('www.','',$this->get_setting('shopurl')),$this->get_setting('shopname'));
       	$ml->setFrom($setts->tx_ord_mail,$setts->tx_inf_shopname);
       	$ml->addAddress($order->tx_c_email,$order->tx_c_name);
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
       	
		static::send($ml);
	}
	
	public static function broadcast(subscriber $subsc, col_article $articles,col_article $news,col_article $specials) {
		$setts=setts::get_obj();
		
		$ml=static::get_mailer();

		$subj='Рассылка - '.$setts->tx_inf_shopname;

       	$tpl=new template('mail/broadcast');
       	$tpl->baseurl=$setts->tx_inf_shopurl;
		$tpl->shopname=$setts->tx_inf_shopname;
		$tpl->unsubscr='http://'.$setts->tx_inf_shopurl.'/subscribe?unsubscr='.$subsc->tx_email.'&code='.$subsc->tx_unsuc;
		$tpl->articles=$articles;
		$tpl->news=$news;
		$tpl->specials=$specials;
       	$cont=$tpl->execute();

       	$ml->Sender=$setts->tx_ord_mail;
		$ml->setFrom($setts->tx_ord_mail,$setts->tx_inf_shopname);
       	$ml->addAddress($subsc->tx_email);
       	$ml->Subject=$subj;
       	$ml->msgHTML($cont);
       	
		static::send($ml);
	}
}
?>