<?php
class NoAuthorizationProvidedException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('Authorization is needed for this action. But no token provided.', 0);
    }
}
?>