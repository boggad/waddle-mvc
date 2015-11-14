<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 29.08.2015
 * Time: 23:00
 */

namespace Engine\Classes;


class ManyToOneRelationModel implements RelationModel  {

    /**
     * @var Model
     */
    private $context;
    private $meta;
    /**
     * @var App
     */
    private $app;

    public function __construct(Model $context, array $meta, App $app) {
        $this->context = $context;
        $this->meta = $meta;
        $this->app = $app;
    }

    /**
     * @return Model
     */
    public function get() {
        $class = $this->meta['targetClass'];
        $id = $this->meta['mappedByValue'];
        $em = $this->app->getEntityManager($class);
        $r = new \ReflectionProperty(get_class($this->context), $this->meta['property']);
        $r->setAccessible(true);
        $model = $em->find($id);
        $r->setValue($this->context, $model);
        return $model;
    }
}