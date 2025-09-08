<?php
// Core imports
error_reporting(E_ALL);
// Core variables
define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR',              realpath(dirname(__FILE__).'/..'));
define('CORE_DIR',              realpath(BASE_DIR.'/core'));
define('CORE_CONFIG_DIR',       realpath(BASE_DIR.'/config'));
define('CORE_CLASSES_DIR',      realpath(CORE_DIR.'/classes'));
define('CORE_FUNCTIONS_DIR',    realpath(CORE_DIR.'/functions'));
// Core stuff
//require_once(CORE_DIR.'/autoloader.php');
require_once(CORE_CLASSES_DIR.'/GreyDogSoftware/ConfigManager.class.php');
require_once(CORE_FUNCTIONS_DIR.'/resolve_path.php');
require_once(CORE_FUNCTIONS_DIR.'/get_files.php');
require_once(CORE_FUNCTIONS_DIR.'/get_content.php');
require_once(CORE_FUNCTIONS_DIR.'/get_repos.php');

/*
$config = array(
	"repositories"=>        array('Kazu'=>'/mnt/HD/HD_a2/sdogo/Markdown'), // Physical folder where to scan for files
	"allowed_extensions"=>  array("md","txt")	                    // Allowed file extensions. Must be at least one, and lower case
);
*/
//GreyDogSoftware\ConfigManager::SetConfig('viewer_conf.php', $config);
//die();
$config = GreyDogSoftware\ConfigManager::GetConfig('viewer_conf.php');
$Response = array(
    'exit_code'=> 0,
    'exit_message'=> '',
    'query_path'=> '',
    'content' =>''
);
if (isset($_GET['act'])) {
    $Action = strtolower($_GET['act']);
    switch ($Action) {
        case 'get_content':
            if (isset($_GET['path'])){
                $Query= $_GET['path'];
                $Response['exit_code']=0;
                //$Response['content']=get_content($Query,$config);
                get_content($Query,$config);
                die();
            }else{
                $Response['exit_code']=2;
                $Response['exit_message']='No path set';
            }
            break;
        case 'get_files':
            if (isset($_GET['path'])){
                $Response['exit_code']=0;
                $Response['exit_message']='done';
                $Query= $_GET['path'];
                $Response['query_path']=$Query;
                $Files=get_files($Query,$config);
                $Response['content']=$Files;
            }else{
                $Response['exit_code']=2;
                $Response['exit_message']='No path set';
            }
            break;
        case 'get_repos':
            $Response['exit_code']=0;
            $Response['content']=get_repos($config);
            break;
    }
}else{
    $Response['exit_code']=1;
    $Response['exit_message']='No action set';
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($Response);
?>