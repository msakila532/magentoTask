<?php

namespace Ziffity\AddCart\Api;

interface SingleCartDetailsInterface
{
    /**
     * @param int $id
     * @return \Ziffity\AddCart\Api\DataInterface[]
     */
    public function getCartById(int $id);
}
