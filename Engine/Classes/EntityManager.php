<?php

namespace Engine\Classes;


class EntityManager {
    //use AnnotationManager;
    use Hydrator;

    protected $class;
    protected $app;
    private $table;
    private $primary;
    private $entities;
    private $toDelete;
    private $metadata;
    //private $propertiesMetadata;
    /**
     * @var DbConnection
     */
    private $connection;
    private $provider;


    function __construct(App $app, $class) {
        $this->class = $class;
        $this->metadata = static::getClassAnnotation($class);

        if (!isset($this->metadata['ORM_Entity'])) {
            throw new \Exception('Class ' . $class . ' is not mapped with ORM!');
        }

        //$this->propertiesMetadata = static::getPropertiesMetadata($class);
        $this->table = $this->metadata['ORM_Table'];
        $this->primary = static::findPrimaryProperty($class);
        $this->provider = $app->getConfig()['db']['provider'];
        $this->app = $app;
        $this->toDelete = array();

        $this->entities = array();

        $this->connect();
    }

    private function connect() {
        $connClass = $this->provider;
        $connClass[0] = strtoupper($connClass[0]);
        $connClass = __NAMESPACE__ . '\\' . $connClass . 'Connection';
        $dbCfg = $this->app->getConfig()['db'];
        $this->connection = new $connClass($dbCfg['host'], $dbCfg['user'], $dbCfg['password'], $dbCfg['name']);
    }


    /**
     * @param $id
     * @return Model
     */
    function find($id) {
        $primaryColumn = static::findPrimaryColumn($this->class);
        $rawData = $this->connection->select(['*'], [$this->table], ':' . $primaryColumn . ' = ' . $id);
        if (count($rawData) > 0) {
            $model = $this->hydrateData($rawData[0], $this->class, $this->connection, $this->app);
            if (in_array($model, $this->entities, true)) {
                $this->entities[array_search($model, $this->entities)] = $model;
            } else {
                $this->entities[] = $model;
            }
            return $model;
        } else {
            return null;
        }
    }

    /**
     * @return Model[]
     */
    function findAll($orderDesc = false) {
        $models = [];
        $post = null;
        if ($orderDesc) {
            $post = 'order by :id desc';
        }
        $rawData = $this->connection->selectAll(['*'], [$this->table], $post);
        foreach ($rawData as $row) {
            $model = $this->hydrateData($row, $this->class, $this->connection, $this->app);
            $models[] = $model;
            if (in_array($model, $this->entities, true)) {
                $this->entities[array_search($model, $this->entities)] = $model;
            } else {
                $this->entities[] = $model;
            }
        }
        return $models;
    }


    function persist(Model $entity) {
        if (in_array($entity, $this->entities)) {
            $this->entities[array_search($entity, $this->entities)] = $entity;
        } else {
            $this->entities[] = $entity;
        }
    }

    function remove(Model $entity) {
        if (isset($entity)) {
            if (!in_array($entity, $this->toDelete)) {
                $this->toDelete[] = $entity;
            }
            if (in_array($entity, $this->entities)) {
                unset($this->entities[array_search($entity, $this->entities)]);
            }
        }
    }

    function flush() {

        foreach ($this->toDelete as $e) {
            $table = @static::getClassAnnotation(get_class($e))['ORM_Table'];
            if ($table && strlen($table) > 0) {
                $primaryColumn = static::findPrimaryColumn(get_class($e));
                $primaryProp = static::findPrimaryProperty(get_class($e));
                $primaryProp = new \ReflectionProperty(get_class($e), $primaryProp);
                $primaryProp->setAccessible(true);
                $primaryValue = $primaryProp->getValue($e);
                $primaryProp->setAccessible(false);
                unset($primaryProp);
                $this->connection->delete($table, [$primaryColumn => $primaryValue]);
            }
        }

        $this->toDelete = array();

        /**
         * @var $e Model
         */
        foreach ($this->entities as $e) {
            if ($e->getExists()) {
                $class = get_class($e);
                $table = @static::getClassAnnotation($class)['ORM_Table'];
                if ($table && strlen($table) > 0) {
                    $primaryColumn = static::findPrimaryColumn($class);
                    $primaryProp = static::findPrimaryProperty($class);
                    $primaryProp = new \ReflectionProperty($class, $primaryProp);
                    $primaryProp->setAccessible(true);
                    $primaryValue = $primaryProp->getValue($e);
                    $primaryProp->setAccessible(false);
                    $fields = [];
                    $meta = static::getPropertiesMetadata($class);
                    foreach ($meta as $property => $data) {
                        if ($property == $primaryProp->getName()) continue;
                        $p = new \ReflectionProperty($class, $property);
                        $p->setAccessible(true);
                        if (isset($data['ORM_Column'])) {
                            if (!isset($data['ORM_Type'])) {
                                throw new \Exception('ORM Type is not set.');
                            }
                            if ($data['ORM_Type'] == 'numeric') {
                                $fields[$data['ORM_Column']] = $p->getValue($e);
                                if (strlen($fields[$data['ORM_Column']]) <= 0) {
                                    $fields[$data['ORM_Column']] = 'NULL';
                                }
                            } elseif ($data['ORM_Type'] == 'string') {
                                if (is_null($p->getValue($e))) {
                                    $fields[$data['ORM_Column']] = 'NULL';
                                } else {
                                    $val = $this->connection->escape($p->getValue($e));
                                    $fields[$data['ORM_Column']] = '\'' . $val . '\'';
                                }
                            } elseif ($data['ORM_Type'] == 'bool') {
                                if (is_null($p->getValue($e))) {
                                    $fields[$data['ORM_Column']] = 'NULL';
                                } else {
                                    $fields[$data['ORM_Column']] = $p->getValue($e) ? '1' : '0';
                                }
                            } elseif ($data['ORM_Type'] == 'date') {
                                if (is_null($p->getValue($e))) {
                                    $fields[$data['ORM_Column']] = 'NULL';
                                } else {
                                    $val = $p->getValue($e);
                                    if ($val instanceof \DateTime) {
                                        $val = $val->format('Y-m-d');
                                    }
                                    $fields[$data['ORM_Column']] = '\'' . $val . '\'';
                                }
                            } else {
                                throw new \Exception('Wrong ORM Type: "' . $data['ORM_Type'] . '"');
                            }
                        } elseif (isset($data['ORM_ManyToOne'])) {
                            $relData = static::getRelationData($class, $p->getName(), 'ORM_ManyToOne');
                            $targetClass = 'Src\Models\\' . $relData['targetClass'];
                            $mappedByColumn = $relData['mappedBy'];
                            $targetPrimaryName = static::findPrimaryProperty($targetClass);
                            $targetPrimaryProperty = new \ReflectionProperty($targetClass, $targetPrimaryName);
                            $targetPrimaryProperty->setAccessible(true);
                            $obj = $p->getValue($e);
                            if ($obj instanceof RelationModel) $obj = $obj->get();
                            $targetPrimaryValue = $targetPrimaryProperty->getValue($obj);
                            $fields[$mappedByColumn] = $targetPrimaryValue;
                        } elseif (isset($data['ORM_OneToMany'])) {
                            // TODO: if cascade
                        } else {
                            throw new \Exception('ORM annotation is not found.');
                        }
                        unset($p);
                    }
                    $this->connection->update($fields, $table, [$primaryColumn => $primaryValue]);
                    unset($primaryProp);
                    unset($meta);
                }
            } else {
                // TODO: Добавление всех новых одним запросом
                $class = get_class($e);
                $table = @static::getClassAnnotation($class)['ORM_Table'];
                if ($table && strlen($table) > 0) {
                    $fields = [];
                    $values = [];
                    $values[0] = [];
                    $meta = static::getPropertiesMetadata($class);
                    $primaryProp = static::findPrimaryProperty($class);
                    foreach ($meta as $property => $data) {
                        if ($property == $primaryProp) continue;
                        $p = new \ReflectionProperty($class, $property);
                        $p->setAccessible(true);
                        if (isset($data['ORM_Column'])) {
                            if (!isset($data['ORM_Type'])) {
                                throw new \Exception('ORM Type is not set.');
                            }
                            if ($data['ORM_Type'] == 'numeric') {
                                $fields[] = $data['ORM_Column'];
                                $val = $p->getValue($e);
                                $values[0][] = strlen($val) > 0 ? $val : 'NULL';
                            } elseif ($data['ORM_Type'] == 'string') {
                                if (is_null($p->getValue($e))) {
                                    $fields[] = $data['ORM_Column'];
                                    $values[0][] = 'NULL';
                                } else {
                                    $val =  $this->connection->escape($p->getValue($e));
                                    $fields[] = $data['ORM_Column'];
                                    $values[0][] = '\'' . $val . '\'';
                                }
                            } elseif ($data['ORM_Type'] == 'bool') {
                                $fields[] = $data['ORM_Column'];
                                //var_dump($p->getValue($e));
                                if (is_null($p->getValue($e))) {
                                    $values[0][] = 'NULL';
                                } else {
                                    $values[0][] = $p->getValue($e) ? '1' : '0';
                                }
                            } elseif ($data['ORM_Type'] == 'date') {
                                $fields[] = $data['ORM_Column'];
                                if (is_null($p->getValue($e))) {
                                    $values[0][] = 'NULL';
                                } else {
                                    $val = $p->getValue($e);
                                    if ($val instanceof \DateTime) {
                                        $val = $val->format('Y-m-d');
                                    }
                                    $values[0][] = '\'' . $val . '\'';
                                }
                            } else {
                                throw new \Exception('Wrong ORM Type: "' . $data['ORM_Type'] . '"');
                            }
                        } elseif (isset($data['ORM_ManyToOne'])) {
                            $relData = static::getRelationData($class, $p->getName(), 'ORM_ManyToOne');
                            $targetClass = 'Src\Models\\' . $relData['targetClass'];
                            $mappedByColumn = $relData['mappedBy'];
                            $targetPrimaryName = static::findPrimaryProperty($targetClass);
                            $targetPrimaryProperty = new \ReflectionProperty($targetClass, $targetPrimaryName);
                            $targetPrimaryProperty->setAccessible(true);
                            $obj = $p->getValue($e);
                            if ($obj instanceof RelationModel) $obj = $obj->get();
                            $targetPrimaryValue = $targetPrimaryProperty->getValue($obj);
                            $fields[] = $mappedByColumn;
                            $values[0][] = $targetPrimaryValue;
                        } elseif (isset($data['ORM_OneToMany'])) {
                            // TODO: if cascade
                        } else {
                            throw new \Exception('ORM annotation is not found.');
                        }
                        unset($p);

                    }
                    /*var_dump('--------------');
                    var_dump($table);
                    var_dump($fields);
                    var_dump($values);*/
                    $this->connection->insert($table, $fields, $values);
                    $primaryName = static::findPrimaryProperty($class);
                    $primaryProperty = new \ReflectionProperty($class, $primaryName);
                    $primaryProperty->setAccessible(true);
                    $primaryValue = $this->connection->query('SELECT LAST_INSERT_ID() as :last_id;');
                    $primaryValue = $primaryValue[0]['last_id'];
                    $primaryProperty->setValue($e, $primaryValue);
                    unset($meta);
                    unset($primaryProperty);
                }
            }
        }

        $this->entities = [];
    }


} 