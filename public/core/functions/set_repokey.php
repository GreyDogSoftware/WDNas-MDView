<?php
function set_repokey($RepoName, $RepoSecret){
    $HashKey = md5($RepoName.'_'.$RepoSecret);
    return $HashKey ;
}
?>