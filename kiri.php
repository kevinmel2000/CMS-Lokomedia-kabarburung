<?php
include "config/koneksi.php";
include "config/fungsi_indotgl.php";
$module = isset($_GET['module']) ?$_GET['module']:'';
// Modul detail berita
if ($module=='detailberita'){
	$detail=mysqli_query($conn,"SELECT * FROM berita,users   
                      WHERE users.username=berita.username 
                      AND id_berita='$_GET[id]'");
	$d   = mysqli_fetch_array($detail);
	$tgl = tgl_indo($d['tanggal']);
	echo "<span class=date>$d[hari], $tgl - $d[jam] WIB</span><br />";
	echo "<span class=judul>$d[judul]</span><br />";
	echo "<span class=posting>Diposting oleh : <b>$d[nama_lengkap]</b> - Dibaca: <b>$d[dibaca]</b> kali</span><br /><br />";
  // Apabila ada gambar dalam berita, tampilkan   
 	if ($d['gambar']!=''){
		echo "<span class=image><img src='foto_berita/$d[gambar]' border=0></span>";
	}
 	$isi_berita=nl2br($d['isi_berita']); // membuat paragraf pada isi berita
	echo "$isi_berita";	 		  
  // Apabila detail berita dilihat, maka tambahkan berapa kali dibacanya
  mysqli_query($conn,"UPDATE berita SET dibaca=$d[dibaca]+1 
              WHERE id_berita='$_GET[id]'");
}


// Modul berita per kategori
elseif ($module=='detailkategori'){
  // Tampilkan nama kategori
  $sq = mysqli_query($conn,"SELECT nama_kategori from kategori where id_kategori='$_GET[id]'");
  $n = mysqli_fetch_array($sq);
  echo "<span class=posting>&#187; Kategori : <b>$n[nama_kategori]</b></span><br /><br />";
  
  // Tampilkan daftar berita sesuai dengan kategori yang dipilih
 	$sql   = "SELECT * FROM berita WHERE id_kategori='$_GET[id]' 
            ORDER BY id_berita DESC";		 
	$hasil = mysqli_query($conn,$sql);
	$jumlah = mysqli_num_rows($hasil);
	// Apabila ditemukan berita dalam kategori
	if ($jumlah > 0){
   while($r=mysqli_fetch_array($hasil)){
		$tgl = tgl_indo($r['tanggal']);
		echo "<span class=date>$r[hari], $tgl - $r[jam] WIB</span><br />";
		echo "<span class=judul><a href=?module=detailberita&id=$r[id_berita]>$r[judul]</a></span><br />";
 		// Apabila ada gambar dalam berita, tampilkan
    if ($r['gambar']!=''){
			echo "<span class=image><img src='foto_berita/small_$r[gambar]' width=110 border=0></span>";
		}
    // Tampilkan hanya sebagian isi berita
    $isi_berita = nl2br($r['isi_berita']); // membuat paragraf pada isi berita
    $isi = substr($isi_berita,0,300); // ambil sebanyak 300 karakter
    $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat
    echo "$isi ... <a href='?module=detailberita&id=$r[id_berita]'>Selengkapnya</a>
          <br /><hr color=#e0cb91 noshade=noshade />";
	 }
  }
  else{
    echo "Belum ada berita pada kategori <b>$_GET[nama_kat]</b>";
  }
}


// Modul detail agenda
elseif ($module=='detailagenda'){
  $detail=mysqli_query($conn,"SELECT * FROM agenda 
                      WHERE id_agenda='$_GET[id]'");
  $d   = mysqli_fetch_array($detail);
  $tgl_posting   = tgl_indo($d['tgl_posting']);
  $tgl_mulai   = tgl_indo($d['tgl_mulai']);
  $tgl_selesai = tgl_indo($d['tgl_selesai']);
  $isi_agenda=nl2br($d['isi_agenda']);
  
  echo "<span class=judul>$d[tema]</span><br />";
  echo "<span class=date>Diposting tanggal: $tgl_posting</span><br /><br />";
  echo "<b>Topik</b>  : $isi_agenda <br />";
  echo "<b>Tanggal</b> : $tgl_mulai s/d $tgl_selesai <br /><br />";
  echo "<b>Tempat</b> : $d[tempat] <br /><br />";
  echo "<b>Pengirim (Contact Person)</b> : $d[pengirim] <br />";
}


// Modul hasil pencarian berita
elseif ($module=='hasilcari'){
  echo "<span class=posting>&#187; Hasil Pencarian</span><br /><br />";
	$cari   = mysqli_query($conn,"SELECT * FROM berita WHERE isi_berita LIKE '%$_POST[kata]%'");
	$jumlah = mysqli_num_rows($cari);
  // Apabila berita ditemukan sesuai dengan kata yang diinginkan 
  if ($jumlah > 0){
    echo "Ditemukan <b>$jumlah</b> berita dengan kata <b>$_POST[kata]</b> : <ul>"; 
    while($r=mysqli_fetch_array($cari)){
      echo "<li><a href='?module=detailberita&id=$r[id_berita]'>$r[judul]</a></li>";
    }      
    echo "</ul>";
  }
  else{
    echo "Tidak ditemukan berita dengan kata <b>$_POST[kata]</b>";
  }
}


// Halaman utama (Home)
else{
  // Tampilkan 4 headline berita terbaru
 	$terkini= mysqli_query($conn,"SELECT * FROM berita ORDER BY id_berita DESC LIMIT 4");		 
	while($t=mysqli_fetch_array($terkini)){
		$tgl = tgl_indo($t['tanggal']);
		echo "<span class=date>$t[hari], $tgl - $t[jam] WIB</span><br />";
		echo "<span class=judul><a href=?module=detailberita&id=$t[id_berita]>$t[judul]</a></span><br />";
 		// Apabila ada gambar dalam berita, tampilkan
    if ($t['gambar']!=''){
			echo "<span class=image><img src='foto_berita/small_$t[gambar]' width=110 border=0></span>";
		}
    // Tampilkan hanya sebagian isi berita
    $isi_berita = nl2br($t['isi_berita']); // membuat paragraf pada isi berita
    $isi = substr($isi_berita,0,300); // ambil sebanyak 300 karakter
    $isi = substr($isi_berita,0,strrpos($isi," ")); // potong per spasi kalimat

    echo "$isi ... <a href='?module=detailberita&id=$t[id_berita]'>Selengkapnya</a>
          <br />	<hr color=#e0cb91 noshade=noshade />";
	}
  
  // Tampilkan 5 judul berita sebelumnya (tampilkan judulnya aja)
  echo "<img src=images/berita_sebelumnya.jpg><br>";
  $sebelum=mysqli_query($conn,"SELECT * FROM berita 
                        ORDER BY id_berita DESC LIMIT 4,5");		 
	while($s=mysqli_fetch_array($sebelum)){
	   echo "&bull; &nbsp; &nbsp; 
          <a href='?module=detailberita&id=$s[id_berita]'>$s[judul]</a><br />";
	}
}
?>
