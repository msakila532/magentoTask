<?php

namespace Ziffity\AddCart\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Ziffity\AddCart\Model\AddcartFactory as CartModel;
use Ziffity\AddCart\Model\ResourceModel\Addcart as CartResource;

class Addcart
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CartModel
     */
    protected $model;

    /**
     * @var CartResource
     */
    protected $resource;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer,
        CartModel $model,
        CartResource $resource
    ) {
        $this->serializer = $serializer;
        $this->model = $model;
        $this->resource = $resource;
    }

    /**
     * @param $data
     * @return void
     */
    public function consume($data)
    {
        $model = $this->model->create();
        $cartDetails = $this->serializer->unserialize($data);
        $model->setData($cartDetails);
        try {
            $this->resource->save($model);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
