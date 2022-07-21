<?php
  require_once "database.php";
  $spill_text = "";
  if(get_session()){
    header( "refresh:0;url=./");
    exit("anda sudah masuk");
  }
  if($_SERVER['REQUEST_METHOD']=="POST"){
    function validasi(){
      $data = array();
      $data['username'] = $_POST["username"];
      $data['password'] = $_POST["password"];
      $data['nama_lengkap'] = $_POST["nama_lengkap"];
      $word  = "";
      if(empty($data['nama_lengkap'])
        ||preg_match("/^[' ]+$/",$data['nama_lengkap'])
        ){
        $word .= "nama lengkap tidak boleh kosong !<br>";
      }

      if (!preg_match("/^[a-zA-Z-' ]*$/",$data['nama_lengkap'])) {
        $word .= "nama lengkap hanya boleh mengandung huruf alphabet[a-z atau A-Z] dan spasi<br>";
      }
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
      if(empty($data['password'])){
        $word .= "Password tidak boleh kosong !<br>";
      }
      if($word===""){
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
      add_user($_POST["username"],$_POST["password"],$_POST["nama_lengkap"],$konfigurasi_default,$kunci_default);
      $spill_text = '<div class="pesan_sukses">'.
                    'Berhasil melakukan registrasi akun'
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
          				<a href="./login.php"><div type="button" class="btn btn-outline-light me-2">Login</div></a>
          				<a href="./registrasi.php"><div type="button" class="btn btn-warning">Sign-up</div></a>
                  <a href="./daftarpengguna.php"><div type="button" class="btn btn-success t me-2">List Pengguna</div></a>
          				<!--<button type="button" class="btn btn-success">Halaman Utama</button>
          				<button type="button" class="btn btn-danger">Log Out</button>-->
        			</div>
      			</div>
    		</div>
  		</header>
  		<div class="container">
  			<div class="row">
  				<div class="col-12 d-flex justify-content-center mt-5 gogo" style="background-color:black;">
  					<div id="judul"> REGISTRASI </div>
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
                <label for="exampleInputEmail1">Nama Lengkap</label>
<?php echo        '<input type="text" value="'.($a =isset($_POST['nama_lengkap'])? $_POST['nama_lengkap'] :'').'" name="nama_lengkap" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap">' ?>
                <small id="emailHelp" class="form-text text-muted">tidak boleh kosong dan hanya boleh mengandung huruf alphabet termasuk spasi</small>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
<?php echo        '<input type="text" value="'.($a =isset($_POST['username'])? $_POST['username'] :'').'" name="username" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Masukan Username anda">' ?>
                <small id="emailHelp" class="form-text text-muted">hanya boleh mengandung huruf alphabet kecil [a-z] dan tidak boleh kosong</small>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Password">
              </div>
              <div class="form-check">
                <input type="checkbox" onclick="lihatpassword()" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">lihat password</label>
              </div>
              <div style="float:right;overflow: auto;padding-bottom: 10px;">
              <button type="submit" class="btn btn-primary">REGISTER</button>
              </div>
            </form>    
          </div>
          <div class="col-2"></div>
	  		</div>
	  		<!--<div class="row" style="border:2px solid #000;border-top:0px solid #000;">
	  			<div class="col-xl-12 strip"><span class="mark">TTS - Symmetric Cryptography</span></div>
	  		</div>-->
	  		
  		</div>
  		<script>
        function lihatpassword(){
          var x = document.getElementById("password");
          if (x.type === "password") {
              x.type = "text";
            } else {
              x.type = "password";
            }
        }
  			var text = "REGISTRASI";
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