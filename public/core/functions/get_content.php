<?php
function get_content($SearchPath, $Config){
    if(empty($Config))die("No config");

    // Exploding the path
    $PathNodes = explode("/", $SearchPath);
    //echo count($PathNodes);
    if(count($PathNodes) > 0){
        $RepoKey = $PathNodes[0];
        if (key_exists($RepoKey, $Config['repositories'])){
            $NodePath = substr($SearchPath, strlen($RepoKey));
            //$TempPath = $Config['repositories'][$RepoKey] . $NodePath;
            $TempPath = '';
            if (is_array($Config['repositories'][$RepoKey])){
                if(key_exists('path',$Config['repositories'][$RepoKey])) $TempPath = $Config['repositories'][$RepoKey]['path']  . $NodePath;
            }else{
                $TempPath = $Config['repositories'][$RepoKey] . $NodePath;
            }
            $NewPath = realpath($TempPath);

            /*echo $RepoKey.'</br>';
            echo $NodePath.'</br>';
            echo $TempPath.'</br>';
            echo $NewPath.'</br>';*/

            //return file_get_contents($NewPath); // Envía el archivo
            readfile($NewPath);
        }
    }
    return array();
}
?>
