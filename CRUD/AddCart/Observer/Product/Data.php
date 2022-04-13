<?php

namespace CRUD\AddCart\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use CRUD\AddCart\Model\AddcartFactory as CartModel;
use CRUD\AddCart\Model\ResourceModel\Addcart as CartResource;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use CRUD\AddCart\Publisher\Addcart;

class Data implements ObserverInterface
{

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var CartModel
     */
    protected $model;
    /**
     * @var Addcart
     */
    protected $publisher;

    /**
     * @var CartResource
     *
     */

    protected $resource;

    /**
     * @param CartModel $model
     * @param CartResource $resource
     * @param Context $httpContext
     * @param Addcart $publisher
     */
    protected $_logger;
    private $checkoutSession;

    public function __construct(
        \Psr\Log\LoggerInterface $_logger,
        CartModel $model,
        CartResource  $resource,
        Context $httpContext,
        CheckoutSession $checkoutSession,
        Addcart  $publisher
    ) {
        $this->model = $model;
        $this->resource = $resource;
        $this->httpContext = $httpContext;
        $this->_logger=$_logger;
        $this->checkoutSession = $checkoutSession;
        $this->publisher = $publisher;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $sku = $observer->getProduct()->getSku();
        $quote = $this->checkoutSession->getQuote();
        $quoteId = $quote->getId();
        $customerId = $quote->getCustomerId();
        $created = $quote->getCreatedAt();
        $data = [
            'sku'         => $sku,
            'customer_id' => $customerId,
            'quote_id'    => $quoteId,
            'created'     => $created
        ];
        $this->publisher->publish($data);

    }
}
