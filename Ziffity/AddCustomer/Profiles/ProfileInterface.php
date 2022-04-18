<?php

namespace Ziffity\AddCustomer\Profiles;

interface ProfileInterface
{
    /**
     * @param string $file
     * @return array
     */
    public function getData($file);
}
