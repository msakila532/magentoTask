<?php
namespace CRUD\AddCart\Model\ResourceModel\Addcart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use CRUD\AddCart\Model\Addcart as Model;
use CRUD\AddCart\Model\ResourceModel\Addcart as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
        parent::_construct();

    }
}

