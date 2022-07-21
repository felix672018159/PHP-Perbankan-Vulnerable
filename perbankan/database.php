<?php



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//K O N F I G U R A S I _ S E R V E R
require_once "config.php";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//kode program untuk koneksi database beserta fungsinya;
$serverini = $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'];

if(strpos($serverini,"/database.php") !==FALSE){
	echo "Tidak boleh melakukan akses secara langsung terhadap file database ini !";
	exit();
}


$mysqli = new mysqli("localhost",$username,$password, "vuln_server_perbankan_felix672018159");

if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}else{
	$cek_koneksi_database = 1;
	//echo "sukses masuk kedalam database slurs";
}
function set_session($user_session){
	setcookie("user_session", $user_session, time()+3600, "/");
}

function get_session(){
	if(!isset($_COOKIE["user_session"])){
		return false;
		//sesi tidak ditemukan
	}else{
		return $_COOKIE["user_session"];
	}
}

function delete_session(){
	if (isset($_COOKIE['user_session'])) {
    	unset($_COOKIE['user_session']); 
    	setcookie('user_session', null, -1, '/'); 
    	return true;
	} else {
    	return false;
	}
}

function check_session(){
	if(get_session()){
		$cipher = get_session();
		$data = get_user_by_session($cipher);
		if($data){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}


function enkripsi_caesar($konfigurasi_karakter,$kunci,$plaintext){
	$length_plain 		= strlen($plaintext);
	$length_konfigurasi = strlen($konfigurasi_karakter);
	//for($y=0; $y<26; $y++){
	$generate = "";
	for($i=0;$i<$length_plain;$i++){
		$abjad  = substr($plaintext,$i,1);
		//START DECRYPT------------------------------
		if($abjad==" "){
			$generate .= " ";
		}else{
			$posisi           = strpos($konfigurasi_karakter, $abjad);
			$rumusgeserkanan  = (($length_konfigurasi + $posisi) + $kunci) %$length_konfigurasi;
			$plainletter      = substr($konfigurasi_karakter,$rumusgeserkanan,1);
			$generate .= $plainletter;				
		}
	}
	return $generate;
}

function dekripsi_caesar($konfigurasi_karakter,$kunci,$ciphertext){
	$length_cipher 		= strlen($ciphertext);
	$length_konfigurasi = strlen($konfigurasi_karakter);
	//for($y=0; $y<26; $y++){
	$generate = "";
	for($i=0;$i<$length_cipher;$i++){
		$abjad  = substr($ciphertext,$i,1);
		//START DECRYPT------------------------------
		if($abjad==" "){
			$generate .= " ";
		}else{
			$posisi           = strpos($konfigurasi_karakter, $abjad);
			$rumusgeserkanan  = (($length_konfigurasi + $posisi) - $kunci) %$length_konfigurasi;
			$cipherletter      = substr($konfigurasi_karakter,$rumusgeserkanan,1);
			$generate .= $cipherletter;				
		}
	}
	return $generate;
	//	}
}
//////////

function delete_user($nama_user){
	global $mysqli;
	if(!cari_username($nama_user)){
		return "Username tidak ditemukan";
	}
	$stmt = $mysqli->prepare("DELETE FROM pengguna WHERE username=?");
	$stmt->bind_param("s",$nama_user);
	$stmt->execute();
	$stmt->close();
	return "Akun dengan username $nama_user dihapus";
}


function get_all_user(){
	global $mysqli;
	$stmt =  $mysqli->prepare("select * from pengguna;");
	$stmt->execute();
	$stmt->bind_result($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
	$data = array(array());
	$no = 0;
	while($stmt->fetch()){
		$data[$no] = array($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
		$no++;
		//echo "id               = ".$id;
		//echo "<br>username     = ".$username;
		//echo "<br>password     = ".$password;
		//echo "<br>nama lengkap = ".$namalengkap;
		//echo "<br>=================================";
	}
	$stmt->close();
	return $data;
}
function get_all_user_sort_saldo(){
	global $mysqli;
	$stmt =  $mysqli->prepare("select * from pengguna order by saldo desc;");
	$stmt->execute();
	$stmt->bind_result($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
	$data = array(array());
	$no = 0;
	while($stmt->fetch()){
		$data[$no] = array($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
		$no++;
		//echo "id               = ".$id;
		//echo "<br>username     = ".$username;
		//echo "<br>password     = ".$password;
		//echo "<br>nama lengkap = ".$namalengkap;
		//echo "<br>=================================";
	}
	$stmt->close();
	return $data;
}
function get_user_rekening_sort_saldo($rekening=""){
	global $mysqli;
	if(strlen($rekening)>6){
		$rekening = substr($rekening,6,(strlen($rekening)-6));//92774032
	}
	$sqlquery = "select id_user,username,saldo from pengguna where id_user = $rekening";
	if($res = $mysqli->query($sqlquery)){	

		while($row = $res->fetch_array(MYSQLI_NUM)){
			$hasil[] = 	$row;
		}
		if(isset($hasil)){
			foreach($hasil as $gg){
				echo '<div class="row">';
				foreach($gg as $p=>$s){
					if($p==0){
						echo '<div class="col-2 ez">'.'927740'.$gg[$p].'</div>';
					}else if($p==1){
						echo '<div class="col-5 ez">'.substr($gg[1],0,-3).'***</div>';
					}else{
						echo '<div class="col-5 ez">$ '.$gg[$p].'</div>';
					}
					
	                      //'<div class="col-5 ez">'.substr($expose[1],0,-3).'***</div>'
	                      //'<div class="col-5 ez">$ '.$expose[6].'</div>';
				}
				echo '</div>';
				//printf("%s %s %s<br>", $gg[0],$gg[1],$gg[2]);
			}
		}
		//echo $res->mysql_errno();
		$res->free_result();		
	}else{
		//echo "ehey";
		echo $mysqli->error;
		//var_dump($res);
	}
}

function get_user_by_session($ciphertext){
	global $mysqli;
	$stmt =  $mysqli->prepare("select * from pengguna;");
	$stmt->execute();
	$stmt->bind_result($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
	//$data = array(array());
	$datauser = array();
	$no = 0;
	while($stmt->fetch()){
		

		$plaintext = dekripsi_caesar($konfigurasi,$kunci, $ciphertext);
		//echo $plaintext."<br>";
		//var_dump($data_user)."<br>";
		if($plaintext == $username){
			$datauser = array($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
			return $datauser;
			break;
		}
		//echo "id               = ".$id;
		//echo "<br>username     = ".$username;
		//echo "<br>password     = ".$password;
		//echo "<br>nama lengkap = ".$namalengkap;
		//echo "<br>=================================";
	}
	$stmt->close();
	return false;	
}

function get_user_by_username($ciphertext){
	global $mysqli;
	$stmt =  $mysqli->prepare("select * from pengguna;");
	$stmt->execute();
	$stmt->bind_result($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
	//$data = array(array());
	$datauser = array();
	$no = 0;
	while($stmt->fetch()){
		

		$plaintext = $ciphertext;
		//echo $plaintext."<br>";
		//var_dump($data_user)."<br>";
		if($plaintext == $username){
			$datauser = array($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
			return $datauser;
			break;
		}
		//echo "id               = ".$id;
		//echo "<br>username     = ".$username;
		//echo "<br>password     = ".$password;
		//echo "<br>nama lengkap = ".$namalengkap;
		//echo "<br>=================================";
	}
	$stmt->close();
	return false;	
}
function get_user_by_rekening($ciphertext){
	global $mysqli;
	$stmt =  $mysqli->prepare("select * from pengguna;");
	$stmt->execute();
	$stmt->bind_result($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
	//$data = array(array());
	$datauser = array();
	$no = 0;
	while($stmt->fetch()){
		

		$plaintext = $ciphertext;
		//echo $plaintext."<br>";
		//var_dump($data_user)."<br>";
		if($plaintext == $id){
			$datauser = array($id,$username,$password,$namalengkap,$konfigurasi,$kunci,$saldo);
			return $datauser;
			break;
		}
		//echo "id               = ".$id;
		//echo "<br>username     = ".$username;
		//echo "<br>password     = ".$password;
		//echo "<br>nama lengkap = ".$namalengkap;
		//echo "<br>=================================";
	}
	$stmt->close();
	return false;	
}

function update_data_user($ciphertext,$data){
	global $mysqli;
	//susunan data adalah
	//0 id
	//1 username
	//2 password
	//3 nama_lengkap
	//4 konfigurasi
	$datauser = get_user_by_session($ciphertext);
	if($datauser){
		if(isset($data["password"])){
			$datauser[2] = $data["password"];
		}
		if(isset($data["nama_lengkap"])){
			$datauser[3] = $data["nama_lengkap"];
		}
		if(isset($data["konfigurasi"])){
			$datauser[4] = $data["konfigurasi"];
		}
		if(isset($data["kunci"])){
			$datauser[5] = $data["kunci"];
		}

		//UPDATE tbl_data SET Nama = :nama, IPK = :ipk, Asal = :asal WHERE NIM = :nim
		//var_dump($datauser);
		$stmt = $mysqli->prepare("UPDATE pengguna SET password = ?, nama_lengkap = ?,konfigurasi = ?,kunci = ? WHERE username = ?");
		$stmt->bind_param("sssis",$datauser[2],$datauser[3],$datauser[4],$datauser[5],$datauser[1]);
		$stmt->execute();
		echo $stmt->error;
		$stmt->close();
		return "Berhasil update data";
	}else{
		return "Data tidak ditemukan";
	}
}

function update_data_saldo($ciphertext,$data){
	global $mysqli;	
	$stmt = $mysqli->prepare("UPDATE pengguna SET saldo = ? WHERE username = ?");
	$stmt->bind_param("is",$data,$ciphertext);
	$stmt->execute();
	echo $stmt->error;
	$stmt->close();
}

function show_data_user($ciphertext){
	$data = get_user_by_session($ciphertext);
	if($data){
		echo "id               = ".$data[0];
		echo "<br>username     = ".$data[1];
		echo "<br>password     = ".$data[2];
		echo "<br>nama lengkap = ".$data[3];
		echo "<br>konfigurasi  = ".$data[4];
		echo "<br>kunci        = ".$data[5];
		echo "<br>Saldo        = ".$data[6];
		echo "<br>=================================";
	}else{
		echo "data tidak ditemukan";
	}
}


function cari_username($cari){
	$data = get_all_user();
	foreach ($data as $row) {
		//echo "id               = ".$row[0];
		//echo "<br>username     = ".$row[1];
		//echo "<br>password     = ".$row[2];
		//echo "<br>nama lengkap = ".$row[3];
		//echo "<br>=================================";	
		if($row[1] === $cari){
			//echo "ketemu";
			return true;
		}
	}
	return false;
}

function add_user($username,$password,$nama_lengkap,$konfigurasi,$key){
	global $mysqli;
	if(cari_username($username)){
		return "Username tidak dapat digunakan, gunakan yang lain ..";
	}
	$stock = 1;
	$stmt = $mysqli->prepare("INSERT INTO pengguna (username,password,nama_lengkap,konfigurasi,kunci,saldo) VALUES(?,?,?,?,?,?)");
	$stmt->bind_param("ssssii",$username,$password,$nama_lengkap,$konfigurasi,$key,$stock);
	$stmt->execute();
	$stmt->close();
	return "Akun telah berhasil dibuat";
}


function show_all_user(){
	$data = get_all_user();

	foreach ($data as $row) {
		echo "id               = ".$row[0];
		echo "<br>username     = ".$row[1];
		echo "<br>password     = ".$row[2];
		echo "<br>nama lengkap = ".$row[3];
		echo "<br>konfigurasi  = ".$row[4];
		echo "<br>kunci        = ".$row[5];
		echo "<br>=================================";	
	}
}


function randomize_key($key){
	$banyakkarakter = strlen($key);
	$kalimat        =  $key;
	$hasil_generate = "";
	for($i=0;$i<$banyakkarakter;$i++){
		$totalkaraktersekarang = strlen($kalimat);
		if($totalkaraktersekarang>0){
			$acak = rand(0,$totalkaraktersekarang-1);
		}else{
			$acak = 0;
		}
		$karakter = substr($kalimat, $acak,1);
		$hasil_generate.=$karakter;
		$kalimat = str_replace($karakter,"",$kalimat);
		//echo $totalkaraktersekarang."<br>";
	}
	return $hasil_generate;
}



?>