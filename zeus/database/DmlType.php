<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 2017/6/7 0007
 * Time: 14:16
 */

namespace zeus\database;

class DmlType
{
    const DML_SELECT_LIST   = "select_list";
    const DML_SELECT_ONE    = "select_one";
    const DML_INSERT        = "INSERT";
    const DML_INSERT_BATCH  = "INSERT_BATCH";
    const DML_UPDATE        = "UPDATE";
    const DML_DELETE        = "DELETE";
}