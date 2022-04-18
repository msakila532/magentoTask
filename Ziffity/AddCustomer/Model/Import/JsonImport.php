<?php

namespace Ziffity\AddCustomer\Model\Import;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\InvalidArgumentException;
use Ziffity\AddCustomer\Model\Customer;
use Ziffity\AddCustomer\Profiles\ProfileInterface;


class JsonImport implements ProfileInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var File
     */
    private $file;

    /**
     * @param Customer $customer
     * @param File $file
     * @param SerializerInterface $serializer
     */
    public function __construct(
        File $file,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->file = $file;
    }

    /**
     * @param string $fixture
     * @return mixed|void
     * @throws InvalidArgumentException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getData($fixture)
    {
        $str = $this->file->fileGetContents($fixture);
        $data = [];
        $json = $this->serializer->unserialize($str, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(__('Unable to unserialize json file.'));
        }
        return $json;
    }
}

