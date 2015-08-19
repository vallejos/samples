<?php

/**
 * Description of User
 *
 * @author kamus
 */
class User {
    private $id;
    private $username;

    function __construct($id, $username) {
        $this->id = $id;
        $this->username = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->name = $username;
    }



}
?>
