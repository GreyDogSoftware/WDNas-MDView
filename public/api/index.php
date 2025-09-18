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
require_once(CORE_CLASSES_DIR.'/GreyDogSoftware/ConfigManager.class.php');
require_once(CORE_FUNCTIONS_DIR.'/resolve_path.php');
require_once(CORE_FUNCTIONS_DIR.'/get_files.php');
require_once(CORE_FUNCTIONS_DIR.'/get_content.php');
require_once(CORE_FUNCTIONS_DIR.'/get_repos.php');
require_once(CORE_FUNCTIONS_DIR.'/get_fileinfo.php');

// Constants query status
define('HEADER_HTTP_OK',            "HTTP/1.1 200 OK");
define('HEADER_HTTP_BADREQUEST',    "HTTP/1.1 400 Bad Request");
define('HEADER_HTTP_UNAUTHORIZED',  "HTTP/1.1 401 Unauthorized");
define('HEADER_HTTP_NOTFOUND',      "HTTP/1.1 404 Not Found");
// Constants content type
define('HEADER_CONTENT_JSON',       'Content-Type: application/json; charset=utf-8');


$config = null;
$Response['headers'] =  array(
    'status'=>  HEADER_HTTP_OK,
    'type'=>    HEADER_CONTENT_JSON
);
$Response['body'] =  array(
    'exit_code'=> 0,        // Exit code of the function. If zero, it means no error
    'exit_message'=> '',    // A message in case of an error
    'query_path'=> '',      // Queried path
    'content' =>''          // Content from the queried path
);

try {
    $config = GreyDogSoftware\ConfigManager::GetConfig('viewer_conf.php');
} catch (Exception $e) {
    $config = null;
    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
    $Response['body']['exit_code'] = 1;
    $Response['body']['exit_message'] = $e->getMessage();
}

if ($config!=null){
    if (isset($_GET['act'])) {
        $Action = strtolower($_GET['act']);
        switch ($Action) {
             // TODO: Create a function, so the API returns the file info.
            case 'get_content':
                if (isset($_GET['path'])){
                    $Query= $_GET['path'];
                    $Response['body']['exit_code']=0;
                    $Response['body']['exit_message']='sucess';
                    $Response['body']['query_path']=$Query;
                    try {
                        get_content($Query,$config); // Reading the target file to the output.
                    } catch (Exception $e) {
                        header($Response['headers']['status']);
                        echo $e->getMessage();
                    }
                    die(); // This function needs to end here, otherwise, the headers get sent.
                }else{
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=2;
                    $Response['body']['exit_message']='No path set';
                }
                break;
            case 'get_files':
                if (isset($_GET['path'])){
                    $Query= $_GET['path'];
                    $Response['body']['query_path']=$Query;
                    $FetchOk = false;
                    $Files=null;
                    try{
                        $Files=get_files($Query,$config);
                        $FetchOk=true;
                    }catch (Exception $e) {
                        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        $Response['body']['exit_code']=1;
                        $Response['body']['exit_message']=$e->getMessage();
                    }
                    if ($FetchOk){
                        $Response['body']['exit_code']=0;
                        $Response['body']['exit_message']='sucess';
                        $Response['body']['content']=$Files;
                    }
                }else{
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=2;
                    $Response['body']['exit_message']='No path set';
                }
                break;
            case 'get_repos':
                try{
                    $Content=get_repos($config);
                    $Response['body']['exit_code']=0;
                    $Response['body']['exit_message']='sucess';
                    $Response['body']['content']=$Content;
                }catch (Exception $e) {
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=1;
                    $Response['body']['exit_message']=$e->getMessage();
                }
                break;
            case 'get_fileinfo':
                if (isset($_GET['path'])){
                    $Query= $_GET['path'];
                    $Response['body']['query_path']=$Query;
                    try{
                        $Content=get_FileInfo($Query, $config);
                        $Response['body']['exit_code']=0;
                        $Response['body']['exit_message']='sucess';
                        $Response['body']['content']=$Content;
                    }catch (Exception $e) {
                        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        $Response['body']['exit_code']=1;
                        $Response['body']['exit_message']=$e->getMessage();
                    }
                }else{
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=2;
                    $Response['body']['exit_message']='No path set';
                }
                break;
        }
    }else{
        $Response['body']['exit_code']=1;
        $Response['body']['exit_message']='No action set';
    }
}

header($Response['headers']['status']);
header($Response['headers']['type']);
echo json_encode($Response['body']);
?>