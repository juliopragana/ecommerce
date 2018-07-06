<?php 

namespace Hcode;

class PageAdmin extends Page{
	
	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
	{
		//usando o método construtor da classe pai
		parent::__construct($opts, $tpl_dir);

	}

}



 ?>