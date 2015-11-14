<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 01.09.2015
 * Time: 19:58
 */

namespace Engine\Classes;


trait Hydrator {
    use AnnotationManager;

    /**
     * @param array $rawData
     * @param string $className
     * @param DbConnection $connection
     * @param App $app
     * @throws \Exception
     * @return Model
     */
    private function hydrateData(array $rawData, $className, DbConnection $connection, App $app) {
        $hydrator = new \ReflectionClass($className);
        $model = new $className;
        $propertiesMetadata = static::getPropertiesMetadata($className);
        foreach ($propertiesMetadata as $property => $meta) {
            $p = $hydrator->getProperty($property);
            $p->setAccessible(true);
            if (isset($meta['ORM_Column'])) {

                if ($meta['ORM_Type'] == 'date') {
                    $p->setValue($model, new \DateTime($rawData[$meta['ORM_Column']]));
                } else {
                    $p->setValue($model, $rawData[$meta['ORM_Column']]);
                }

            } elseif (isset($meta['ORM_ManyToOne'])) {
                $_data = preg_split('/[\s]*,[\s]*/', $meta['ORM_ManyToOne']);
                $data = [];
                foreach ($_data as $param) {
                    preg_match_all('/([\w]+)[\s]*=[\s]*"(.+?)"/s', $param, $matches);
                    $data[$matches[1][0]] = $matches[2][0];
                }
                $mappedByValue = $rawData[$data['mappedBy']];
                $data['mappedByValue'] = $mappedByValue;
                $data['property'] = $p->getName();
                $p->setValue($model, new ManyToOneRelationModel($model, $data, $app));
            } elseif (isset($meta['ORM_OneToMany'])) {
                $_data = preg_split('/[\s]*,[\s]*/', $meta['ORM_OneToMany']);
                $data = [];
                foreach ($_data as $param) {
                    preg_match_all('/([\w]+)[\s]*=[\s]*"(.+?)"/s', $param, $matches);
                    $data[$matches[1][0]] = $matches[2][0];
                }

                $targetClass = 'Src\Models\\' . $data['targetClass'];
                $targetTable = static::getClassAnnotation($targetClass)['ORM_Table'];
                $inversedBy = $data['inversedBy'];
                $primaryColumn = static::findPrimaryColumn($className);
                $id = $rawData[$primaryColumn];
                $rawArray = $connection->select(['*'], [$targetTable], ':' . $inversedBy . '=' . $id);
                $models = [];
                foreach ($rawArray as $row) {
                    $models[] = $this->hydrateData($row, $targetClass, $connection, $app);
                }
                $p->setValue($model, $models);
            }
            $p->setAccessible(false);
        }
        $ep = $hydrator->getProperty('_exists');
        $ep->setAccessible(true);
        $ep->setValue($model, true);
        $ep->setAccessible(false);
        return $model;
    }
} 