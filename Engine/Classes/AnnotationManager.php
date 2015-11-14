<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 01.09.2015
 * Time: 19:52
 */

namespace Engine\Classes;


trait AnnotationManager {
    /**
     * @param $className
     * @return array
     */
    private static function getClassAnnotation($className) {
        $r = new \ReflectionClass($className);
        $doc = $r->getDocComment();
        preg_match_all('/@([\w]*?)\((.*?)\)/s', $doc, $annotations);
        $a = [];
        for ($i = 0; $i < count($annotations[0]); $i++) {
            $a[$annotations[1][$i]] = $annotations[2][$i];
        }
        return $a;
    }

    /**
     * @param $className
     * @param $propertyName
     * @return array
     */
    private static function getPropertyAnnotation($className, $propertyName) {
        $r = new \ReflectionProperty($className, $propertyName);
        $doc = $r->getDocComment();
        preg_match_all('/@([\w]*?)\((.*?)\)/s', $doc, $annotations);
        $a = [];
        for ($i = 0; $i < count($annotations[0]); $i++) {
            $a[$annotations[1][$i]] = $annotations[2][$i];
        }
        return $a;
    }

    /**
     * @param $className
     * @return array
     */
    private static function getPropertiesMetadata($className) {
        $r = new \ReflectionClass($className);
        $props = $r->getProperties();
        $meta = array();
        foreach ($props as $p) {
            $m = static::getPropertyAnnotation($className, $p->getName());
            if (count($m) > 0)
                $meta[$p->getName()] = $m;
        }
        return $meta;
    }

    /**
     * @param \ReflectionMethod $method
     * @internal param $className
     * @internal param $methodName
     * @return array
     */
    private static function getMethodAnnotation(\ReflectionMethod $method) {
        $doc = $method->getDocComment();
        preg_match_all('/@([\w]*?)\((.*?)\)/s', $doc, $annotations);
        $a = [];
        for ($i = 0; $i < count($annotations[0]); $i++) {
            $a[$annotations[1][$i]] = $annotations[2][$i];
        }
        return $a;
    }

    /**
     * @param $className
     * @throws \Exception
     * @return string
     */
    private static function findPrimaryProperty($className) {
        $r = new \ReflectionClass($className);
        $properties = $r->getProperties();
        foreach ($properties as $p) {
            $meta = static::getPropertyAnnotation($className, $p->getName());
            if (isset($meta['ORM_Id'])) {
                return $p->getName();
            }
        }
        throw new \Exception('Model class ' . $className . ' does not have the primary key!');
    }

    /**
     * @param $className
     * @throws \Exception
     * @return string
     */
    private static function findPrimaryColumn($className) {
        $r = new \ReflectionClass($className);
        $properties = $r->getProperties();
        foreach ($properties as $p) {
            $meta = static::getPropertyAnnotation($className, $p->getName());
            if (isset($meta['ORM_Id'])) {
                return $meta['ORM_Column'];
            }
        }
        throw new \Exception('Model class ' . $className . ' does not have the primary key!');
    }

    private static function getRelationData($className, $fieldName, $relation) {
        $meta = static::getPropertyAnnotation($className, $fieldName);
        $_data = preg_split('/[\s]*,[\s]*/', $meta[$relation]);
        $data = [];
        foreach ($_data as $param) {
            preg_match_all('/([\w]+)[\s]*=[\s]*"(.+?)"/s', $param, $matches);
            $data[$matches[1][0]] = $matches[2][0];
        }
        return $data;
    }
} 