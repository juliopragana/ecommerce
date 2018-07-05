<?php 

namespace Hcode;
use Rain\Tpl;

class Page {

	private $tpl;
	private $opitions = [];
	private $defaults = [
		"data"=>[]
	];

	//método para o header
	public function __construct($opts = array()){

		$this->opitions = array_merge($this->defaults, $opts);

		$config = array(
					"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
					"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
					"debug"         => false // set to false to improve the speed
				   );

		Tpl::configure( $config );

		$this->tpl = new Tpl;

		$this->setData($this->opitions["data"]);

		$this->tpl->draw("header");

	}

	private function setData($data = array())
	{
		foreach ($data as $key => $value) {
			# code...
			$this->tpl->assign($key, $value);
		}
	}

	public function setTpl($name, $data = array(), $returnHTML =false)
	{
		$this->setData($data);

	return $this->tpl->draw($name, $returnHTML);

	}



	//método para o footer
	public function __destruct(){
		$this->tpl->draw("footer");
	}

}


 ?>