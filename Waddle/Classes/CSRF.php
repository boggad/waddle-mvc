<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 02.09.2015
 * Time: 22:58
 */

namespace Waddle\Classes;


class CSRF {
    private $lifetime;
    private $idLifetime;

    private $token;

    public function __construct($lifetime = 43200) {
        $this->lifetime = $lifetime;
        $this->idLifetime = 3600*72;

        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > $this->idLifetime) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }

        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            $this->refreshToken();
        } elseif ((time() - $_SESSION['last_activity']) > $this->lifetime) {
            /*session_unset();
            session_destroy();*/
            $_SESSION['last_activity'] = time();
            $this->refreshToken();
        } else {
            $this->token = $_SESSION['csrf_token'];
        }
    }

    /**
     * @return string
     */
    public function getToken() {
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > $this->idLifetime) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }

        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            $this->refreshToken();
        } elseif ((time() - $_SESSION['last_activity']) > $this->lifetime) {
            /*session_unset();
            session_destroy();*/
            $_SESSION['last_activity'] = time();
            $this->refreshToken();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * @param $token
     * @return bool
     */
    public function verifyToken($token) {
        return $this->getToken() === $token;
    }

    public function refreshToken() {
        $this->token = hash('sha256', uniqid(mt_rand(), true));
        $this->token = base64_encode($this->token . crc32($this->token));
        $_SESSION['csrf_token'] = $this->token;
    }
} 