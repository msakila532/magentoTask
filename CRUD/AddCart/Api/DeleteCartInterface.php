<?php

namespace CRUD\AddCart\Api;

interface DeleteCartInterface
{

    /**
     * @param int $id
     * @return \CRUD\AddCart\Api\DataInterface[]
     */
    public function deleteCartById(int $id);
}
