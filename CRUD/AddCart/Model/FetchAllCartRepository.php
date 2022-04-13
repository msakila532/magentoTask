<?php

namespace CRUD\AddCart\Model;

use CRUD\AddCart\Api\FetchAllCartInterface;
use CRUD\AddCart\Api\DataInterfaceFactory;
use CRUD\AddCart\Model\ResourceModel\Addcart\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class FetchAllCartRepository implements FetchAllCartInterface
{
    /**
     * @var DataInterfaceFactory
     */

    private $dataInterfaceFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        dataInterfaceFactory $dataInterfaceFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataInterfaceFactory = $dataInterfaceFactory;
    }


    /**
     * @param int|null $pageId
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function getCartList(int $pageId = null)
    {
        if ($pageId == null) {
            $pageId = 1;
        }
        $data = [];
        try {
            $cartCollection = $this->collectionFactory->create()->setPageSize(5)->setCurPage($pageId);

            foreach ($cartCollection as $item) {
                $dataInterface = $this->dataInterfaceFactory->create();
                $dataInterface->setId($item->getId());
                $dataInterface->setSku($item->getSku());
                $dataInterface->setCustomerId($item->getCustomerId());
                $dataInterface->setQuoteId($item->getQuoteId());
                $dataInterface->setCreatedAt($item->getCreatedAt());
                $data[] = $dataInterface;
            }
            return $data;
        } catch (LocalizedException $e) {
            throw LocalizedException(__('No data found'));
        }
    }
}
