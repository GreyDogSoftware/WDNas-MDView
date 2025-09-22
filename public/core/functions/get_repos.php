<?php
function get_repos($Config){
    if(empty($Config))throw new InvalidConfigException();

    $ListDirs=array();
    foreach($Config['repositories'] as $Key => $Value){
        $repoName='';
        $repoPath='';
        $repoDesciption='';
        $repoProtected=false;
        $repoAvailable=false;
        if(is_array($Value)){
            if (key_exists('name',$Value)) $repoName=$Value['name'];
            if (key_exists('description',$Value)) $repoDesciption=$Value['description'];
            if (key_exists('secret',$Value)) $repoProtected=true;
        }else{
            $repoName=$Key;
            $repoProtected=false;
        }
        $repoPath=$Key.'/';

        // This function should be error handled, otherwise it interrupts the repositories enumeration
        try{
            $repoFullPath = resolvePath($repoPath, $Config);
            $repoAvailable=true;
        }catch (Exception $e) {
            $repoAvailable=false;
            $repoProtected=false;
        }
        $ListDirs[]=array("file_name"=>$repoName,
                          "file_type"=>'repo',
                          "file_ext"=>'',
                          "path_rela"=>$repoPath,
                          "description"=>$repoDesciption,
                          "protected"=>$repoProtected,
                          "available"=>$repoAvailable
                    );

    }
    return $ListDirs;
}
?>
