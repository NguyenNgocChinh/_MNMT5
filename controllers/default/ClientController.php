<?php 

/**
* 
*/
class ClientController extends Controller
{
	
	function __construct()
	{
		$this->folder = "default";
	}
	function viewcart(){
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		$data[] = array();
		
		if(isset($_SESSION['cart'])){
			$title = "Giỏ hàng của bạn:";
			for($i = 0; $i < count($_SESSION['cart']); $i++){
				$data[] = $md->getPrdById($_SESSION['cart'][$i]);
			}
		} else {
			$title = "<span class='glyphicon glyphicon-alert' style='color: #c1ac13'></span> Giỏ hàng của bạn trống!";
		}

		$this->render('cart', $data ,$title);
	}

	function addtocart(){
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		$masp = array();
		if(isset($_POST['masp'])){$masp = $_POST['masp'];}

		if(isset($_SESSION['cart'])){
			$position = array_search($md->getPrdById($masp)['masp'], $_SESSION['cart']);
			if($position !== false){
				array_splice($_SESSION['cart'], $position,1);
				if(isset($_SESSION['user'])){
					$sql = "DELETE FROM giohang WHERE user_id = ".$_SESSION['user']['id']." AND masp = ".$masp;
					$md->exe_query($sql);
				}
			} else {
				if(isset($_SESSION['user'])){
					$sql = "INSERT INTO giohang VALUES(".$_SESSION['user']['id'].",".$masp.",1)";
					$md->exe_query($sql);
				}
				$_SESSION['cart'][] = $md->getPrdById($masp)['masp'];
			}
			echo " ".count($_SESSION['cart']);
		} else {
			$_SESSION['cart'][] = $md->getPrdById($masp)['masp'];
			echo " ".count($_SESSION['cart']);
		}
	}
	function delPrd(){
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		$masp = '';
		if(isset($_SESSION['cart'])){$masp = $_SESSION['cart'];}
		$dlt = array_splice($_SESSION['cart'], array_search($masp, $_SESSION['cart']), 1);
		if(isset($_SESSION['user'])){
			$sql = "DELETE FROM giohang WHERE user_id = ".$_SESSION['user']['id']." AND masp = ".$dlt[0];
			$md->exe_query($sql);
		}
		echo " ".count($_SESSION['cart']);
	}
}