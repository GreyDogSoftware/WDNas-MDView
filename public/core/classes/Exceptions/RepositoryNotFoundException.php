<?php
class RepositoryNotFoundException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('Repository key not found in config.', 10);
    }
}
?>