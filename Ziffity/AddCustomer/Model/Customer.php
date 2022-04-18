<?php

namespace Ziffity\AddCustomer\Model;

use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Ziffity\AddCustomer\Model\Import\JsonImport;
use Ziffity\AddCustomer\Model\Import\CsvImport;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

class Customer
{
    private $file;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
    /**
     * @var
     */
    private $output;
    /**
     * @var JsonImport
     */
    private $jsonImport;
    /**
     * @var CsvImport
     */
    private $csvImport;
    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerInterface;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @param File $file
     * @param StoreManagerInterface $storeManagerInterface
     * @param JsonImport $jsonImport
     * @param CsvImport $csvImport
     * @param CustomerInterfaceFactory $customerInterface
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        File $file,
        StoreManagerInterface $storeManagerInterface,
        JsonImport $jsonImport,
        CsvImport $csvImport,
        CustomerInterfaceFactory $customerInterface,
        CustomerRepository $customerRepository

    ) {
        $this->file = $file;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->jsonImport = $jsonImport;
        $this->csvImport = $csvImport;
        $this->customerInterface = $customerInterface;
        $this->customerRepository = $customerRepository;

    }

    /**
     * @param string $fixture
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $profile
     * @param ProgressBarFactory $progressBarFactory
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function install(string $fixture, OutputInterface $output, $profile, $progressBarFactory): void
    {
        $this->output = $output;
         $getdata = $profile->getData($fixture);
        // get store and website ID
        $store = $this->storeManagerInterface->getStore();
        $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
        $storeId = (int) $store->getId();
        $progressBar = $progressBarFactory->create(
            [
                'output' => $output,
                'max' => count($getdata)
            ]
        );
        $progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
        );

        $progressBar->start();
        foreach ($getdata as $key => $value) {
                  $this->createCustomer($value, $websiteId, $storeId);
            $progressBar->advance();
               }
        $progressBar->finish();

    }

    /**
     * @param $data
     * @param $websiteId
     * @param $storeId
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function createCustomer($data, $websiteId, $storeId): void
    {
        $customer = $this->customerInterface->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($data['emailaddress']);
        $customer->setFirstname($data['fname']);
        $customer->setLastname($data['lname']);
        $customer->setStoreId($storeId);
        $this->customerRepository->save($customer);

    }

}
