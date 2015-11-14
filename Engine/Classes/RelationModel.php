<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 29.08.2015
 * Time: 22:45
 */

namespace Engine\Classes;


interface RelationModel {
    public function __construct(Model $context, array $meta, App $app);
    /**
     * @return Model
     */
    public function get();
} 