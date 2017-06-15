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
    private static $_instances = [];

    protected $pdo;
    protected $entity_schema;

    public static function getSchema($schema)
    {
        $schema = strtolower(trim($schema));
        if (!isset(self::$_instances[$schema])) {
            self::$_instances[$schema] = new static($schema);
        }
        return self::$_instances[$schema];
    }

    protected function __construct($schema)
    {
        $this->pdo = DbManager::openSession();
        $this->entity_schema = $schema;
    }

    public function save(AbstractEntity $entity)
    {
        if (null == $entity) {
            throw new IllegalArgumentException("DbRepository save not found entity");
        }

        $id = $entity->getId();

        if (!empty($id)) {
            //update
            $fields = $entity->getUpdatedData();

            $sepc = new UpdateSpecification($this->entity_schema, $fields);
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

        $sepc = new DeleteSpecification($this->entity_schema);
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

        $list = [];
        $data = $this->pdo->execute($specification);
        if (!empty($data)) {
            if (DmlType::DML_SELECT_ONE) {
                $data = [$data];
            }
            foreach ($data as $item) {
                $list[] = new $class($item);
            }
        }
        return $list;
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