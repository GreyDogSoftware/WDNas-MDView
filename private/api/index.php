<?php
// Core imports
error_reporting(E_ALL);
// Core variables
define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR',              realpath(dirname(__FILE__).'/..'));
define('CORE_DIR',              realpath(BASE_DIR.'/core'));
define('CORE_CONFIG_DIR',       realpath(BASE_DIR.'/../config'));
define('CORE_CLASSES_DIR',      realpath(CORE_DIR.'/classes'));
define('CORE_FUNCTIONS_DIR',    realpath(CORE_DIR.'/functions'));
// Core stuff
require_once(CORE_CLASSES_DIR.'/GreyDogSoftware/ConfigManager.class.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/AuthorizationRequiredException.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/ContentNotFoundException.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/InvalidAuthorizationTokenException.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/InvalidConfigException.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/NoAuthorizationProvidedException.php');
require_once(CORE_CLASSES_DIR.'/Exceptions/RepositoryNotFoundException.php');

// Constants query status
define('HEADER_HTTP_OK',            "HTTP/1.1 200 OK");
define('HEADER_HTTP_BADREQUEST',    "HTTP/1.1 400 Bad Request");
define('HEADER_HTTP_UNAUTHORIZED',  "HTTP/1.1 401 Unauthorized");
define('HEADER_HTTP_NOTFOUND',      "HTTP/1.1 404 Not Found");
// Constants content type
define('HEADER_CONTENT_JSON',       'Content-Type: application/json; charset=utf-8');


function SendResponse($Response):void{
    if(key_exists('headers',$Response)){
        if(key_exists('status',$Response['headers']))header($Response['headers']['status']);
        if(key_exists('type',$Response['headers']))header($Response['headers']['type']);
    }
    if(key_exists('body',$Response)){
        echo json_encode($Response['body']);
    }
}

function APIMain():void{
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
    $CurrentAction = null;
    if(isset($_GET['act']))$CurrentAction = strtolower($_GET['act']);
    if($CurrentAction==null) return;

    switch($CurrentAction){
        case 'getconfig':
            $config = null;
            try {
                $config = GreyDogSoftware\ConfigManager::GetConfig('viewer_conf.php');
            } catch (Exception $e) {
                // Error casting the config
                $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                $Response['body']['exit_code'] = 1;
                $Response['body']['exit_message'] = $e->getMessage();
                SendResponse($Response); return;
            }
            if ($config==null){
                // For some reason the try block didn't catch the error
                $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                $Response['body']['exit_code'] = 1;
                $Response['body']['exit_message'] = 'Invalid config.';
                SendResponse($Response); return;
            }

            $Repos=array();
            foreach ($config['repositories'] as $Key=>$Value){
                $RepoName=''; $RepoDesc=''; $RepoPath=''; $RepoSecr='';
                if(is_array($Value)){
                    if(!key_exists('name',$Value)) $RepoName=$Key;
                    if(key_exists('name',$Value)) $RepoName=$Value['name'];
                    if(key_exists('description',$Value)) $RepoDesc=$Value['description'];
                    if(key_exists('secret',$Value)) $RepoSecr=$Value['secret'];
                    if(key_exists('path',$Value)) $RepoPath=$Value['path'];
                }else{
                    $RepoName=$Key;
                    $RepoPath=$Value;
                }
                $Repos[$Key]=array('path'=>$RepoPath,'name'=>$RepoName,'description'=>$RepoDesc,'secret'=>$RepoSecr);
            }
            $config['repositories']=$Repos;
            //$config['repositories']=array();
            $Response['headers']['status'] = HEADER_HTTP_OK;
            $Response['body']['exit_code'] = 0;
            $Response['body']['exit_message'] = 'sucess';
            $Response['body']['content'] = $config;
            SendResponse($Response); return;
            //break;
        case 'setconfig':

            break;
    }

}
APIMain();
?>