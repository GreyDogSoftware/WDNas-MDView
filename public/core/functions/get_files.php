<?php

function get_files($SearchPath, $Config): array{
    if(empty($Config))die("No config");

    $NewPath = resolvePath($SearchPath, $Config);
    //echo '<pre>';
    //var_dump($NewPath);
    //echo '</pre>';
    //die("");
    if(!$NewPath) {
        return [];
    }else{
        $ListDirs=array();
        $ListFile=array();
        //echo $SearchPath."</br>";
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
