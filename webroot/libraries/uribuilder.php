<?

abstract class uribuilder_base {
	protected function root() {
		return '';
	}
	
	public abstract function bo($object);
	public abstract function act($name, $arg);
	public abstract function anch($anchor);
	
	public function __invoke($obj) {
		return $this->bo($obj);
	}
	
	public function __call($name, $args) {
		return $this->act($name, $args[0]);
	}
	
	public function __get($name) {
		return $this->anch($name);
	}
}

?>