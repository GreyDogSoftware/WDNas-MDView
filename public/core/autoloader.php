<?php
function Autoload_core_LoadClass($name){
	//echo $name;
	//$name = str_replace('\\', DS, $name);
	$FileName = CORE_CLASSES_DIR .'/'. $name . '.class.php';
	$FileName = realpath($FileName);
	echo $FileName.'<br>';
	if(file_exists($FileName)){
		//echo 'yes<br>';		
		include_once($FileName);
	}
}
spl_autoload_register('Autoload_core_LoadClass');
?>