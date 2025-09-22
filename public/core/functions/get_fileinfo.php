<?php
function get_FileInfo($filePath, $Config){
    if(empty($Config))throw new InvalidConfigException();

    $Result = array(
        'name'=>'',         // Name without extension
        'fullname'=>'',     // Name with extension
        'path'=>$filePath,  // Relative path
        'size'=> 0,         // File size in bytes
        'extension'=>'',    // File extension
        'exists'=>false,    // Shows if the file exists
        'protected'=>false  // Shows if the file is under a protected repo
    );
    try{
        $Status = resolvePath($filePath, $Config);
        $info = new SplFileInfo($Status['fullPath']);
    }catch (Exception $e) {
        throw new ContentNotFoundException();
    }
    $Result['name']=$info->getFilename();
    $Result['fullname']=$info->getFilename();
    $Result['size']=$info->getSize();
    $Result['extension']=$info->getExtension();
    $Result['exists']=true;
    $Result['protected']=$Status['protected'];
    //$Result['debug']=$Status;

    if(strlen($Result['extension'])>0) $Result['name']=substr($info->getFilename(),0,stripos($Result['name'],$Result['extension'])-1);

    return $Result;
}
// http://localhost/md-view/api/?act=get_fileinfo&path=main/git.md
?>