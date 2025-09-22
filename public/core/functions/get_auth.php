<?php
function get_auth($RepoKey, $Config): bool{
    if(empty($Config))throw new InvalidConfigException();
    if(!key_exists('repositories', $Config)) throw new InvalidConfigException();
    if(!key_exists($RepoKey, $Config['repositories'])) throw new RepositoryNotFoundException();
    if(!is_array($Config['repositories'][$RepoKey])) return true;
    if(!key_exists('secret', $Config['repositories'][$RepoKey])) throw new NoAuthorizationProvidedException();
    $CheckKey = $Config['repositories'][$RepoKey]['secret'];
    if(!key_exists('repokey_'.$RepoKey,$_COOKIE)) throw new NoAuthorizationProvidedException();
    $CookieKey = $_COOKIE['repokey_'.$RepoKey];
    if($CheckKey==$CookieKey) return true;
    throw new InvalidAuthorizationTokenException();
}
?>