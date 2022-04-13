<?php

namespace CRUD\AddCart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Addcart extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tbl_addcart_crud', 'id');
    }
}

