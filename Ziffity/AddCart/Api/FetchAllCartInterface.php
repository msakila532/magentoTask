<?php

namespace Ziffity\AddCart\Api;

interface FetchAllCartInterface
{

    /**
     * @param int|null $pageId
     * @return \Ziffity\AddCart\Api\DataInterface[]
     */
    public function getCartList(int $pageId = null);
}
