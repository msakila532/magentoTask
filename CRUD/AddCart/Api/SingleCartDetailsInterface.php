<?php

namespace CRUD\AddCart\Api;

interface SingleCartDetailsInterface
{
    /**
     * @param int $id
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function getCartById(int $id);
}
