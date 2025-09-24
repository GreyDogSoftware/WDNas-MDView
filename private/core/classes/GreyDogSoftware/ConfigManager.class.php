<?php
namespace GreyDogSoftware{
    final class ConfigManager{
        public static $ShowFullPathOnError = false;
        public static function GetConfig($Target, bool $ForceConstPath=true) {
            $TargetPath='';
            if($ForceConstPath){
                self::GetEnviroment();
                $TargetPath = realpath(CORE_CONFIG_DIR."/$Target");
                if(!file_exists($TargetPath)) throw new \Exception("Config file not found (".(self::$ShowFullPathOnError?CORE_CONFIG_DIR."/$Target":$Target).")");
            }else{
                $TargetPath = realpath($Target);
                if(!file_exists($TargetPath)) throw new \Exception("Config file not found ($Target)");
            }
            $Result = include $TargetPath;
            return $Result;
            //return false;
        }
        public static function SetConfig($Target, $Contents, $CreateNew=true, bool $ForceConstPath=true) {
            $TargetPath='';
            if($ForceConstPath){
                self::GetEnviroment();
                $TargetPath = CORE_CONFIG_DIR."/$Target";
                if(!file_exists($TargetPath)) if (!$CreateNew) throw new \Exception("Config file not found (".(self::$ShowFullPathOnError?CORE_CONFIG_DIR."/$Target":$Target).")");
            }else{
                $TargetPath = $Target;
                if(!file_exists($TargetPath)) if (!$CreateNew) throw new \Exception("Config file not found ($Target)");
            }
            if(!file_put_contents($TargetPath, '<?php return ' . var_export($Contents, true) . ';?>')) throw new \Exception("Can't save config file.");
            return true;
        }

        // TODO: This function need to be developed... this is mostly a placeholder
        public static function DeleteConfig($Target, bool $ForceConstPath=true) {
            $TargetPath='';
            if($ForceConstPath){
                self::GetEnviroment();
                $TargetPath = realpath(CORE_CONFIG_DIR."/$Target");
            }else{
                $TargetPath = realpath($Target);
            }
            if(!file_exists($TargetPath)) return false;
            unlink($TargetPath);
            return true;
        }

        private static function GetEnviroment() {
            if (!defined('CORE_CONFIG_DIR')) throw new \Exception('Config repository path not set. The CORE_CONFIG_DIR constant must be defined with a valid path.');
            if (!realpath(CORE_CONFIG_DIR)) throw new \Exception('Config repository path not found. Invalid path set in CORE_CONFIG_DIR');
            return true;
        }
    }
}
?>