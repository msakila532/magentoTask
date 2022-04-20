<?php
declare(strict_types=1);

namespace Ziffity\AddCustomer\Model;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Ziffity\AddCustomer\Model\Import\CsvImport;
use Ziffity\AddCustomer\Model\Import\JsonImport;

class Customer
{
    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerInterface;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var File
     */
    private $file;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
    /**
     * @var JsonImport
     */
    private $jsonImport;
    /**
     * @var CsvImport
     */
    private $csvImport;

    /**
     * @param File $file
     * @param StoreManagerInterface $storeManagerInterface
     * @param JsonImport $jsonImport
     * @param CsvImport $csvImport
     * @param CustomerInterfaceFactory $customerInterface
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        File                     $file,
        StoreManagerInterface    $storeManagerInterface,
        JsonImport               $jsonImport,
        CsvImport                $csvImport,
        CustomerInterfaceFactory $customerInterface,
        CustomerRepository       $customerRepository

    )
    {
        $this -> file = $file;
        $this -> storeManagerInterface = $storeManagerInterface;
        $this -> jsonImport = $jsonImport;
        $this -> csvImport = $csvImport;
        $this -> customerInterface = $customerInterface;
        $this -> customerRepository = $customerRepository;

    }

    /**
     * @param string $file
     * @param OutputInterface $output
     * @param $profile
     * @param ProgressBarFactory $progressBarFactory
     * @return void
     * @throws LocalizedException
     */
    public function install(string $file, OutputInterface $output, $profile, ProgressBarFactory $progressBarFactory)
    {
        $getData = $profile->getData($file, $output);
        // get store and website ID
        $store = $this->storeManagerInterface->getStore();
        $websiteId = (int)$this->storeManagerInterface->getWebsite()->getId();
        $storeId = (int)$store->getId();
        $progressBar = $progressBarFactory->create(
            [
                'output' => $output,
                'max' => count($getData)
            ]
        );
        $progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
        );

        $progressBar->start();
        foreach ($getData as $value) {
            $this->createCustomer($value, $websiteId, $storeId, $output);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

    /**
     * @param $data
     * @param $websiteId
     * @param $storeId
     * @param $output
     */
    public function createCustomer($data, $websiteId, $storeId, $output)
    {
        $customer = $this->customerInterface->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($data['emailaddress']);
        $customer->setFirstname($data['fname']);
        $customer->setLastname($data['lname']);
        $customer->setStoreId($storeId);
        try {
            $this->customerRepository->save($customer);
        } catch (LocalizedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}
