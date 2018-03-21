<?
require_once LIBRARIES_PATH.'reader_csv.php';
require_once LIBRARIES_PATH.'reader_excel.php';

class Import
{
	public static function StoreSession($Data) {
		$_SESSION['import_session']=$Data;
	}
	
	public static function RestoreSession() {
		if (static::IsStoredSession()) {
			return $_SESSION['import_session'];
		}
	}
	
	public static function IsStoredSession() {
		return (isset($_SESSION['import_session']));
	}
	
	public static function ClearStoredSession() {
		if (isset($_SESSION['import_session']))
			unset($_SESSION['import_session']);
	}
}

class ImportBase
{
	protected $Method = null;
	protected $File = null;
	protected $State = null;
	
	protected $Offset = 0;
	protected $StepTime = 0;
	
	protected $ProgressAbs = 0;
	protected $ProgressRelState = 0;
	protected $ProgressRelFull = 0;
	
	protected $Data = array();
	
	public function __construct($File = null, $Method = null, $State = 'IDLE')
	{
		$this->SetFile($File);
		$this->SetMethod($Method);
		$this->SetState($State);
	}
	
	public function SetFile($File)
	{
		$this->File=$File;
	}
	
	public function SetMethod($Method)
	{
		$this->Method=$Method;
	}
	
	public function SetState($State)
	{
		$this->State=$State;
	}
	
	public function GetSessionData()
	{
		return array(
			'file'   => $this->File,
			'method' => $this->Method,
			'state'  => $this->State,
			
			'offset' => $this->Offset
		);
	}
	
	public function SetSessionData($arr)
	{
		$this->SetFile($arr['file']);
		$this->SetMethod($arr['method']);
		$this->SetState($arr['state']);
		
		$this->Offset = $arr['offset'];
	}
	
	public function GetLastStepTime()
	{
		return $this->StepTime;
	}
	
	public function Reset()
	{
		$this->SetState('INIT');
	}
	
	public function ExecuteStep()
	{
		if ($this->IsStopped()) {
			trigger_error("Import is in not running state and cannot be runned", E_USER_WARNING);
		}
		
		$cname='StateHandler_'.$this->State;
		if (method_exists($this, $cname)) {
			$st=microtime(true);
			$this->State = $this->$cname();
			$et=microtime(true);
			$this->StepTime = $et - $st;
			return;
		}
		$this->State = 'FAIL';
		trigger_error("Import went into a state with no handlers defined", E_USER_WARNING);
	}
	
	public function IsFailed() {
		return ($this->State == 'FAIL');
	}

	public function IsDone() {
		return ($this->State == 'DONE');
	}
	
	public function IsIdle() {
		return ($this->State == 'IDLE');
	}
	
	public function IsStopped() {
		return (($this->State == 'IDLE') || ($this->State == 'DONE') || ($this->State == 'FAIL'));
	}
	
	public function IsStoredSession()
	{
		return Import::IsStoredSession();
	}
	
	public function StoreSession() {
		Import::StoreSession($this->GetSessionData());
	}
	
	public function RestoreSession() {
		if (Import::IsStoredSession()) {
			$this->SetSessionData(Import::RestoreSession());
		}
	}
}

abstract class ImportExcel extends ImportBase
{
	protected $Fields;
	
	protected $rdr = null;
	
	protected $partial = true;
	protected $num = 100;
	
	protected $imp = null;
	protected $n = null;
	
	public function __construct($Fields, $File = null, $Method = null, $State = 'IDLE')
	{
		$this->Fields = $Fields;
		parent::__construct($File, $Method, $State);
	}
	
	public function SetFile($File)
	{
		parent::SetFile($File);
		$this->rdr = new xl_reader($this->File, $this->Fields);
		//$this->rdr = new xl_reader2($this->File, $this->Fields);
	}
	
	protected function StateHandler_INIT()
	{
		$this->Init();
		return 'READ';
	}
	
	protected function StateHandler_READ()
	{
		$this->rdr->pos_to($this->Offset);
		
		$rs = $this->rdr->get_recordset();
		foreach ($rs as $tpid => $record) {
			$this->RowProcess($tpid, $record);
		}
		
		$next = $this->rdr->pos_next();
		if (is_null($next)) {
			return 'READ_DONE';
		} else {
			$this->Offset = $next;
			return 'READ';
		}
		
		/*$tpid=0;
		$this->rdr->read(function ($r) use (&$tpid) { $tpid++; $this->RowProcess($tpid, $r);});
		return 'READ_DONE';*/
	}
	
	abstract protected function Init();	
	abstract protected function RowProcess($tpid, &$data);
}
?>