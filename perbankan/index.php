<?php
  require_once "database.php";
  $spill_text = "";
  $spill_text_transfer = "";
  $dataglobal;
  if(get_session()===false){
  	header( "refresh:3;url=login.php" );
  	die("akses ditolak");
  }
  if(check_session()){
  	$informasi = get_user_by_session(get_session());
  	//var_dump($informasi);
  }else{
  	delete_session();
  	header( "refresh:3;url=login.php");
  	die("akses ditolak");
  }

  if(isset($_GET["action"])){
  	if($_GET["action"]=="generatekonfigurasi"){
  		$key = $konfigurasi_default;//"abcdefghijklmnopqrstuvwxyz";
  		$key = randomize_key($key);
  		$data = array();
  		$data['konfigurasi'] = $key;
		update_data_user(get_session(), $data);
    	$cipher = enkripsi_caesar($key,$informasi[5],$informasi[1]);
    	//var_dump($informasi);
    	//echo $cipher."<br>";
    	//echo $informasi[1]."<br>";
    	//echo $informasi[5]."<br>";
    	//echo $key."<br>";
    	//echo $data["konfigurasi"];
    	//update_data_user(get_session(), $data);
    	set_session($cipher);
    	$informasi = get_user_by_session($cipher);
    	header('Location: '.$_SERVER['PHP_SELF']);
    	//var_dump($informasi2);
    	//header('Location: '.$_SERVER['PHP_SELF']);
    	
  	}
  	if($_GET["action"]=="generatekonfigurasidefault"){
  		$key = $konfigurasi_default;///"abcdefghijklmnopqrstuvwxyz";
  		//$key = randomize_key($key);
  		$data = array();
  		$data['konfigurasi'] = $key;
		update_data_user(get_session(), $data);
    	$cipher = enkripsi_caesar($key,$informasi[5],$informasi[1]);
    	//var_dump($informasi);
    	//echo $cipher."<br>";
    	//echo $informasi[1]."<br>";
    	//echo $informasi[5]."<br>";
    	//echo $key."<br>";
    	//echo $data["konfigurasi"];
    	//update_data_user(get_session(), $data);
    	set_session($cipher);
    	$informasi = get_user_by_session($cipher);
    	header('Location: '.$_SERVER['PHP_SELF']);
    	//var_dump($informasi2);
    	//header('Location: '.$_SERVER['PHP_SELF']);
    	
  	}
  }
  if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['kirimsaldo'])){
      $sword = '';
      $konversisaldo=-1;
      if(!isset($_POST['kirim'])){
        $sword .= 'Rekening yang akan dikirim tidak diketahui ! <br>';
      }else{
        $teee =  str_replace("927740","",$_POST['kirim']);
        $konversirekening = (int)$teee;
      }
      if(!isset($_POST['saldoz'])){
        $sword .= 'Saldo yang akan dikirim tidak boleh kosong ! <br>';
      }elseif($_POST['saldoz']==''){
        $sword .= 'Saldo yang akan dikirim tidak boleh kosong ! <br>';
      }else{
        echo $_POST['saldoz'];
        $konversisaldo = (int)$_POST['saldoz'];
      }
      $asalrekening   = $informasi;
      $targetrekening = get_user_by_rekening($konversirekening);
      if($targetrekening){
        if($konversisaldo>$informasi[6]){
          $sword .= "Saldo tidak mencukupi untuk ditransfer !<br>";
        }elseif($konversisaldo<0){
          $sword .= "Saldo yang ditransfer tidak boleh negatif !<br>";
        }
      }else{
        $sword .= 'Rekening yang anda kirim tidak ditemukan !<br>';
      }
      if($asalrekening[0]==$targetrekening[0]){
        $sword .= 'Rekening yang anda kirim tidak boleh sama !<br>';
      }
      if($sword==''){
          //update_data_user(get_session(), $dataglobal);
          //$informasi = get_user_by_session(get_session());
          $user_asal    = $asalrekening[1];
          $user_target  = $targetrekening[1];
          $saldo_asal   = $asalrekening[6]-$konversisaldo;
          $saldo_target = $targetrekening[6]+$konversisaldo;
          update_data_saldo($user_target,$saldo_target);
          update_data_saldo($user_asal,$saldo_asal);
          $informasi = get_user_by_session(get_session());
          $spill_text_transfer = '<div class="pesan_sukses">'.
                    'Saldo sebesar $'.$konversisaldo." Berhasil di transfer ke rekening "."927740".$targetrekening[0]
                    .'</div>';
      }else{
        //var_dump($informasi);
          $spill_text_transfer = '<div class="pesan_error">'.
                    $sword
                    .'</div>';
      }
  }
  if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['updatedata'])){
    function validasi(){
      global $dataglobal;
      $data = array();
      //$data['username'] = $_POST["username"];
      $data['password'] = $_POST["password"];
      $data['nama_lengkap'] = $_POST["nama_lengkap"];
      //$data['kunci'] = $_POST["kunci"];
      $word  = "";
      if(empty($data['nama_lengkap'])
        ||preg_match("/^[' ]+$/",$data['nama_lengkap'])
        ){
        $word .= "nama lengkap tidak boleh kosong !<br>";
      }

      if (!preg_match("/^[a-zA-Z-' ]*$/",$data['nama_lengkap'])) {
        $word .= "nama lengkap hanya boleh mengandung huruf alphabet[a-z atau A-Z] dan spasi<br>";
      }
      /*
      if(cari_username($data["username"])){
        $word .= "Username sudah ada yang menggunakan !<br>";
      }
      if(empty($data['username'])){
        $word .= "Username tidak boleh kosong !<br>";
      }
      if(strlen($data['username'])<10){
        $word .= "Username minimal terdapat 10 karakter !<br>";
      }
      
      if (!preg_match("/^[a-z]*$/",$data['username'])) {
        $word .= "username hanya boleh mengandung huruf alphabet kecil [a-z]<br>";
      }
      */
      if(empty($data['password'])){
        $word .= "Password tidak boleh kosong !<br>";
      }
      if($word===""){
      	$dataglobal = $data;
        return false;
      }else{
        return $word;
      }
    }

    $pesan = validasi();
    //echo $pesan;
    if($pesan){
      $spill_text = '<div class="pesan_error">'.
                    $pesan
                    .'</div>';
    }else{
      //add_user($_POST["username"],$_POST["password"],$_POST["nama_lengkap"],$konfigurasi_default,$kunci_default);
    	update_data_user(get_session(), $dataglobal);
    	//$cipher = enkripsi_caesar($informasi[4],$dataglobal['kunci'],$informasi[1]);
    	//set_session($cipher);
    	$informasi = get_user_by_session(get_session());
    	//echo get_session();
    	//var_dump($informasi);
    	$spill_text = '<div class="pesan_sukses">'.
                    'Berhasil Update data akun'
                    .'</div>';
    };
  }
  
?>

<!DOCTYPE html>
<html>
	<head>
		<!-- Required meta tags -->
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1">

	    <!-- Bootstrap CSS -->
	    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	    <style>
	    	ul li a.ss{
	    		color: #ff8a00!important;
			    font-size: 23px;
			    font-weight: 500;
			    font-family: monospace;
			    letter-spacing: 5px;
			    padding: 0;
	    	}
	    	div#judul{
	    		font-size:30px;
	    		font-family:verdana;
	    		font-weight:600;
	    	}
	    	div.gogo{
	    		background-color: black;
    			border-radius: 20px 0px 0px 0px;
	    	}
	    	div.hias{
	    		border:2px solid #000;
	    	}
	    	span.mark{
	    		color: #ffffff;
			    font-family: monospace;
			    font-size: 30px;
			    background-color: transparent;
			    letter-spacing: 4px;
			    text-shadow: -5px -3px #000;
	    	}
        div.pesan_sukses{
            border: 2px solid #0cbf00;
            background-color: #efffef;
            color: #0cbf00;
            font-weight: 500;
            padding-left: 10px;
            font-size: 16px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 5px;
            overflow: auto;
            font-family: verdana;
        }
        
        div.pesan_error{
          border: 2px solid #f00;
          background-color: #ffcccc;
          color: #f70000;
          font-weight: 500;
          padding-left: 10px;
          font-size: 15px;
          width: 100%;
          margin-top:5px;
          margin-bottom:5px;
          overflow: auto;
          font-family: verdana;
        }
	    	input[name=saldo] {
          color: #1B840C; 
          font-weight:600;
        }​
        div.halo{
          border:2px solid #000;
        }
        div.strip{
	    		background: repeating-linear-gradient(
				  45deg,
				  #606dbc,
				  #606dbc 10px,
				  #465298 10px,
				  #465298 20px
				)


	    </style>
	    <title>Studi Kasus TUGAS 2</title>
	</head>
	<body>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
		<header class="p-3 bg-dark text-white">
		    <div class="container">
      			<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        			<a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          				<!--<svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>-->
          				<img src="logo.png" width="40px">
        			</a>
        			<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          				<li><a href="#" class="nav-link px-2 text-secondary ss">Bank UKSW</a></li>
        			</ul>
        			<!--<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          				<li><a href="#" class="nav-link px-2 text-secondary">Home</a></li>
          				<li><a href="#" class="nav-link px-2 text-white">Features</a></li>
          				<li><a href="#" class="nav-link px-2 text-white">Pricing</a></li>
          				<li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
          				<li><a href="#" class="nav-link px-2 text-white">About</a></li>
        			</ul>
        			
        			<form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
          				<input type="search" class="form-control form-control-dark" placeholder="Cari sesuatu..." aria-label="Search">
        			</form>-->
        			<div class="text-end">
          				<!--<button type="button" class="btn btn-outline-light me-2">Login</button>
          				<button type="button" class="btn btn-warning">Sign-up</button>-->
          				<a href="./"><button type="button" class="btn btn-success">Halaman Utama</button></a>
          				<a href="./logout.php"><div type="button" class="btn btn-danger">Log Out</div></a>
        			</div>
      			</div>
    		</div>
  		</header>
  		<div class="container">
  			<div class="row">
  				<div class="col-12 d-flex justify-content-center mt-5 gogo" style="background-color:black;">
  					<div id="judul"> SELAMAT DATANG - <?php echo $informasi[1];?> </div>
  				</div>
  			</div>
  			<div class="row" style="border:2px solid #000;">
	  			<div class="col-2"></div>
	  			<div class="col-8">


<?php echo $spill_text?>
<?php //isset($_POST['nama_lengkap'])? $_POST['nama_lengkap'] :''
?>

            <form action="" method="POST">
              <div class="form-group">
                <label for="exampleInputEmail1">Nomor Rekening</label>
<?php echo        '<input disabled type="text" value="927740'.($a =isset($informasi[0])? $informasi[0] :'').'" name="rekening" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukan Username anda">' ?>
                <small id="emailHelp" class="form-text text-muted">Nomor yang digunakan untuk bertransaksi antar pengguna</small>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Nama Lengkap</label>
<?php echo        '<input type="text" value="'.($a =isset($informasi[3])? $informasi[3] :'').'" name="nama_lengkap" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap">' ?>
                <small id="emailHelp" class="form-text text-muted">tidak boleh kosong dan hanya boleh mengandung huruf alphabet termasuk spasi</small>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
<?php echo        '<input disabled type="text" value="'.($a =isset($informasi[1])? $informasi[1] :'').'" name="username" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukan Username anda">' ?>
                <small id="emailHelp" class="form-text text-muted">hanya boleh mengandung huruf alphabet kecil [a-z] dan tidak boleh kosong</small>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
<?php echo      '<input type="password" value="'.($a =isset($informasi[2])? $informasi[2] :'').'" name="password" class="form-control" id="password" placeholder="Password">'; ?>
              </div>
              <div class="form-check">
				<input type="checkbox" onclick="lihatpassword()" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">lihat password</label>
              </div>

              <div style="float:right;overflow: auto;padding-bottom: 10px;">
              	<button type="submit" name="updatedata" class="btn btn-primary">UPDATE DATA</button>
              </div>
            </form>    
            <!-----------START---------->

            <div style="height:50px;width:100%;"></div>
<?php echo $spill_text_transfer
?>
            <form action="" method="POST">
              <div class="form-group">
                <label for="exampleInputEmail1">Anda memiliki Saldo sebanyak</label>
<?php echo        '<input disabled type="text" value="USD  '.($a =isset($informasi[6])? $informasi[6] :'').'" name="saldo" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="$  ">' ?>
              </div>
              <div class="row">
                <div class="col-1" style="margin-top:auto;margin-bottom:auto;color:green;">
                  <h2>USD</h2>
                </div>
                <div class="col-11">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Jumlah saldo yang akan dikirim</label>
<?php echo        '<input value="" name="saldoz" class="form-control" id="saldoz" aria-describedby="emailHelp" placeholder="Masukan Jumlah Saldo yang akan dikirim">' ?>
                    
                  </div>
                </div>
              </div>

              <div class="input-group mt-2 mb-2 mr-sm-2">
                <div class="input-group-prepend">
                  <div class="input-group-text" style="font-size:15px">Kirim Ke rekening</div>
                </div>
                <select class="form-control" name="kirim" placeholder="pilih pengguna" id="exampleFormControlSelect1">
                  
<?php
                  $daftarpengguna= get_all_user();
                  foreach($daftarpengguna as $dp){
                      if($dp[0]==$informasi[0]){

                      }else{
                        echo "<option>927740".$dp[0]."</option>";
                      }
                  }                
?>
                </select>
              </div>

              <div style="float:right;overflow: auto;padding-bottom: 10px;">
                <button type="submit" name="kirimsaldo" class="btn btn-danger">Kirim Saldo</button>
              </div>
            </form>    
            <!----------END----------->
          </div>
          <div class="col-2"></div>
	  		</div>
	  		<!--<div class="row" style="border:2px solid #000;border-top:0px solid #000;">
	  			<div class="col-xl-12 strip"><span class="mark">TTS - Symmetric Cryptography</span></div>
	  		</div>-->
	  		
  		</div>
  		<script>
        function setInputFilter(textbox, inputFilter) {
          ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
            textbox.addEventListener(event, function() {
              if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
              } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
              } else {
                this.value = "";
              }
            });
          });
        }
        setInputFilter(document.getElementById("saldoz"), function(value) {
          return /^\d*$/.test(value); // Allow digits and '.' only, using a RegExp
        });
        function lihatpassword(){
          var x = document.getElementById("password");
          if (x.type === "password") {
              x.type = "text";
            } else {
              x.type = "password";
            }
        }
<?php  		echo 'var text = "SELAMAT DATANG - '.$informasi[1].'";'; ?>
  			var warna = ["red","orange","yellow","green","aqua","blue","purple"];
        var warna = ["orange"];
  			var ubah = text.toUpperCase();
  			var awal =0;	
  			var offset = 0;
  			setInterval(function () {
  				var panjangkarakter = ubah.length;
  				var banyakwarna = warna.length;
  				
  				var hasil = "";
  				var char="";
  				var i;
  				for(i=0;i<panjangkarakter;i++){
  					if(awal>=banyakwarna){
  						awal = 0;
  					}
  					char = ubah.substring(i,i+1);
  					hasil += '<span style="color:'+warna[awal]+';">'+char+"</span>";
  					if(char==" "){
  						continue;
  					}
  					//console.log("yok lari yok" + awal);
  					awal +=1;
  				}
  				offset -=1;
  				if(offset<=0){
  					offset = banyakwarna;
  				}
  				awal = offset;
  				
  				//console.log("================================================================== ");
    			document.getElementById('judul').innerHTML = hasil;
			}, 100);
  		</script>
	</body>
</html>