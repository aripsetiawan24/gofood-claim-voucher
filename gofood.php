<?php
// https://github.com/arcode13/gofood-claim-voucher

include ("function.php");

function nama()
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ex = curl_exec($ch);
	// $rand = json_decode($rnd_get, true);
	preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
	return $name[2][mt_rand(0, 14) ];
	}
function register($no)
	{
	$nama = nama();
	$email = str_replace(" ", "", $nama) . mt_rand(100, 999);
	$data = '{"name":"' . nama() . '","email":"' . $email . '@gmail.com","phone":"+' . $no . '","signed_up_country":"ID"}';
	$register = request("/v5/customers", "", $data);
	//print_r($register);
	if ($register['success'] == 1)
		{
		return $register['data']['otp_token'];
		}
	  else
		{
		return false;
		}
	}
function verif($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $token . '"},"client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e"}';
	$verif = request("/v5/customers/phone/verify", "", $data);
	if ($verif['success'] == 1)
		{
		return $verif['data']['access_token'];
		}
	  else
		{
		return false;
		}
	}
	function login($no)
	{
	$nama = nama();
	$email = str_replace(" ", "", $nama) . mt_rand(100, 999);
	$data = '{"phone":"+'.$no.'"}';
	$register = request("/v4/customers/login_with_phone", "", $data);
	print_r($register);
	if ($register['success'] == 1)
		{
		return $register['data']['login_token'];
		}
	  else
		{
		return false;
		}
	}
function veriflogin($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e","data":{"otp":"'.$otp.'","otp_token":"'.$token.'"},"grant_type":"otp","scopes":"gojek:customer:transaction gojek:customer:readonly"}';
	$verif = request("/v4/customers/login/verify", "", $data);
	if ($verif['success'] == 1)
		{
		return $verif['data']['access_token'];
		}
	  else
		{
		return false;
		}
	}
function claim($token)
	{
	$data = '{"promo_code":"GOFOODBOBA07"}';
	$claim = request("/go-promotions/v1/promotions/enrollments", $token, $data);
	if ($claim['success'] == 1)
		{
		return $claim['data']['message'];
		}
	  else
		{
		return false;
		}
	}
echo "Thanks To Muhamad Syahrul Minanul Aziz\n";
echo "\n";
echo "Silahkan Pilih Salah Satu Dibawah Ini :\n";
echo "1. Masuk\n";
echo "2. Daftar\n";
echo "Pilih Nomor: ";
$type = trim(fgets(STDIN));
if($type == 2){
echo "Cara Mendaftar Akun Nya :\n";
echo "Jika Nomor INDO Masukan Nomor HP Awalan 62 & Nomor US Masukan Awalan 1\n";
echo "Contoh: - Nomor INDO 628123456789\n";
echo "        - Nomor US 13377352197\n";
echo "Pastikan Nomor HP Anda Belum Terdaftar Di GOJEK\n";
echo "Masukan Nomor Anda: ";
$nope = trim(fgets(STDIN));
$register = register($nope);
if ($register == false)
	{
	echo "Gagal Mendapatkan Kode OTP, Gunakan Nomor Yang Belum Terdaftar!\n";
	}
  else
	{
	echo "Masukan Kode OTP Anda: ";
	// echo "Enter Number: ";
	$otp = trim(fgets(STDIN));
	$verif = verif($otp, $register);
	if ($verif == false)
		{
		echo "Kode OTP Salah, Silahkan Dicoba Kembali!\n";
		}
	  else
		{
		echo "Mencoba Untuk Mendapatkan Voucher GOFOOD\n";
		$claim = claim($verif);
		if ($claim == false)
			{
			echo "Gagal Mendapatkan Voucher, Silahkan Dicoba Kembali!\n";
			}
		  else
			{
			echo $claim . "\n";
			}
		}
	}
}else if($type == 1){
echo "Cara Masuk Akun Nya :\n";
echo "Jika Nomor INDO Masukan Nomor HP Awalan 62 & Nomor US Masukan Awalan 1\n";
echo "Contoh: - Nomor INDO 628123456789\n";
echo "        - Nomor US 13377352197\n";
echo "Masukan Nomor Anda: ";
$nope = trim(fgets(STDIN));
$login = login($nope);
if ($login == false)
	{
	echo "Gagal Mendapatkan Kode OTP, Silahkan Di Coba Kembali!\n";
	}
  else
	{
	echo "Masukan Kode OTP Anda: ";
	// echo "Enter Number: ";
	$otp = trim(fgets(STDIN));
	$verif = veriflogin($otp, $login);
	if ($verif == false)
		{
		echo "Kode OTP Salah, Silahkan Dicoba Kembali!!\n";
		}
	  else
		{
		echo "Mencoba Untuk Mendapatkan Voucher GOFOOD\n";
		$claim = claim($verif);
		if ($claim == false)
			{
			echo "Gagal Mendapatkan Voucher, Silahkan Dicoba Kembali!\n";
			}
		  else
			{
			echo $claim . "\n";
			}
		}
	}	
}
?>
