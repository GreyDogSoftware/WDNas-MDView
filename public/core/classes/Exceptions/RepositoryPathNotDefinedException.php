<?php
class RepositoryPathNotDefinedException extends Exception{
    // Redefinir la excepción para que el mensaje no sea opcional.
    public function __construct() {
        parent::__construct('Repository path is not defined.', 0);
    }
}
?>