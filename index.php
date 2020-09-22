<?php
require __DIR__.'/vendor/autoload.php';
include 'excel_reader2.php';

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

?>

<html>
<head>
 <meta charset="utf-8">
  <meta name="viewport" content="width-device-width, initial-scale=1.0">
  <title>Admin Sidoma</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
  <div id="login_div" class="main-div">
    <nav class="navbar navbar-light bg-danger">
      <a class="navbar-brand">
        <img src="assets/sidoma.png"  alt="" loading="lazy">
      </a>
    </nav>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <center><br><h4><strong>Selamat datang di website admin Sidoma.<br> Silahkan login terlebih dahulu untuk masuk ke aplikasi Sidoma.</strong></h4></center>
          <div class="form-group"><br>
            <label>Email</label>
            <input type="text" class="form-control" id="email_field" placeholder="Email">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="Password" class="form-control" id="password_field" placeholder="Password">
          </div>
         <button onclick="login()" class="btn btn-danger">Login</button>
  </div>
  </div>
  </div>
  </div>

  
  <div id="user_div" class="loggedin-div" >
    <nav class="navbar navbar-light bg-danger">
    <a class="navbar-brand">
      <img src="assets/sidoma.png" alt="" loading="lazy">
    </a>
    <form class="form-inline my-2 my-lg-0">
        <button class="btn btn-outline-light my-2 my-sm-0" onclick="logout()">Logout</button>
      </form>
  </nav><br>
  <div class="row">
  &emsp;<div class="col">
    <a href="assets/template-excel-mahasiswa.xls" download>
              <button class="btn btn-danger">Template Mahasiswa</button>
            </a>
            <a href="assets/template-excel-dosen.xls" download>
              <button class="btn btn-danger">Template Dosen</button>
            </a>
    </div>
    
  </div>


    <center>
    <form method="post" enctype="multipart/form-data">
        <p>Pilih file excel yang akan di import</p>
        <input type="file" name="fileimport">
        <button type="submit" class="btn btn-danger" name="btnimport">Upload Data Mahasiswa</button>
        <button type="submit" class="btn btn-danger" name="btnimportdosen">Upload Data Dosen</button>
    </form>
    </center>
    <!-- INPUT DATA MAHASISWA -->
  <?php 
        if (isset($_POST['btnimport'])) {
          
            if (isset($_FILES['fileimport'])) {
                $factory = (new Factory())
                ->withDatabaseUri('https://sidoma-dosen.firebaseio.com/');
                $database = $factory->createDatabase();

                $target = basename($_FILES['fileimport']['name']) ;
                move_uploaded_file($_FILES['fileimport']['tmp_name'], $target);
              
                
                chmod($_FILES['fileimport']['name'],0777);
              
                $data = new Spreadsheet_Excel_Reader($_FILES['fileimport']['name'],false);
              
                $jumlah_baris_sheet1 = $data->rowcount(0);
               
                for ($i=2; $i<=$jumlah_baris_sheet1; $i++) {
                    
                    $emailMahasiswa    = $data->val($i, 1, 0);
                    $fotoMahasiswa     = $data->val($i, 2, 0);
                    $kelasMahasiswa    = $data->val($i, 2, 0);
                    $kodeDosen         = $data->val($i, 3, 0);
                    $namaMahasiswa     = $data->val($i, 4, 0);
                    $nimMahasiswa      = $data->val($i, 5, 0);
                    $noMahasiswa       = $data->val($i, 6, 0);
                    // $statusMahasiswa   = $data->val($i, 8, 0);
                    // $totalPoin         = $data->val($i, 9, 0);
                    
                    
                    $database->getReference('Mahasiswa/'.$nimMahasiswa)->set([
                        'email_mhs'     => $emailMahasiswa,
                        'foto_mhs'      => "default",
                        'kelas'         => "$kelasMahasiswa",
                        'kode_dosen'    => $kodeDosen,
                        'nama_mhs'      => $namaMahasiswa,
                        'nim'           => "$nimMahasiswa",
                        'no_telepon'    => "0$noMahasiswa",
                        'status'        => "mahasiswa",
                        'total_poin'    => 100,
                        ]
                    );
                }
    
                unlink($_FILES['fileimport']['name']);
                if ($target) {
            
                  echo '<center> <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-animation="true" data-delay="5000" data-autohide="false">
                  <div class="toast-header" style="width: 100%;">
                    <span class="rounded mr-2 bg-danger" style="width: 15px;height: 15px"></span>
             
                    <strong class="mr-auto">Berhasil Mengupload</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  
                </div></center><br>';
              
                } else {
                    echo '<center> <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-animation="true" data-delay="5000" data-autohide="false">
                    <div class="toast-header" style="width: 100%;">
                      <span class="rounded mr-2 bg-danger" style="width: 15px;height: 15px"></span>
               
                      <strong class="mr-auto">Gagal Mengupload Mengupload</strong>
                      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    
                  </div></center><br>';
                }
                

            }
        }
    ?>

    <!-- INPUT DATA DOSEN -->
    <?php 
    
          if (isset($_POST['btnimportdosen'])) {
              if (isset($_FILES['fileimport'])) {

                $factory = (new Factory())

                ->withDatabaseUri('https://sidoma-dosen.firebaseio.com/');
                
                $database = $factory->createDatabase();

                $target = basename($_FILES['fileimport']['name']) ;
                move_uploaded_file($_FILES['fileimport']['tmp_name'], $target);
                
                
                chmod($_FILES['fileimport']['name'],0777);
                
                
                $data = new Spreadsheet_Excel_Reader($_FILES['fileimport']['name'],false);
                
                
                
                $jumlah_baris_sheet1 = $data->rowcount(0);
               
                for ($i=2; $i<=$jumlah_baris_sheet1; $i++) {
                    
                    $emailDosen    = $data->val($i, 1, 0);
                    $fotoDosen    = $data->val($i, 2, 0);
                    $kodeDosen     = $data->val($i, 3, 0);
                    $namaDosen    = $data->val($i, 4, 0);
                    $nipDosen     = $data->val($i, 5, 0);
                    $noDosen     = $data->val($i, 6, 0);
                    $statusDosen      = $data->val($i, 7, 0);
                   
                    $database->getReference('Dosen/'.$kodeDosen)->set([
                        'email_dosen'     => $emailDosen,
                        'foto_dosen'     => "default",
                        'kode_dosen'      => $kodeDosen,
                        'nama_dosen'      => $namaDosen,
                        'nip'             => "$nipDosen",
                        'no_telepon'      => "0$noDosen",
                        'status'          => "dosen",
                        ]
                    );
                }
               
                unlink($_FILES['fileimport']['name']);
                if ($target) {
                  echo '<script type="text/javascript">
                    alert("Berhasil Mengupload");</script>';
              
                } else {
                    echo "Gagal Mengupload!";
                }
            }
        }
    ?>

    <script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
    <script>
    // Initialize Firebase
    const firebaseConfig = {
      apiKey: "AIzaSyAjD9TKwuSV2GBkomX-iocH3M3p9qH3I60",
      authDomain: "sidoma-dosen.firebaseapp.com",
      databaseURL: "https://sidoma-dosen.firebaseio.com",
      projectId: "sidoma-dosen",
      storageBucket: "sidoma-dosen.appspot.com",
      messagingSenderId: "307370909968",
      appId: "1:307370909968:web:ef51cc42ba72834b858595"
    };
    firebase.initializeApp(firebaseConfig); 
   
  </script>

   <script src="index.js"></script>

   <div class='container'>
        <div class="row">
          <div class="col-md-6">
            <form class="border p-4 mb-4" id="reviewForm">
             <h4><strong>Form Tambah Kesalahan</strong></h4>
              <div class="form-group">
                <input type="hidden" class="form-control" id="hiddenId" placeholder="Kesalahan">
              </div>

              <div class="form-group">
                <label>Kesalahan</label>
                <input type="text" class="form-control" id="kesalahan" placeholder="Kesalahan">
              </div>

              <div class="form-group">
                <label>Poin</label>
                <input type="text" class="form-control" id="poin_kesalahan" placeholder="Poin">
              </div>
            

              <div class="form-group">
                <select name="jenis" id="ket" class="form-control">
                  <option value="" disable selected>Jenis Soft Skill</option>
                  <option value="SoftSkillSatu">Kemampuan bekerja dalam tim</option>
                  <option value="SoftSkillDua">Komunikasi verbal</option>
                  <option value="SoftSkillTiga">Kemampuan memecahkan masalah</option>
                  <option value="SoftSkillEmpat">Kemampuan memperoleh dan mengelola informasi</option>
                  <option value="SoftSkillLima">Kemampuan untuk membuat rencana dan prioritas</option>
               </select>
              </div>

           <button type='submit' class="btn btn-danger">Tambahkan</button><br>
           <br><p>Keteranga Jenis Soft SKill:<br><br>
           SoftSkillSatu  : Kemampuan bekerja dalam tim <br>
           SoftSkillDua   : Komunikasi verbal<br>
           SoftSkillTiga  : Kemampuan memecahkan masalah<br>
           SoftSkillEmpat : Kemampuan memperoleh dan mengelola informasi<br>
           SoftSkillLima  : Kemampuan untuk membuat rencana dan prioritas</p>
           
          </form>
         </div>
      
        <div class="col-md-6">
        
        <h4><strong>Daftar Kesalahan</strong></h4>
          <ul id='reviews'></ul>
         
        </div>
      </div>
     </div>
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

 
  <script src="index.js"></script>


</body>
</html>
