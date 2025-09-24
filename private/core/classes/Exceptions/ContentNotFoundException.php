<?php
class ContentNotFoundException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('File source not found.', 0);
    }
}
?>