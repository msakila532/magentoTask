<?php

namespace Ziffity\AddCart\Model;

use Ziffity\AddCart\Api\EditCartInterface;
use Ziffity\AddCart\Api\DataInterfaceFactory;
use Ziffity\AddCart\Model\AddcartFactory as CartModel;
use Ziffity\AddCart\Model\ResourceModel\Addcart as CartResource;
use Ziffity\AddCart\Model\ResourceModel\Addcart\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class EditCartRepository implements EditCartInterface
{
    /**
     * @var DataInterfaceFactory
     */

    private $dataInterfaceFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CartModel
     */
    private $model;

    /**
     * @var CartResource
     */

    private $resource;

    public function __construct(
        CollectionFactory $collectionFactory,
        DataInterfaceFactory $dataInterfaceFactory,
        CartModel $model,
        CartResource $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataInterfaceFactory = $dataInterfaceFactory;
        $this->model = $model;
        $this->resource = $resource;
    }

    /**
     * @param int $id
     * @param string $sku
     * @param int $quoteId
     * @param int $customerId
     * @param $createdAt
     * @return \Ziffity\AddCart\Api\DataInterface[]
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateCart($id, string $sku = null, int $quoteId = null, int $customerId = null, $createdAt = null)
    {
        $model = $this->model->create();
        $this->resource->load($model, $id, 'id');
        if(!$model->getData()){
            return ['success' => 'ID is not Available'];
        }
        if ($sku != null) {
            $model->setSku($sku);
        }

        if ($quoteId != null) {
            $model->setQuoteId($quoteId);
        }

        if ($customerId != null) {
            $model->setCustomerId($customerId);
        }

        if ($createdAt != null) {
            $model->setCreatedAt($createdAt);
        }
        try {
            $this->resource->save($model);
            return ['success' => 'Updated Successfully'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
