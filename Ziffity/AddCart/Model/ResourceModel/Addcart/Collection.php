<?php
namespace Ziffity\AddCart\Model\ResourceModel\Addcart;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ziffity\AddCart\Model\Addcart as Model;
use Ziffity\AddCart\Model\ResourceModel\Addcart as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
        //parent::__construct();

    }
}

