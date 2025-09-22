<?php

function get_files($SearchPath, $Config): array{
    if(empty($Config))throw new InvalidConfigException();

    $NewPath = resolvePath($SearchPath, $Config);
    if(!$NewPath) {
        return [];
    }else{
        $AuthResolved = false;
        $NeedsAuth = true;
        try{
            $NeedsAuth = check_auth($NewPath['nodeName'],$Config);
        }catch (Exception $e) {
            $AuthResolved = false;
        }
        if ($NeedsAuth){
            try{
                $AuthCheck = get_auth($NewPath['nodeName'],$Config);
                if ($AuthCheck) $AuthResolved = true;
            }catch (Exception $e) {
                $AuthResolved = false;
            }
        }else{
            $AuthResolved = true;
        }

        $Result = array();
        if ($AuthResolved){
            $ListDirs=array();
            $ListFile=array();

            $dir = new DirectoryIterator($NewPath['fullPath']);
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {

                    $FileName = $fileinfo->getFilename();
                    $FileExten= ($fileinfo->isDir())?NULL:strtolower($fileinfo->getExtension());
                    $FileType = ($fileinfo->isDir())?'dir':'file';

                    if($fileinfo->isDir()){
                        $ListDirs[]=array("file_name"=>$FileName,
                                        "file_type"=>$FileType,
                                        "file_ext"=>$FileExten,
                                        "path_rela"=>$NewPath['relativePath'].$FileName.'/');
                    }else{
                        if(in_array($FileExten,$Config['allowed_extensions'])){
                            $ListFile[]=array("file_name"=>$FileName,
                                            "file_type"=>$FileType,
                                            "file_ext"=>$FileExten,
                                            "path_rela"=>$NewPath['relativePath'].$FileName);
                        }
                    }
                }
            }
            asort($ListDirs);
            asort($ListFile);
            $Result = array_merge($ListDirs,$ListFile);
        }else{
            throw new AuthorizationRequiredException();
        }

        if (count($Result)==0){
            // No files or folders returned
            $Result[]=array("file_name"=>'empty_folder',
                            "file_type"=>'empty',
                            "file_ext"=>'',
                            "path_full"=>'',
                            "path_rela"=>'',
                            "path_web"=>'');
        }
        return $Result;
    }

}
?>
