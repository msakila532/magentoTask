<?php

namespace CRUD\AddCart\Api;

interface FetchAllCartInterface
{

    /**
     * @param int|null $pageId
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function getCartList(int $pageId = null);
}
