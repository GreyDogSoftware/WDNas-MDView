<?php
class InvalidConfigException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('The config file is invalid. Maybe is malformed, or the config is empty.', 1);
    }
}
?>