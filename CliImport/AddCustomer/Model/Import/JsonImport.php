<?php

namespace CliImport\AddCustomer\Model\Import;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\InvalidArgumentException;
use CliImport\AddCustomer\Model\Customer;

class JsonImport
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
     * @var CustomerImport
     */
    private $customerImport;

    /**
     * @param Customer $customer
     * @param File $file
     * @param SerializerInterface $serializer
     */
    public function __construct(
        File $file,
        SerializerInterface $serializer,
        CustomerImport $customerImport
    ) {
        $this->serializer = $serializer;
        $this->file = $file;
        $this->customerImport = $customerImport;
    }

    /**
     * @param string $fixture
     * @param int $websiteId
     * @param int $storeId
     * @return mixed|void
     * @throws InvalidArgumentException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getData($fixture, $websiteId, $storeId)
    {
        $str = $this->file->fileGetContents($fixture);
        $data = [];
        $json = $this->serializer->unserialize($str, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(__('Unable to unserialize json file.'));
        }
        foreach ($json as $key => $value) {
            $this->customerImport->createCustomer($value, $websiteId, $storeId);
        }

    }
}

