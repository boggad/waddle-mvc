<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 01.09.2015
 * Time: 19:28
 */

namespace Waddle\Classes;


class QueryBuilder {
    use Hydrator;

    const SELECT = 1;
    const UPDATE = 2;
    const INSERT = 3;
    const DELETE = 4;

    /**
     * @var DbConnection
     */
    private $conn;

    /**
     * @var App
     */
    private $app;

    private $queryType = 0;
    private $class;
    /**
     * @var Model[]
     */
    private $modelsToUpdate;

    /**
     * @var Model[]
     */
    private $modelsToInsert;

    /**
     * @var Model[]
     */
    private $modelsToDelete;
    private $whereClause;
    private $post;

    public function __construct(App $app) {
        $this->app = $app;
        $dbConfig = $app->getConfig()['db'];
        $provider = strtolower($dbConfig['provider']);
        $provider[0] = strtoupper($provider[0]);
        $connectionClass = __NAMESPACE__ . '\\' . $provider . 'Connection';
        $this->conn =
            new $connectionClass($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['name']);
    }

    /**
     * @param $class
     * @return QueryBuilder
     */
    public function select($class) {
        $this->whereClause = null;
        $this->post = null;
        $this->queryType = QueryBuilder::SELECT;
        $this->class = $class;
        return $this;
    }

    /**
     * @param array $models
     * @return QueryBuilder
     */
    public function update(array $models) {
        $this->whereClause = null;
        $this->post = null;
        $this->queryType = QueryBuilder::UPDATE;
        $this->modelsToUpdate = $models;
        return $this;
    }

    /**
     * @param array $models
     * @return QueryBuilder
     */
    public function insert(array $models) {
        $this->whereClause = null;
        $this->post = null;
        $this->queryType = QueryBuilder::INSERT;
        $this->modelsToInsert = $models;
        return $this;
    }

    /**
     * @param array $models
     * @return QueryBuilder
     */
    public function delete(array $models) {
        $this->whereClause = null;
        $this->post = null;
        $this->queryType = QueryBuilder::DELETE;
        $this->modelsToDelete = $models;
        return $this;
    }

    /**
     * @param string $clause
     * @return QueryBuilder
     */
    public function where($clause) {
        $this->post = null;
        $this->whereClause = $clause;
        return $this;
    }

    /**
     * @param $clause
     * @return QueryBuilder
     */
    public function post($clause) {
        $this->post = null;
        $this->post = $clause;
        return $this;
    }

    /**
     * @throws \Exception
     * @return Model[]|int
     */
    public function query() {
        switch ($this->queryType) {
            case QueryBuilder::SELECT:
                $this->class = 'Src\Models\\' . $this->class;
                $meta = static::getClassAnnotation($this->class);
                if (!isset($meta['ORM_Entity'])) {
                    throw new \Exception('Class "' . $this->class .'" is not mapped with ORM.');
                }
                $table = $meta['ORM_Table'];
                $rawResult = $this->conn->select(['*'], [$table], $this->whereClause, $this->post);
                $models = [];
                foreach ($rawResult as $raw) {
                    $models[] = $this->hydrateData($raw, $this->class, $this->conn, $this->app);
                }
                return $models;
                break;
            case QueryBuilder::UPDATE:
                /**
                 * @var $model Model
                 */
                $affected = 0;
                foreach($this->modelsToUpdate as $model) {
                    if (!$model->getExists()) continue;
                    $class = get_class($model);
                    $table = @static::getClassAnnotation($class)['ORM_Table'];
                    if ($table && strlen($table) > 0) {
                        $primaryColumn = static::findPrimaryColumn($class);
                        $primaryProp = static::findPrimaryProperty($class);
                        $primaryProp = new \ReflectionProperty($class, $primaryProp);
                        $primaryProp->setAccessible(true);
                        $primaryValue = $primaryProp->getValue($model);
                        $primaryProp->setAccessible(false);
                        $fields = [];
                        $meta = static::getPropertiesMetadata($class);
                        foreach($meta as $property => $data) {
                            if ($property == $primaryProp->getName()) continue;
                            $p = new \ReflectionProperty($class, $property);
                            $p->setAccessible(true);
                            if (isset($data['ORM_Column'])) {
                                if (!isset($data['ORM_Type'])) {
                                    throw new \Exception('ORM Type is not set.');
                                }
                                if ($data['ORM_Type'] == 'numeric') {
                                    $fields[$data['ORM_Column']] = $p->getValue($model);
                                } elseif ($data['ORM_Type'] == 'string') {
                                    $fields[$data['ORM_Column']] = '\'' . $p->getValue($model) . '\'';
                                } else {
                                    throw new \Exception('Wrong ORM Type: "' . $data['ORM_Type'] . '"');
                                }
                            } elseif (isset($data['ORM_ManyToOne'])) {
                                $relData = static::getRelationData($class, $p->getName(), 'ORM_ManyToOne');
                                $targetClass = $relData['targetClass'];
                                $mappedByColumn = $relData['mappedBy'];
                                $targetPrimaryName = static::findPrimaryProperty($targetClass);
                                $targetPrimaryProperty = new \ReflectionProperty($targetClass, $targetPrimaryName);
                                $targetPrimaryProperty->setAccessible(true);
                                $targetPrimaryValue = $targetPrimaryProperty->getValue($p->getValue($model));
                                $fields[$mappedByColumn] = $targetPrimaryValue;
                            } elseif (isset($data['ORM_OneToMany'])) {
                                // TODO: if cascade
                            } else {
                                throw new \Exception('ORM annotation is not found.');
                            }
                            unset($p);
                        }
                        $affected += $this->conn->update($fields, $table, [$primaryColumn => $primaryValue]);
                        unset($primaryProp);
                        unset($meta);
                    }
                }
                return $affected;
                break;

            case QueryBuilder::INSERT:
                /**
                 * @var $model Model
                 */
                $affected = 0;
                foreach($this->modelsToInsert as $model) {
                    //if ($model->getExists()) continue;
                    // TODO: Добавление всех новых одним запросом
                    $class = get_class($model);
                    $table = @static::getClassAnnotation($class)['ORM_Table'];
                    if ($table && strlen($table) > 0) {
                        $fields = [];
                        $values = [];
                        $values[0] = [];
                        $meta = static::getPropertiesMetadata($class);
                        $primaryProp = static::findPrimaryProperty($class);
                        foreach($meta as $property => $data) {
                            if ($property == $primaryProp) continue;
                            $p = new \ReflectionProperty($class, $property);
                            $p->setAccessible(true);
                            if (isset($data['ORM_Column'])) {
                                if (!isset($data['ORM_Type'])) {
                                    throw new \Exception('ORM Type is not set.');
                                }
                                if ($data['ORM_Type'] == 'numeric') {
                                    $fields[] = $data['ORM_Column'];
                                    $values[0][] = $p->getValue($model);
                                } elseif ($data['ORM_Type'] == 'string') {
                                    $fields[] = $data['ORM_Column'];
                                    $values[0][] = '\'' . $p->getValue($model) . '\'';
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
                                $targetPrimaryValue = $targetPrimaryProperty->getValue($p->getValue($model));
                                $fields[] = $mappedByColumn;
                                $values[0][] = $targetPrimaryValue;
                            } elseif (isset($data['ORM_OneToMany'])) {
                                // TODO: if cascade
                            } else {
                                throw new \Exception('ORM annotation is not found.');
                            }
                            unset($p);
                        }
                        $affected += $this->conn->insert($table, $fields, $values);
                        unset($meta);
                    }
                }
                return $affected;
                break;

            case QueryBuilder::DELETE:
                $affected = 0;
                foreach ($this->modelsToDelete as $model) {
                    if (!$model->getExists()) continue;
                    $c = get_class($model);
                    $table = @static::getClassAnnotation($c)['ORM_Table'];
                    if ($table && strlen($table) > 0) {
                        $primaryColumn = static::findPrimaryColumn($c);
                        $primaryProp = static::findPrimaryProperty($c);
                        $primaryProp = new \ReflectionProperty($c, $primaryProp);
                        $primaryProp->setAccessible(true);
                        $primaryValue = $primaryProp->getValue($model);
                        $primaryProp->setAccessible(false);
                        unset($primaryProp);
                        $affected += $this->conn->delete($table, [$primaryColumn => $primaryValue]);
                    }
                }
                return $affected;
                break;

            default:
                throw new \Exception('No query type specified.');
        }
    }


} 