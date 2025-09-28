<?php
class InvalidAuthorizationTokenException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('Invalid authorization token.', 31);
    }
}
?>