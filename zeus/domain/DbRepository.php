<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/14
 * Time: 20:37
 */

namespace zeus\domain;


use zeus\base\exception\IllegalArgumentException;
use zeus\database\DbManager;
use zeus\database\DmlType;
use zeus\database\specification\AbstractSpecification;
use zeus\database\specification\DeleteSpecification;
use zeus\database\specification\InsertBatchSpecification;
use zeus\database\specification\InsertSpecification;
use zeus\database\specification\UpdateSpecification;

class DbRepository
{
    private static $_instances;
    protected $pdo;

    /**
     * @param $schema
     * @return DbRepository
     */
    public static function getInstance()
    {
        if (!isset(self::$_instances)) {
            self::$_instances = new static();
        }
        return self::$_instances;
    }

    protected function __construct()
    {
        $this->pdo = DbManager::openSession();
    }

    public function save(AbstractEntity $entity)
    {
        if (null == $entity) {
            throw new IllegalArgumentException("DbRepository save not found entity");
        }

        $id = $entity->getId();
        $schema = $entity->getSchema();

        if (!empty($id)) {
            //update
            $fields = $entity->getData();
            $sepc = new UpdateSpecification($schema, $fields);
            $sepc->where($entity->getIdFiled(), $id);
            //cas并发
            $old_fields = $entity->getProperties();
            foreach ($old_fields as $key => $val) {
                $sepc->where($key, $val);
            }

            return $this->pdo->execute($sepc);
        }

        $fields = $entity->getProperties();
        $sepc = new InsertSpecification($this->entity_schema, $fields);
        $id = $this->pdo->execute($sepc);
        $entity->setId($id);

        return $id;
    }

    public function remove(AbstractEntity $entity)
    {
        if (null == $entity || empty($entity->getId())) {
            throw new IllegalArgumentException("DbRepository remove not found entity");
        }

        $sepc = new DeleteSpecification($schema = $entity->getSchema());
        $sepc->where($entity->getIdFiled(), $entity->getId());

        return $this->pdo->execute($sepc);
    }

    public function load($class, AbstractSpecification $specification)
    {
        if (null == $specification) {
            throw new IllegalArgumentException("DbRepository load : specification");
        }

        if (!class_exists($class)) {
            throw new IllegalArgumentException("DbRepository class {$class} not found");
        }

        $result = [];
        $data = $this->pdo->execute($specification);
        if (!empty($data)) {
            if (DmlType::DML_SELECT_ONE) {
                $data = [$data];
                foreach ($data as $item) {
                    $result[] = new $class($item);
                }
            } else {
                $result = $data;
            }
        }
        return $result;
    }

    /**
     *
     * UpdateBatch extends AbstractSpecification {
     *      //TDOO;; setSql,setParams,dml
     * }
     *
     * RemoveBach the same as UpdateBatch
     *
     * @see InsertBatchSpecification
     * @param AbstractSpecification $specification
     */
    public function updateBatch(AbstractSpecification $specification)
    {
        if (null == $specification) {
            throw new IllegalArgumentException("DbRepository updateBatch : specification");
        }

        if (DmlType::DML_BATCH != $specification->getDml()) {
            throw new IllegalArgumentException("DbRepository updateBatch : {$specification->getDml()} not support");
        }

        $this->pdo->execute($specification);
    }
}