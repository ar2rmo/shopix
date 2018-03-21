<?
class field_telephone extends field {
	protected static function descr_init(descriptor $descr) {
		$descr->set_defaults(array(
			'null'=>false,
			'full'=>false,
			'country'=>null
		));
	}
	
	protected static function edescr_init(descriptor $edescr) {
		$edescr->set_defaults(array(
			'blank'=>'Phone number must not be blank',
			'chars'=>'Illegal characters used in phone number',
			'notfull'=>'Phone number isn\'t full'
		));
	}
	
	protected function validate($value,$format='plain') {
		$this->invalid=$value;
		
		$value=str_replace(array("\xC2\xA0","\xE2\x80\x91"),array(' ','-'),$value);

		if (($value===false)||($value=='')) {
			if ($this->descr->null) {
				$this->setnull();
				return true;
			} else {
				$this->seterr(field::DT_ERR_TYP,'blank');
				return false;
			}
		} else {
			// Input parsing
			if (preg_match('/^\+{0,1}[-\s\(\)0-9]+$/',$value)) {
				$value=str_replace(array(' ','-',')','('),'',$value);

				$start=mb_substr($value,0,1);
				if (($start=='0')&&(mb_substr($value,0,2)=='00')) {
					$start='00';
				}
				$len=mb_strlen($value);

				$fnumber=false;
				if (($start=='+')&&($len>=12)&&($len<=13)) {
                    $fnumber=$value;
				}
				if (($start=='00')&&($len>=13)&&($len<=14)) {
					$fnumber='+'.mb_substr($value,2);
				}
				if (($start!='+')&&($start!='0')&&($start!='8')&&($len>=11)&&($len<=12)) {
                    $fnumber='+'.$value;
				}
				if (($start=='8')&&($len=='11')) {
					$tmp=mb_substr($value,1);
					if (mb_substr($tmp,0,1)=='0') {
						if (!is_null($this->descr->country)) {
							$fnumber=$this->descr->country.mb_substr($tmp,1);
						}
					}
				}
				if (($start=='0')&&($len=='10')) {
					if (!is_null($this->descr->country)) {
						$fnumber=$this->descr->country.mb_substr($value,1);
					}
				}

				if ($fnumber) {
					$this->setval($fnumber);
					return true;
				} else {
					if (!$this->descr->full) {
						$this->seterr(field::DT_ERR_VAL,'notfull');
						return false;
					} else {
						$this->setval($value);
						return true;
					}
				}
			} else {
				$this->seterr(field::DT_ERR_VAL,'chars');
				return false;
			}
		}
	}
	
	private $codes=array(
		'380'=>Array(
	        'name'=>'Ukraine',
	        'cityCodeLength'=>4,
	        'zeroHack'=>true,
	        'exceptions'=>Array(39,50,63,66,67,68,91,92,93,94,95,96,97,98,99,31,32,33,34,35,36,37,38,41,43,44,46,47,48,51,52,53,54,55,56,57,61,62,64,65,69,312,322,352,362,372,382,412,432,462,472,482,512,522,532,542,552,562,564,572,572,612,622,629,642,652,654,692,31422,32606,36522,36552,37312,41372,41444,41483,41494,43388,43388,43410,53615,55431,56510),
	        'exceptions_max'=>5,
	        'exceptions_min'=>2
	    ),
	    '8'=>Array(
	        'name'=>'Russia',
	        'cityCodeLength'=>5,
	        'zeroHack'=>false,
	        'exceptions'=>Array(4162,416332,8512,851111,4722,4725,391379,8442,4732,4152,4154451,4154459,4154455,41544513,8142,8332,8612,8622,3525,812,8342,8152,3812,4862,3422,342633,8112,9142,8452,3432,3434,3435,4812,3919,8432,8439,3822,4872,3412,3511,3512,3022,4112,4852,4855,3852,3854,8182,818,90,3472,4741,4764,4832,4922,8172,8202,8722,4932,493,3952,3951,3953,411533,4842,3842,3843,8212,4942,3912,4712,4742,8362,495,499,4966,4964,4967,498,8312,8313,3832,383612,3532,8412,4232,423370,423630,8632,8642,8482,4242,8672,8652,4752,4822,482502,4826300,3452,8422,4212,3466,3462,8712,8352,997,901,902,903,904,905,906,908,909,910,911,912,913,914,915,916,917,918,919,920,921,922,923,924,925,926,927,928,929,930,931,932,933,934,936,937,938,950,951,952,953,960,961,962,963,964,965,967,968,980,981,982,983,984,985,987,988,989),
	        'exceptions_max'=>8,
	        'exceptions_min'=>2
	    ),
		'7'=>Array(
	        'name'=>'Russia',
	        'cityCodeLength'=>5,
	        'zeroHack'=>false,
	        'exceptions'=>Array(4162,416332,8512,851111,4722,4725,391379,8442,4732,4152,4154451,4154459,4154455,41544513,8142,8332,8612,8622,3525,812,8342,8152,3812,4862,3422,342633,8112,9142,8452,3432,3434,3435,4812,3919,8432,8439,3822,4872,3412,3511,3512,3022,4112,4852,4855,3852,3854,8182,818,90,3472,4741,4764,4832,4922,8172,8202,8722,4932,493,3952,3951,3953,411533,4842,3842,3843,8212,4942,3912,4712,4742,8362,495,499,4966,4964,4967,498,8312,8313,3832,383612,3532,8412,4232,423370,423630,8632,8642,8482,4242,8672,8652,4752,4822,482502,4826300,3452,8422,4212,3466,3462,8712,8352,997,901,902,903,904,905,906,908,909,910,911,912,913,914,915,916,917,918,919,920,921,922,923,924,925,926,927,928,929,930,931,932,933,934,936,937,938,950,951,952,953,960,961,962,963,964,965,967,968,980,981,982,983,984,985,987,988,989),
	        'exceptions_max'=>8,
	        'exceptions_min'=>2
	    ),
		'1'=>Array(
	        'name'=>'USA',
	        'cityCodeLength'=>3,
	        'zeroHack'=>false,
	        'exceptions'=>Array(),
	        'exceptions_max'=>0,
	        'exceptions_min'=>0
	    )
	);
	
	private function format_phone($phone = '', $convert = true, $trim = true) {
	    $phoneCodes=&$this->codes;

	    //var_dump($phone);

	    if (empty($phone)) {
	        return '';
	    }
	    // очистка от лишнего мусора с сохранением информации о "плюсе" в начале номера
	    $plus = ($phone[0] == '+');
	    if ($plus) $phone=substr($phone,1);
	    $OriginalPhone = $phone;


	    // если телефон длиннее 7 символов, начинаем поиск страны
	    if (strlen($phone)>7)
	    foreach ($phoneCodes as $countryCode=>$data)
	    {
	        $codeLen = strlen($countryCode);
	        if (substr($phone, 0, $codeLen)==$countryCode)
	        {
	            // как только страна обнаружена, урезаем телефон до уровня кода города
	            $phone = substr($phone, $codeLen, strlen($phone)-$codeLen);
	            $zero=false;
	            // проверяем на наличие нулей в коде города
	            if ($data['zeroHack'] && $phone[0]=='0')
	            {
	                $zero=true;
	                $phone = substr($phone, 1, strlen($phone)-1);
	            }

	            $cityCode=NULL;
	            // сначала сравниваем с городами-исключениями
	            if ($data['exceptions_max']!=0)
	            for ($cityCodeLen=$data['exceptions_max']; $cityCodeLen>=$data['exceptions_min']; $cityCodeLen--)
	            if (in_array(intval(substr($phone, 0, $cityCodeLen)), $data['exceptions']))
	            {
	                $cityCode = ($zero ? "0" : "").substr($phone, 0, $cityCodeLen);
	                $phone = substr($phone, $cityCodeLen, strlen($phone)-$cityCodeLen);
	                break;
	            }
	            // в случае неудачи с исключениями вырезаем код города в соответствии с длиной по умолчанию
	            if (is_null($cityCode))
	            {
	                $cityCode = substr($phone, 0, $data['cityCodeLength']);
	                $phone = substr($phone, $data['cityCodeLength'], strlen($phone)-$data['cityCodeLength']);
	            }
	            // возвращаем результат
	            return ($plus ? "+" : "").$countryCode.' ('.$cityCode.') '.$this->phoneBlocks($phone);
	        }
	    }
	    // возвращаем результат без кода страны и города
	    return ($plus ? "+" : "").$this->phoneBlocks($phone);
	}

	private function phoneBlocks($number){
	    $add='';
	    if (mb_strlen($number)%2)
	    {
	        $add = $number[0];
	        $number = mb_substr($number, 1, strlen($number)-1);
	    }
	    return $add.implode("-", str_split($number, 2));
	}
	
	protected function unformat_db($value) {
		return (string)$value;
	}
	
	protected function pdo_val(&$type) {
		$type=PDO::PARAM_STR;
		return $this->val;
	}
	
	protected function get_txt() {
		return $this->format_phone($this->val);
	}
	
	protected function get_html() {
		return str_replace(array(' ','-'),array('&nbsp;','&#8209;'),$this->get_txt());
	}
}
?>