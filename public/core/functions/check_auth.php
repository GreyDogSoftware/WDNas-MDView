<?php
function check_auth($RepoKey, $Config): bool{
    if(empty($Config))throw new InvalidConfigException();
    if(!key_exists('repositories', $Config)) throw new InvalidConfigException();
    if(!key_exists($RepoKey, $Config['repositories'])) throw new RepositoryNotFoundException();
    if(!is_array($Config['repositories'][$RepoKey])) return false;
    if(key_exists('secret', $Config['repositories'][$RepoKey])) return true;
    return false;
}
?>