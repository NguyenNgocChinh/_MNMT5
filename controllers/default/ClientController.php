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
	
	function order(){
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		$data[] = array();
		$num = 0;
		if(isset($_GET['num'])){$num = $_GET['num'];$_SESSION['num'] = $num;}
		$title = 0;
		
		if(isset($_SESSION['cart'])){
			for($i = 0; $i < count($_SESSION['cart']); $i++){
				$row = $md->getPrdById($_SESSION['cart'][$i]);
				$row['num'] = $num[$i];
				$data[] = $row;
				$pr = intval(preg_replace('/\s+/', '', $row['gia']));
				$title += $num[$i]*$pr;
			}
		}
		array_shift($data);
		$this->render('order', $data, $title);
	}
	function orderComplete(){
		$ten = $sdt = $quan = $dc = $type = ""; $tt = 0;
		$num = $sp = [];

		if(isset($_POST['ten'])){$ten = $_POST['ten'];}
		if(isset($_POST['sodt'])){$sdt = $_POST['sodt'];}
		if(isset($_POST['quan'])){$quan = $_POST['quan'];}
		if(isset($_POST['dc'])){$dc = $_POST['dc'];}
		if(isset($_POST['sp'])){$sp = $_POST['sp'];}
		if(isset($_POST['num'])){$num = $_POST['num'];}
		if(isset($_POST['type'])){$type = $_POST['type'];}
		/*$now = date("Y-m-d h:i:s");*/
		$now = new DateTime(null, new DateTimeZone('ASIA/Ho_Chi_Minh'));
		$now = $now->format('Y-m-d H:i:s');
		require_once 'vendor/Model.php';
		require_once 'models/default/productModel.php';
		$md = new productModel;
		for ($i=0; $i < count($sp); $i++) {
			$row = $md->getPrdById($sp[$i]);
			$tt += $num[$i]*intval(preg_replace('/\s+/', '', $row['gia']));
		}
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$sql = "INSERT INTO giaodich(magd,tinhtrang,user_id,user_name,user_dst,user_addr,user_phone,tongtien,date) VALUES ('',0,'','".$ten."','".$quan."','".$dc."','".$sdt."','".$tt."','".$now."')";
		$rs = $md->exe_query($sql);
		if($rs){
			$last_id = $md->getLastInsertID();
			for ($i=0; $i < count($sp); $i++){
				$data = array($last_id, $sp[$i], $num[$i]);
				$md->insert('chitietgd',$data);
			}
			echo "OK";
			$this->render('orderComplete');
		}
		if(!($type == "buynow")){
			$_SESSION['cart'] = null;
			if(isset($_SESSION['user'])){
				$md->delete('giohang',"user_id = '".$_SESSION['user']['id']."'");
			}
		}
		$this->render('orderComplete');
	}
}