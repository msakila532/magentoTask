<?php

namespace CRUD\AddCart\Model;

use CRUD\AddCart\Api\SaveCartInterface;
use CRUD\AddCart\Api\DataInterfaceFactory;
use CRUD\AddCart\Model\AddcartFactory as CartModel;
use CRUD\AddCart\Model\ResourceModel\Addcart as CartResource;
use CRUD\AddCart\Model\ResourceModel\Addcart\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class SaveCartRepository implements SaveCartInterface
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
     * @param string $sku
     * @param int $quoteId
     * @param int $customerId
     * @param $createdAt
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function saveCart(string $sku, int $quoteId, int $customerId = null, $createdAt = null)
    {
        $model = $this->model->create();
        $model->setSku($sku);
        $model->setQuoteId($quoteId);
        $model->setCustomerId($customerId);

        if ($createdAt != null) {
            $model->setCreatedAt($createdAt);
        }
        try {
            $this->resource->save($model);
            $response = ['success' => 'Saved Successfully'];
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
