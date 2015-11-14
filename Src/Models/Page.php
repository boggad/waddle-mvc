<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 01.09.2015
 * Time: 19:08
 */

namespace Src\Models;


use Engine\Classes\Model;

/**
 * Class Page
 * @package Src\Models
 * @ORM_Entity()
 * @ORM_Table(pages)
 */
class Page extends Model {

    /**
     * @var int
     * @ORM_Id()
     * @ORM_Column(id)
     * @ORM_Type(numeric)
     */
    private $id;

    /**
     * @var string
     * @ORM_Column(url)
     * @ORM_Type(string)
     */
    private $url;

    /**
     * @var string
     * @ORM_Column(title)
     * @ORM_Type(string)
     */
    private $title;

    /**
     * @var string
     * @ORM_Column(html)
     * @ORM_Type(string)
     */
    private $html;

    /**
     * @return string
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }
} 