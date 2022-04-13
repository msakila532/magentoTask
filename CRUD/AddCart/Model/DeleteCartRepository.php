<?php

namespace CRUD\AddCart\Model;

use CRUD\AddCart\Api\DeleteCartInterface;
use CRUD\AddCart\Api\DataInterfaceFactory;
use CRUD\AddCart\Model\AddcartFactory as CartModel;
use CRUD\AddCart\Model\ResourceModel\Addcart as CartResource;
use CRUD\AddCart\Model\ResourceModel\Addcart\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class DeleteCartRepository implements DeleteCartInterface
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
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function deleteCartById(int $id)
    {
        $model = $this->model->create();
        $this->resource->load($model, $id, 'id');

        try {
            $this->resource->delete($model);
            $response = ['success' => 'Deleted Successfully'];
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
