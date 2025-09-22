<?php
class AuthorizationRequiredException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('This actions needs authentication.', 0);
    }
}
?>