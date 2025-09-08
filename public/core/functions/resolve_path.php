<?php
function resolvePath($SearchPath, $Config){
    if(empty($Config))die("No config");

    // Exploding the path
    $PathNodes = explode("/", $SearchPath);
    if(count($PathNodes) > 0){
        $RepoKey = $PathNodes[0];
        if (key_exists($RepoKey, $Config['repositories'])){
            //echo '<pre>';
            //var_dump($SearchPath);
            //echo '</pre>';
            //echo "\n";

            $NodePath = substr($SearchPath, strlen($RepoKey));
            $TempPath = "";
            if(is_array($Config['repositories'][$RepoKey])){
                if (key_exists('path',$Config['repositories'][$RepoKey])) $TempPath = $Config['repositories'][$RepoKey]['path'] . $NodePath;
            }else{
                $TempPath = $Config['repositories'][$RepoKey] . $NodePath;
            }

            $NewPath = realpath($TempPath);
            /*echo $RepoKey.'</br>';
            echo $NodePath.'</br>';
            echo $TempPath.'</br>';
            echo $NewPath.'</br>';
            die();*/

            $Result = array('fullPath'=>$NewPath,
                            'rootPath'=>$Config['repositories'][$RepoKey],
                            'relativePath'=>$RepoKey.$NodePath,
                            'nodeName'=>$RepoKey);

            if($NewPath) return $Result;
        }
    }
    return false;
}
?>