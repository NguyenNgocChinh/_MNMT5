<?php

/**
* 
*/
class UserController extends Controller
{
	
	function __construct()
	{
		$this->folder = "users";
	}
	function index(){
		echo "Trang khong ton tai";
	}
	
	function register(){
		//xu ly dang ky
		require_once 'vendor/Model.php';
		require_once 'models/users/userModel.php';
		$md = new userModel;

		if(isset($_POST['name'])){
			$name = $_POST['name'];
		}
		if(isset($_POST['username'])){
			$username = $_POST['username'];
		} else {
			echo "Trang khong ton tai!";
			return 0;
		}
		if(isset($_POST['password'])){
			$password = $_POST['password'];
		}
		if(isset($_POST['cpassword'])){
			$cpassword = $_POST['cpassword'];
		}
		if(isset($_POST['addr'])){
			$addr = $_POST['addr'];
		}
		if(isset($_POST['tel'])){
			$phone = $_POST['tel'];
		}
		if(isset($_POST['email'])){
			$email = $_POST['email'];
		}


		if($username == ""){
			echo "Tên tài khoản không được để trống!";
			return false;
		}
		if($md->getUserByUsername($username)){
			echo "Tên tài khoản đã tồn tại!";
			return false;
		} else {
			if($password != $cpassword){
				echo "Nhập lại mật khẩu sai!";
				return false;
			}
			if($md->addUser($name, $username, $password, $addr, $phone, $email)){ 
				echo "RegisterSuccess";
				$_SESSION['user'] = $md->getUserByUsername($username);
				$userCart = array(); $sql = '';
				if(isset($_SESSION['cart'])){
					$sql = "SELECT masp FROM giohang WHERE user_id = ".$_SESSION['user']['id'];
					$userCart = $md->getListMasp($sql);
					$addData = array();
					for($j = 0; $j < count($_SESSION['cart']); $j++){
						$pos = false;
						if($userCart != ''){
							$pos = array_search($_SESSION['cart'][$j], $userCart);
						}
						if($pos === false){
							$addData[] = $_SESSION['cart'][$j];
						}
					}
					$sql = "";
					for ($i=0; $i < count($addData); $i++) { 
						$sql .= "INSERT INTO giohang VALUES (".$_SESSION['user']['id'].", ".$addData[$i].");\n";	
					}
					$md->exe_query($sql);
				}
				return true;
			} else {
				echo "Đã có lỗi trong quá trình tạo tài khoản, vui lòng thử lại sau!";
				return false;
			}
		}
	}
	
	
}
