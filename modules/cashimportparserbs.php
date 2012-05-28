<?php

//ini_set( 'display_errors', 'On' ); 
//error_reporting( E_ALL );

// 1 - właczone szykania 0 - wylączone szukania
define("SZUKAJDANE", 1);
define("SZUKAJID", 1);

header("Content-type: text/html; charset=utf-8"); 

if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES['file']['size'])
{
	$file = file($_FILES['file']['tmp_name']);
	$ln = $_POST['number'];
} 
elseif(isset($_FILES['file'])) // upload errors
	switch($_FILES['file']['error'])
	{
		case 1: 
		case 2: $error['file'] = trans('File is too large.'); break;
		case 3: $error['file'] = trans('File upload has finished prematurely.'); break;
		case 4: $error['file'] = trans('Path to file was not specified.'); break;
		default: $error['file'] = trans('Problem during file upload.'); break;
}
//-----------------------------------------------
		// Wyszukiwanie przelewów
		
	$array=array();
	$ln = $_POST['number'];

// Upload pliku	
	$data = date('Y-m-d_H-i-s');
	$nazwa = $data.'-'.$_FILES['file']['name'];
	move_uploaded_file($_FILES['file']['tmp_name'], "backups/import/".$nazwa); 
	
// 	czytanie pliku

	$plik = "backups/import/".$nazwa;
	$i=0;
	$uchwyt = fopen($plik,rb);
	while(!feof($uchwyt)){
	 	$linia = fgets($uchwyt);
		$array[$i] = strtoupper(iconv("windows-1250","UTF-8",$linia));
		$i++;
	}
	
		
// tworzenie tablicy
	$i = 0;
	$arrays=array();
	foreach($array as $a)if(!empty($a)){
	
		$a = explode(";", $a);
		$arrays[$i][1] = $a[0]; // date
		$arrays[$i][2] = substr($a[1], -4); // id
		$arrays[$i][3] = $a[2]; // cash
		$arrays[$i][4] = $a[1]; // konto
		$i++;
	}
	$array = $arrays;
//	echo '<textarea>';
//	print_r($array);
//	echo '</textarea>';
//	exit();
			
//----------------------------------------------------------
	   	// dodawanie po ID
	   	
	$i = $e =0;
	$DB->Execute('INSERT INTO sourcefiles (name, idate, userid) VALUES (?, ?, ?)', array($nazwa, time(), $AUTH->id));
	$sid = $DB->GetLastInsertId('sourcefiles');
	
	foreach($array as $a){
		$c = $DB->GetOne('SELECT name FROM customers WHERE id=?', array(intval($a[2]))).' '. $DB->GetOne('SELECT lastname FROM customers WHERE id=?', array($a[2]));
		if($c != ' ') {
			// DODAJE 	
			$DB->Execute('INSERT INTO cashimport (date, value, customer, customerid, description, hash,closed, sourcefileid) VALUES (?,?,?,?,?,?,?,?)', 
					array(strtotime($a[1]), $a[3], $c, intval($a[2]), 'Dziękujemy za wpłate.', '', 0 , intval($sid)));
			$i++;
		} else {
			// BŁEDY
			$DB->Execute('INSERT INTO cashimport (date, value, customer, customerid, description, hash,closed, sourcefileid) VALUES (?,?,?,?,?,?,?,?)', 
					array(strtotime($a[1]), $a[3], '', NULL, $a[4], '', 0 , intval($sid)));
			
			$e++;
		}
	}

		
		
	

	
//----------------------------------------------		
// Syatystyki

$layout['stat']['all'] =count($array);	
$layout['stat']['add'] =$i;	
$layout['stat']['error'] =$e;	


$layout['pagetitle'] = trans('Cash Operations Import');
$layout['file'] = $file;

$SESSION->save('backto', $_SERVER['QUERY_STRING']);
$SMARTY->assign('file', $file);
$SMARTY->assign('error', $error);
$SMARTY->assign('customerlist', $LMS->GetCustomerNames());
$SMARTY->assign('sourcelist', $DB->GetAll('SELECT id, name FROM cashsources ORDER BY name'));
$SMARTY->display('cashimportbs.html');



?>
