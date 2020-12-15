<?php 
/**
* 
*/
class IndexController extends Controller
{
	function __construct(){
		$this->folder = "default";
	}
	
	function index()
	{

		$data = "Hello";
		$this->render('index', $data);
	}

}
?>