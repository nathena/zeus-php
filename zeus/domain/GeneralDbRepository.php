<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/14
 * Time: 20:37
 */

namespace zeus\domain;

use zeus\base\exception\IllegalArgumentException;
use zeus\database\DmlType;
use zeus\database\pdo\Pdo;
use zeus\database\specification\AbstractSpecification;
use zeus\database\specification\DeleteSpecification;
use zeus\database\specification\InsertBatchSpecification;
use zeus\database\specification\InsertSpecification;
use zeus\database\specification\UpdateSpecification;

abstract class GeneralDbRepository
{
    public function save(AbstractEntity $entity)
    {
        if (null == $entity) {
            throw new IllegalArgumentException("DbRepository save not found entity");
        }

        $id = $entity->getId();
        $schema = $entity->getSchema();

        $pdo = $this->openSession();
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

            return $pdo->execute($sepc);
        }

        $fields = $entity->getProperties();
        $sepc = new InsertSpecification($this->entity_schema, $fields);
        $id = $pdo->execute($sepc);
        $entity->setId($id);

        return $id;
    }

    public function remove(AbstractEntity $entity)
    {
        if (null == $entity || empty($entity->getId())) {
            throw new IllegalArgumentException("DbRepository remove not found entity");
        }

        $sepc = new DeleteSpecification($entity->getSchema());
        $sepc->where($entity->getIdFiled(), $entity->getId());

        return $this->openSession()->execute($sepc);
    }

    public function load(AbstractSpecification $specification)
    {
        if (null == $specification) {
            throw new IllegalArgumentException("DbRepository load : specification");
        }

        return $this->openSession()->execute($specification);
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

        $this->openSession()->execute($specification);
    }

    /**
     * @return Pdo
     */
    public abstract function openSession();
}