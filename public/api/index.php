<?php

use GreyDogSoftware\RepositoryBrowser;
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
require_once(CORE_CLASSES_DIR.'/GreyDogSoftware/RepositoryBrowser.class.php');
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

$config = null;
$Response['headers'] =  array(
    'status'=>  HEADER_HTTP_OK,
    'type'=>    HEADER_CONTENT_JSON
);
$Response['body'] =  array(
    'exit_code'=> 0,        // Exit code of the function. If zero, it means no error
    'exit_message'=> '',    // A message in case of an error
    'repokey'=> '',  // Queried path
    'reponame'=> '', // Queried path
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
                        $RepoObj = new GreyDogSoftware\RepositoryBrowser($config,$Query);
                        $RepoObj->GetContent($Query);
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
                    $RepoObj = new GreyDogSoftware\RepositoryBrowser($config,$Query);
                    $Response['body']['query_path']=$Query;
                    $FetchOk = false;
                    $Files=null;
                    try{
                        $Files=$RepoObj->GetFiles($Query);
                        $FetchOk=true;
                    }catch (Exception $e) {
                        if($e->getCode()>=30 && $e->getCode()<=39){
                            $Response['headers']['status'] = HEADER_HTTP_UNAUTHORIZED;
                        }else{
                            $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        }
                        $Response['body']['exit_code']=$e->getCode();
                        $Response['body']['exit_message']=$e->getMessage();
                        $Response['body']['repokey']=$RepoObj->Key;
                        $Response['body']['reponame']=$RepoObj->FriendlyName;

                    }
                    if ($FetchOk){
                        $Response['body']['exit_code']=0;
                        $Response['body']['exit_message']='sucess';
                        $Response['body']['repokey']=$RepoObj->Key;
                        $Response['body']['reponame']=$RepoObj->FriendlyName;
                        $Response['body']['content']=$Files;
                    }
                }else{
                    $NewException = new \NoPathSetException();
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=$NewException->getCode();
                    $Response['body']['exit_message']=$NewException->getMessage();
                }
                break;
            case 'get_repos':
                try{
                    $Content=RepositoryBrowser::GetRepos($config);
                    $Response['body']['exit_code']=0;
                    $Response['body']['exit_message']='sucess';
                    $Response['body']['content']=$Content;
                }catch (Exception $e) {
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=$e->getCode();
                    $Response['body']['exit_message']=$e->getMessage();
                }
                break;
            case 'get_fileinfo':
                if (isset($_GET['path'])){
                    $Query= $_GET['path'];
                    $Response['body']['query_path']=$Query;
                    try{
                        $RepoObj = new RepositoryBrowser($config,$Query);
                        $Content= $RepoObj->GetFileInfo($Query);
                        $Response['body']['exit_code']=0;
                        $Response['body']['exit_message']='sucess';
                        $Response['body']['repokey']=$RepoObj->Key;
                        $Response['body']['reponame']=$RepoObj->FriendlyName;
                        $Response['body']['content']=$Content;
                    }catch (Exception $e) {
                        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        $Response['body']['exit_code']=$e->getCode();
                        $Response['body']['exit_message']=$e->getMessage();
                    }
                }else{
                    $NewException = new \NoPathSetException();
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=$NewException->getCode();
                    $Response['body']['exit_message']=$NewException->getMessage();
                }
                break;
            case 'get_auth':
                if (isset($_GET['repo'])){
                    $Query= $_GET['repo'];
                    try{
                        $RepoObj = new GreyDogSoftware\RepositoryBrowser($config,$Query);
                        $KeyIsValid=$RepoObj->isAuthorized();
                        if($KeyIsValid){
                            $Response['body']['exit_code']=0;
                            $Response['body']['exit_message']='auth ok';
                        }else{
                            $Response['headers']['status'] = HEADER_HTTP_UNAUTHORIZED;
                            $Response['body']['exit_code']=$e->getCode();
                            $Response['body']['exit_message']='sucess';
                            $Response['body']['content']=$KeyIsValid;
                        }
                    }catch (Exception $e) {
                        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        $Response['body']['exit_code']=$e->getCode();
                        $Response['body']['exit_message']=$e->getMessage();
                    }
                }else{
                    $NewException = new \RepositoryPathNotDefinedException();
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=$NewException->getCode();
                    $Response['body']['exit_message']=$NewException->getMessage();
                }
                break;
            case 'set_repokey':
                if (isset($_POST['repo'])){
                    if (isset($_POST['secret'])){
                        $RepoKey = $_POST['repo'];
                        try{
                            $RepoObj = new RepositoryBrowser($config,$RepoKey);
                            $NewSecret = RepositoryBrowser::GetAuthCookie($RepoKey,$_POST['secret']);
                            // Cookies are valid only for the current session.
                            setcookie($RepoObj->GetAuthCookieName(), $NewSecret);
                            $Response['body']['exit_code']=0;
                            $Response['body']['exit_message']='key set';
                            $Response['body']['repokey']=$RepoObj->Key;
                            $Response['body']['reponame']=$RepoObj->FriendlyName;
                            $Response['body']['content']=$NewSecret;
                        }catch (Exception $e) {
                            $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                            $Response['body']['exit_code']=$e->getCode();
                            $Response['body']['exit_message']=$e->getMessage();
                        }
                    }else{
                        $NewException = new \NoAuthorizationProvidedException();
                        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                        $Response['body']['exit_code']=$NewException->getCode();
                        $Response['body']['exit_message']=$NewException->getMessage();
                    }
                }else{
                    $NewException = new \RepositoryPathNotDefinedException();
                    $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
                    $Response['body']['exit_code']=$NewException->getCode();
                    $Response['body']['exit_message']=$NewException->getMessage();
                }
                break;
        }
    }else{
        $NewException = new \NoActionSetException();
        $Response['headers']['status'] = HEADER_HTTP_BADREQUEST;
        $Response['body']['exit_code']=$NewException->getCode();
        $Response['body']['exit_message']=$NewException->getMessage();
    }
}

header($Response['headers']['status']);
header($Response['headers']['type']);
echo json_encode($Response['body']);
?>