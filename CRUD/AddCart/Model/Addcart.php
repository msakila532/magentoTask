<?php

namespace CRUD\AddCart\Model;

use Magento\Framework\Model\AbstractModel;
use CRUD\AddCart\Model\ResourceModel\Addcart as ResourceModel;

class Addcart extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
