<?php
function get_content($SearchPath, $Config){
    if(empty($Config))throw new InvalidConfigException();

    // Exploding the path
    $PathNodes = explode("/", $SearchPath);
    if(count($PathNodes) > 0){
        $RepoKey = $PathNodes[0];
        if (key_exists($RepoKey, $Config['repositories'])){
            $NodePath = substr($SearchPath, strlen($RepoKey));
            $TempPath = '';
            if (is_array($Config['repositories'][$RepoKey])){
                if(key_exists('path',$Config['repositories'][$RepoKey])) $TempPath = $Config['repositories'][$RepoKey]['path']  . $NodePath;
            }else{
                $TempPath = $Config['repositories'][$RepoKey] . $NodePath;
            }
            $NewPath = realpath($TempPath);
            if (file_exists($NewPath)){
                // TODO: Maybe add some control to the content type if necessary?
                header('Content-Type: text/plain; charset=UTF-8');
                readfile($NewPath);
            }else{
                throw new ContentNotFoundException();
            }
        }
    }
    return array();
}
?>
