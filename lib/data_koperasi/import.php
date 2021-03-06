<?php
error_reporting(0);
include "../../include/lib_inc.php";
require "../../include/Classes/excel_reader.php";
?>	
<div class="modal-dialog">
    	<div class="modal-content">

      	<div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Import Data Koperasi</h4>
        </div>

        <div class="modal-body">
		<form name="myForm" id="myForm" onSubmit="return validateForm()" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
			<input type="file" id="filesekush" name="filesekush" />
			<label><input type="checkbox" name="drop" value="1" /> <u>Kosongkan tabel sql terlebih dahulu.</u> </label>			
         <div class="modal-footer">
            <button class="btn btn-success" type="submit" name="submit" value="Import">Import</button>
            <button type="reset" class="btn btn-danger"  data-dismiss="modal" aria-hidden="true">Kembali</button>
         </div>			
		</form>
		
        </div>		
        </div>
    </div>
			<?php 
			if (isset($_POST['submit'])) {?>			
			<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
			<div id="info"></div>
			<?php

				$target = basename($_FILES['filesekush']['name']) ;
				move_uploaded_file($_FILES['filesekush']['tmp_name'], $target);

				$data = new Spreadsheet_Excel_Reader($_FILES['filesekush']['name'],false);

			//    menghitung jumlah baris file xls
				$baris = $data->rowcount($sheet_index=0);

			//    jika kosongkan data dicentang jalankan kode berikut
				if($_POST['drop']==1){
			//             kosongkan tabel sektor usaha
						 $truncate ="TRUNCATE TABLE data_koperasi";
						 mysqli_query($conn,$truncate);
				};

			//    import data excel mulai baris ke-2 (karena tabel xls ada header pada baris 1)
				for ($i=2; $i<=$baris; $i++)
				{
			//        menghitung jumlah real data. Karena kita mulai pada baris ke-2, maka jumlah baris yang sebenarnya adalah 
			//        jumlah baris data dikurangi 1. Demikian juga untuk awal dari pengulangan yaitu i juga dikurangi 1
					$barisreal = $baris-1;
					$k = $i-1;

			// menghitung persentase progress
					$percent = intval($k/$barisreal * 100)."%";

			// mengupdate progress
					echo '<script language="javascript">
					document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.'; background-color:lightblue\">&nbsp;</div>";
					document.getElementById("info").innerHTML="'.$k.' data berhasil diinsert ('.$percent.' selesai).";
					</script>';

			//       membaca data (kolom ke-1 sd terakhir)
				  $idkop		  = $data->val($i, 1);
				  $namkop		  = $data->val($i, 2);
				  $jenkop		  = $data->val($i, 3);
				  $peng			  = $data->val($i, 4);
				  $kelkop		  = $data->val($i, 5);	
				  $alamat		  = $data->val($i, 6);
				  $benkop		  = $data->val($i, 7);
			      $idkel		  = $data->val($i, 8);
				  $lat 			  = $data->val($i, 9);
				  $lng			  = $data->val($i, 10);
				  $idsektor		  = $data->val($i, 11);
				  $gam			  = $data->val($i, 12);	
					
			//      setelah data dibaca, masukkan ke tabel pegawai sql
				  $query = "INSERT into data_koperasi (id_koperasi, nama_koperasi,jenis_koperasi,id_pengurus,kel_koperasi,alamat_koperasi,bentuk_koperasi,id_kelurahan,latitude,longitude,id_sektor,foto1,foto2,foto3,foto4,foto5)values('$idkop','$namkop','$jenkop','$peng','$kelkop','$alamat','$benkop','$idkel','$lat','$lng','$idsektor','$gam','$gam','$gam','$gam','$gam')";
				  $hasil = mysqli_query($conn,$query);

				  flush();

			//      kita tambahkan sleep agar ada penundaan, sehingga progress terbaca bila file yg diimport sedikit
			//      pada prakteknya sleep dihapus aja karena bikin lama hehe
			 //     sleep(1);

				}

			//    hapus file xls yang udah dibaca
				unlink($_FILES['filesekush']['name']);
			}
			echo '<a href="javascript:history.go(-1)">back</a>';
			?>
	 
<script type="text/javascript">
//    validasi form (hanya file .xls yang diijinkan)
    function validateForm()
    {
        function hasExtension(inputID, exts) {
            var fileName = document.getElementById(inputID).value;
            return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
        }
 
        if(!hasExtension('filesekush', ['.xls'])){
            alert("Hanya file XLS (Excel 2003) yang diijinkan.");
            return false;
        }
    }
</script>