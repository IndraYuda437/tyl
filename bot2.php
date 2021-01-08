<?php


set_time_limit(0);
error_reporting(0);

function curl($url, $headers, $mode="get", $data=0)
	{
	if ($mode == "get" || $mode == "Get" || $mode == "GET")
		{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		}
	elseif ($mode == "post" || $mode == "Post" || $mode == "POST")
		{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		}
	else
		{
		$result = "Not define";
		}
	return $result;
	}


$headers[] = "User-Agent: android/freeltc";
$headers[] = "Content-Type: application/json; charset=utf-8";


		// Check
function check(){
if (!file_exists("passcode.txt")){
echo "Masukkan Passcode (untuk semua akun)(min 6 digit angka)	> ";
$passcode = trim(fgets(STDIN));
	if (is_numeric($passcode)){
		if (strlen($passcode) > 5 && strlen($passcode) < 7){
		}else{
		exit();
		}
	}else{
	exit();
	}
file_put_contents("passcode.txt", $passcode);
echo "Berhasil Menambahkan Passcode \r";
sleep(3);
}
echo "                                      \r";
}
check();



		// Login
function login($email, $headers, $stat=1){
check();
$passcode = file_get_contents("passcode.txt");
$url = "http://laurentiu.live:8067/api/user/auth";
$dataU = '{"email":"'.$email.'","passcode":"'.$passcode.'"}';
$a = curl($url, $headers, "Post", $dataU);
$a = json_decode($a, true);
if ($stat == 1){
$ballance = $a["balance"];
$email = $a["email"];
echo "Email		> {$email}\n";
echo "Ballance	> {$ballance}\n";
}
if ($a["token"]){
return $a["token"];
}
}


		// Signup

function signup($headers, $email=null){
check();
$passcode = file_get_contents("passcode.txt");

foreach ($headers as $hed){
$header[] = $hed;
}
	// Email
if (!$email){
echo "Masukkan Email		> ";
$email = trim(fgets(STDIN));
echo "Checking email please wait\r";
$data = '{"email":"'.$email.'"}';
$a = json_decode(curl('http://laurentiu.live:8067/api/check/email', $headers, "post", $data), true);
if ($a["message"] == 'Do you want to create an account?'){
echo "                                                     \r";
echo "Done ✅ \r";
sleep(2);
}else{
signup($headers);
}
}
	// Wallet
echo "Masukkan wallet		> ";
$wallet = trim(fgets(STDIN));
echo "Checking wallet please wait\r";
$data = '{"wallet":"'.$wallet.'"}';
$a = json_decode(curl('http://laurentiu.live:8067/api/check/wallet', $headers, "post", $data), true);
if ($a["message"] == "Wallet not used"){
echo "                                          \r";
echo "Done ✅ \r";
sleep(2);
}else{
signup($headers, $email);
}
	// signup
$data = '{"email":"'.$email.'","passcode":"'.$passcode.'","wallet":"'.$wallet.'"}';
$a = json_decode(curl('http://laurentiu.live:8067/api/user/new', $headers, "post", $data), true);
if ($a["status"] == "ok"){
	// Set refferal
$header[] = "moke: ".login($email, $headers, "2");
$data = '{"email":"'.$email.'","passcode":"'.$passcode.'","refcode":"f(perc))HLB2am"}';
curl("http://laurentiu.live:8067/api/user/referral", $header, "post", $data);
echo "Akun berhasil dibuat\r";
sleep(2);
echo "                              \r";
}else{
signup($headers);
}
	// Add json
$em = json_decode(file_get_contents("email.json"), true);
$wal = json_decode(file_get_contents("wallet.json"), true);

foreach ($em as $eml){
$emaill[] = $eml;
}$emaill[] = $email;

foreach ($wal as $wall){
$walle[] = $wall;
}$walle[] = $wallet;

$email = json_encode($emaill, JSON_PRETTY_PRINT);
$wallet = json_encode($walle, JSON_PRETTY_PRINT);
file_put_contents("email.json", $email);
file_put_contents("wallet.json", $wallet);

echo "Ingin Menambah akun lagi (y/n) > ";
$pil = trim(fgets(STDIN));
if ($pil == "y"){
signup($headers);
}else{
claim($headers);
}
}


// Claim
function claim($headers, $akun=0){

$emailG = json_decode(file_get_contents('email.json'), true);
$walletG = json_decode(file_get_contents('wallet.json'), true);
$email = $emailG[$akun];
$wallet = $walletG[$akun];
check();
$passcode = file_get_contents("passcode.txt");

foreach ($headers as $hed){
$header[] = $hed;
}
$header[] = "moke: ".login($email, $headers, "2");

while (true){
$url = "http://laurentiu.live:8067/api/user/bonus";
$data = '{"email":"'.$email.'","passcode":"'.$passcode.'"}';
$a = curl($url, $header, "Post", $data);
$a = json_decode($a, true);
$message = $a['message'];
if ($message != "You are out of rolls"){
$id = $a["rollID"];
echo "{$message}\n";
}else{
$url = "http://laurentiu.live:8067/api/user/get";
$dataU = '{"email":"'.$email.'","passcode":"'.$passcode.'","wallet":"'.$wallet.'"}';
$a = curl($url, $header, "Post", $dataU);
$dec = json_decode($a, true);
$akun = $akun+1;
echo "Update ballance acc[{$akun}]	> {$dec['balance']}\n";
if ($akun >= count($emailG)){
$akun = 0;
sleep(65);
}
claim($headers, $akun);
}

// ads
$url = "http://laurentiu.live:8067/api/user/adv";
$data = '{"email":"'.$email.'","passcode":"'.$passcode.'"}';
$head = array();
foreach ($header as $headerr){
$head[] = $headerr;
}
$head[] = "type: bonus";
$head[] = "adv: {$id}";
curl($url, $head, "Post", $data);
// sv
$url = "http://laurentiu.live:8067/api/user/sv";
curl($url, $head, "Post", $data);
// send bonus
$url = "http://laurentiu.live:8067/api/user/bonusend";
curl($url, $head, "Post", $data);
}
}




system("clear");
echo "		⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜♐VIP MENU♐⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜\n";
echo "	1. Register\n";
echo "	2. Claim\n";
echo "	Pilihan		> ";
$pilihan = trim(fgets(STDIN));

if ($pilihan == 1){
signup($headers);
}elseif ($pilihan == 2){
claim($headers);
}else{
exit();
}















?>
