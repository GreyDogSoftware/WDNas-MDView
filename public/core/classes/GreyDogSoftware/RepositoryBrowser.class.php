<?php
namespace GreyDogSoftware{
    class RepositoryBrowser{
        public $FriendlyName='';
        public $Description='';
        public $Key='';
        public $Path='';
        private $Secret='';
        private $AllowedExtensions=array();
        private $prop_isAvailable=false;
        public function __construct($ConfigArray, $LoadPath){
            $RepoKey = self::GetKeyFromPath($LoadPath);
            if(empty($ConfigArray))throw new \InvalidConfigException();
            if(!key_exists('repositories', $ConfigArray)) throw new \InvalidConfigException();
            if(!key_exists('allowed_extensions', $ConfigArray)) throw new \InvalidConfigException();
            if(!key_exists($RepoKey, $ConfigArray['repositories'])) throw new \RepositoryNotFoundException();
            if(is_array($ConfigArray['allowed_extensions'])) $this->AllowedExtensions = $ConfigArray['allowed_extensions'];
            if(is_array($ConfigArray['repositories'][$RepoKey])){
                // Repo config defined as array
                if(key_exists('path', $ConfigArray['repositories'][$RepoKey])) $this->Path=realpath($ConfigArray['repositories'][$RepoKey]['path']);
                if(key_exists('name', $ConfigArray['repositories'][$RepoKey])){
                    $this->FriendlyName=$ConfigArray['repositories'][$RepoKey]['name'];
                }else{
                    $this->FriendlyName=$RepoKey;
                }
                if(key_exists('description', $ConfigArray['repositories'][$RepoKey])) $this->Description=$ConfigArray['repositories'][$RepoKey]['description'];
                if(key_exists('secret', $ConfigArray['repositories'][$RepoKey])) $this->Secret=$ConfigArray['repositories'][$RepoKey]['secret'];
            }else{
                // Repo config defined as string
                $this->FriendlyName=$RepoKey;
                $this->Path=realpath($ConfigArray['repositories'][$RepoKey]);
                $this->Description='';
                $this->Secret='';
            }
            $this->Key=$RepoKey;
            $this->prop_isAvailable=(file_exists($this->Path))?true:false;
        }

        public function GetFiles($Path) : array{
            // We don't catch any error. Error handling is done in the
            // implementation side.
            //echo '<pre>';var_dump($this);echo '</pre>';
            if($this->isAuthorized()){
                $ListDirs=array();
                $ListFile=array();
                $RealPath=$this->GetAbsolutePath($Path);
                $dir = new \DirectoryIterator($RealPath);
                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        $FileName = $fileinfo->getFilename();
                        $FileExten= ($fileinfo->isDir())?NULL:strtolower($fileinfo->getExtension());
                        $FileType = ($fileinfo->isDir())?'dir':'file';
                        if($fileinfo->isDir()){
                            $ListDirs[]=array("name"=>$FileName,"type"=>$FileType,
                                              "extension"=>$FileExten,"relative"=>$this->GetRelativePath($fileinfo->getRealPath()));
                        }else{
                            if(in_array($FileExten,$this->AllowedExtensions)){
                                $ListFile[]=array("name"=>$FileName,"type"=>$FileType,
                                                  "extension"=>$FileExten,"relative"=>$this->GetRelativePath($fileinfo->getRealPath()));
                            }
                        }
                    }
                }
                asort($ListDirs);
                asort($ListFile);
                return array_merge($ListDirs,$ListFile);
            }else{
                throw new \AuthorizationRequiredException();
            }
        }

        public function GetContent($Path):void{
            $ContentKey = $this->GetKeyFromPath($Path);
            if(strtolower($ContentKey)==strtolower($this->Key)){
                $ContentPath = $this->GetAbsolutePath($Path);
                if($this->isAuthorized()){
                    header('Content-Type: text/plain; charset=UTF-8');
                    readfile($ContentPath);
                }else{
                    throw new \AuthorizationRequiredException();
                }
            }else{
                // Invalid path. Maybe, empty, or from another repo.
            }
        }
        public function isSecured() : bool {
            return ($this->Secret==='')?false:true;
        }
        public function isAuthorized(): bool{
            // The function must throw exceptions in case of a fail, since
            // the exception messages are used in the front end UI.
            if (!$this->isSecured()){
                //echo 'empty key';
                return true; // The repo has no key set
            }else{
                //echo 'has key';
                if(!key_exists($this->GetAuthCookieName(),$_COOKIE)) throw new \NoAuthorizationProvidedException();
                $CookieKey = $_COOKIE[$this->GetAuthCookieName()];
                if($this->GetAuthHash()===$CookieKey) return true;
                throw new \InvalidAuthorizationTokenException();
            }
        }
        private function GetAuthHash():string{
            return md5($this->Key.'_'.$this->Secret);
        }
        public static function GetAuthCookie($RepoKey, $RepoSecret):string{
            // This function is user controlled. We don't care if the actual content
            // is good or not, the validation is made on the isAuthorized() method.
            // If the hashing method is changed, it must be also updated in the
            // GetAuthHash method.
            $HashKey = md5($RepoKey.'_'.$RepoSecret);
            return $HashKey ;
        }
        public function GetAuthCookieName():string{
            // This is the cookie name that the Auth validation function will search for.
            return 'repokey_'.$this->Key;
        }
        public function isAvailable():bool{
            return $this->prop_isAvailable;
        }
        private function GetAbsolutePath($Path):string{
            // Exploding the path
            $PathKey = self::GetKeyFromPath($Path);
            if($PathKey!=''){
                $NodePath = substr($Path, strlen($this->Key));
                //echo $NodePath; die();
                $TempPath = $this->Path.'/' . $NodePath;
                //echo $TempPath; die();
                $NewPath = realpath($TempPath);
                if($NewPath) return $NewPath;
                throw new \RepositoryPathInvalidException();
            }
            throw new \RepositoryNotFoundException();
        }
        private function GetRelativePath($Path):string{
            // Since we are cutting the start of the string
            // Check if the string is long enough first.
            if(strlen($Path)>=strlen($this->Path)){
                $Result = $this->Key.substr($Path,strlen($this->Path));
                // This is needed, so if the server OS is windows, the path doesn't
                // get escaped by the browser js engine.
                $Result = str_replace('\\','/',$Result);
                return $Result;
            }
            //echo $Path."\n";
            return '';
        }
        public function GetFileInfo($filePath){
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
                $info = new \SplFileInfo($this->GetAbsolutePath($filePath));
            }catch (\Exception $e) {
                throw new \ContentNotFoundException();
            }
            $Result['name']=$info->getFilename();
            $Result['fullname']=$info->getFilename();
            $Result['size']=$info->getSize();
            $Result['extension']=$info->getExtension();
            $Result['exists']=true;
            $Result['protected']=$this->isSecured();
            // Removing the extension from the name
            if(strlen($Result['extension'])>0) $Result['name']=substr($info->getFilename(),0,stripos($Result['name'],$Result['extension'])-1);
            return $Result;
        }
        private static function GetKeyFromPath($Path):string{
            // The key should be always the first node
            $PathNodes = explode("/", $Path);
            if(count($PathNodes) > 0){
                return $PathNodes[0];
            }
            return '';
        }
        public static function GetRepos($Config){
            if(empty($Config))throw new \InvalidConfigException();
            $ListDirs=array();
            foreach($Config['repositories'] as $Key => $Value){
                // Trying to cast every entry into a new object instance.
                // It's easier than revalidating the config file, since the
                // config is already validated on the constructor.
                $TestObj = null;
                try{
                    $TestObj = new RepositoryBrowser($Config,$Key);
                    $ListDirs[]=array("name"=>$TestObj->FriendlyName,
                                      "type"=>'repo',
                                      "extension"=>'',
                                      "relative"=>$TestObj->Key.'/',
                                      "description"=>$TestObj->Description,
                                      "protected"=>$TestObj->isSecured(),
                                      "available"=>$TestObj->isAvailable()
                                    );
                }catch (\Exception $e) {
                    // Skip the repo
                }
            }
            return $ListDirs;
        }
    }
}
?>