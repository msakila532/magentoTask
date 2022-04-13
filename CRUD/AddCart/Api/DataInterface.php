<?php
namespace CRUD\AddCart\Api;

interface DataInterface
{
    const ID = 'id';
    const SKU = 'sku';
    const CUSTOMERID = 'customer_id';
    const CREATED_AT = 'created_at';
    const QUOTEID = 'quote_id';


    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */

    public function setId($id);

    /**
     * @return string
     */

    public function getSku();

    /**
     * @param string $sku
     * @return $this
     */

    public function setSku($sku);
    /**
     * @return int
     */

    public function getQuoteId();

    /**
     * @param string $quoteid
     * @return $this
     */

    public function setQuoteId($quoteid);

    /**
     * @return int
     */

    public function getCustomerId();

    /**
     * @param string $customerid
     * @return $this
     */

    public function setCustomerId($customerid);

    /**
     * @return string
     */

    public function getCreatedAt();

    /**
     * @param string $created
     * @return $this
     */

    public function setCreatedAt($created);
}
