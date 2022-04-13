<?php

namespace CliImport\AddCustomer\Model;

use Exception;
use Generator;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use CliImport\AddCustomer\Model\Import\CustomerImport;
use CliImport\AddCustomer\Model\Import\JsonImport;
use Symfony\Component\Console\Output\OutputInterface;


class Customer
{
    private $file;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
    private $customerImport;
    private $output;
    /**
     * @var JsonImport
     */
    private $jsonImport;


    /**
     * @param File $file
     * @param StoreManagerInterface $storeManagerInterface
     * @param CustomerImport $customerImport
     */
    public function __construct(
        File $file,
        StoreManagerInterface $storeManagerInterface,
        CustomerImport $customerImport,
        JsonImport $jsonImport

    ) {
        $this->file = $file;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerImport = $customerImport;
        $this->jsonImport = $jsonImport;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InvalidArgumentException
     */
    public function install(string $fixture, OutputInterface $output, $profile): void
    {
        $this->output = $output;

        // get store and website ID
        $store = $this->storeManagerInterface->getStore();
        $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
        $storeId = (int) $store->getId();
        if($profile=='csv'){
            $this->getData($fixture, $websiteId, $storeId);
        }else{
            $this->jsonImport->getData( $fixture, $websiteId, $storeId);
        }
    }
    public function getData($fixture, $websiteId, $storeId)
    {
        // read the csv header
        $header = $this->readCsvHeader($fixture)->current();

        // read the csv file and skip the first (header) row
        $row = $this->readCsvRows($fixture, $header);
        $row->next();
        // while the generator is open, read current row data, create a customer and resume the generator
        while ($row->valid()) {
            $data = $row->current();
            $this->customerImport->createCustomer($data, $websiteId, $storeId);
            $row->next();
        }

    }

    private function readCsvRows(string $file, array $header): ?Generator
    {
        $handle = fopen($file, 'rb');

        while (!feof($handle)) {
            $data = [];
            $rowData = fgetcsv($handle);
            if ($rowData) {
                foreach ($rowData as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                yield $data;
            }
        }

        fclose($handle);
    }

    private function readCsvHeader(string $file): ?Generator
    {
        $handle = fopen($file, 'rb');

        while (!feof($handle)) {
            yield fgetcsv($handle);
        }

        fclose($handle);
    }


}
