<?php 

/**
* 
*/
class ProductController extends Controller
{
	
	function __construct()
	{
		$this->folder = "default";
	}
	
	
	function PrdDetail($masp){
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		$data = $md->getPrdById($masp);
		$title = $data['tensp'];
		require_once 'views/default/ProductDetail.php';
	}
}