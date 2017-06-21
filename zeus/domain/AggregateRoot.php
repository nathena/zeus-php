<?php
namespace zeus\domain;

use zeus\database\DbManager;
use zeus\database\pdo\Pdo;
use zeus\database\specification\AbstractSpecification;
use zeus\database\specification\DeleteSpecification;
use zeus\database\specification\InsertBatchSpecification;
use zeus\database\specification\InsertSpecification;
use zeus\database\specification\QueryRowSpecification;
use zeus\database\specification\UpdateSpecification;

abstract class AggregateRoot extends AbstractEntity
{
    public function __construct($data=null)
    {
        parent::__construct($data);
    }

    /**
     * @return Pdo
     */
    public function openSession()
    {
        return DbManager::openSession();
    }

    public function save()
    {
        $sepc = new InsertSpecification($this->schema, $this->getProperties());
        $id = $this->openSession()->execute($sepc);
        $this->setId($id);

        return $id;
    }

    public function update()
    {
        $id = $this->getId();
        if(empty($id)){
            throw new IllegalArgumentException("update entity not found the id key");
        }

        //update
        $sepc = new UpdateSpecification($this->schema, $this->getData());
        $sepc->where($this->getIdFiled(), $id);
        //cas并发
        $old_fields = $this->getProperties();
        foreach ($old_fields as $key => $val) {
            $sepc->where($key, $val);
        }

        return $this->openSession()->execute($sepc);
    }

    public function remove()
    {
        $sepc = new DeleteSpecification($this->schema);
        $sepc->where($this->getIdFiled(), $this->getId());

        return $this->openSession()->execute($sepc);
    }

    public static function get($id)
    {
        $entity = new static();
        $spec = new QueryRowSpecification();
        $spec->from($entity->getSchema());
        $spec->where($entity->getIdFiled(),$id);

        $data = $entity->openSession()->execute($spec);
        if(!empty($data)){
            $entity->setProperties($data);
            return $entity;
        }
        return null;
    }

    /**
     * @param AbstractSpecification $specification
     * @return array|null|static
     */
    protected static function load(AbstractSpecification $specification)
    {
        $entity = new static();
        $data = $entity->openSession()->execute($specification);

        if(empty($data)){
            return null;
        }

        if (DmlType::DML_SELECT_ONE) {
            $entity->setProperties($data);
            return $entity;
        }

        $result = [];
        foreach ($data as $item) {
            $entity = new static();
            $entity->setProperties($item);
            $result[] = $entity;
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
    protected static function batchCommand(AbstractSpecification $specification)
    {
        $entity = new static();
        if (DmlType::DML_BATCH == $specification->getDml()) {
            $entity->openSession()->execute($specification);
        }
    }
}
