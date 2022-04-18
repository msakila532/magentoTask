<?php

namespace Ziffity\AddCart\Api;

interface DeleteCartInterface
{

    /**
     * @param int $id
     * @return \Ziffity\AddCart\Api\DataInterface[]
     */
    public function deleteCartById(int $id);
}
