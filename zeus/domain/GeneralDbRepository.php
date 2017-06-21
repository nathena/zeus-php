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
    public function add(AbstractEntity $entity)
    {
        if (null == $entity) {
            throw new IllegalArgumentException(get_class($this)." is empty ,cannot to be added");
        }

        $fields = $entity->getProperties();
        $sepc = new InsertSpecification($this->entity_schema, $fields);
        $id = $this->openSession()->execute($sepc);
        $entity->setId($id);

        return $id;
    }

    public function update(AbstractEntity $entity)
    {
        if (null == $entity) {
            throw new IllegalArgumentException("update entity had was not found");
        }

        $id = $entity->getId();
        if(empty($id)){
            throw new IllegalArgumentException("update entity not found the id key");
        }

        //update
        $sepc = new UpdateSpecification($entity->getSchema(), $entity->getData());
        $sepc->where($entity->getIdFiled(), $id);
        //cas并发
        $old_fields = $entity->getProperties();
        foreach ($old_fields as $key => $val) {
            $sepc->where($key, $val);
        }

        return $this->openSession()->execute($sepc);
    }

    public function delete(AbstractEntity $entity)
    {
        if (null == $entity || empty($entity->getId())) {
            throw new IllegalArgumentException("remove entity had was not found or not found id key");
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
    public function batch(AbstractSpecification $specification)
    {
        if (null == $specification) {
            throw new IllegalArgumentException("batch unknow specification");
        }

        if (DmlType::DML_BATCH != $specification->getDml()) {
            throw new IllegalArgumentException("specification dml : {$specification->getDml()} not support");
        }

        $this->openSession()->execute($specification);
    }

    /**
     * @return Pdo
     */
    public abstract function openSession();
}