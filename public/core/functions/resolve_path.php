<?php
function resolvePath($SearchPath, $Config){
    if(empty($Config))throw new \Exception("Empty config array");

    // Exploding the path
    $PathNodes = explode("/", $SearchPath);
    if(count($PathNodes) > 0){
        $RepoKey = $PathNodes[0];
        if (key_exists($RepoKey, $Config['repositories'])){
            $Result = array(
                'fullPath'=>'',
                'rootPath'=>'',
                'relativePath'=>'',
                'nodeName'=>'');
            $NodePath = substr($SearchPath, strlen($RepoKey));
            $RepoProtected = false;
            $TempPath = "";
            if(is_array($Config['repositories'][$RepoKey])){
                if (key_exists('path',$Config['repositories'][$RepoKey])) $TempPath = $Config['repositories'][$RepoKey]['path'] . $NodePath;
                if (key_exists('secret',$Config['repositories'][$RepoKey])) $RepoProtected=true;
            }else{
                $TempPath = $Config['repositories'][$RepoKey] . $NodePath;
            }
            $NewPath = realpath($TempPath);

            $Result['fullPath'] = $NewPath;
            $Result['rootPath'] = $Config['repositories'][$RepoKey];
            $Result['relativePath'] = $RepoKey.$NodePath;
            $Result['nodeName'] = $RepoKey;
            $Result['protected'] = $RepoProtected;

            // TODO: This need to throw an exception if the path doesn't exists
            if($NewPath) {
                return $Result;
            }else{
                throw new \Exception("Cannot resolve path: ".$SearchPath);
            }
        }
    }
    throw new \Exception("Cannot resolve path: ".$SearchPath);
}
?>