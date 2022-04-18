<?php

namespace Ziffity\AddCustomer\Console\Command;

use Exception;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Filesystem\Io\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ziffity\AddCustomer\Model\Customer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\File\Size;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Ziffity\AddCustomer\Model\Import\CsvImport;
use Ziffity\AddCustomer\Model\Import\JsonImport;


class CreateCustomers extends Command
{
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var State
     */
    private $state;
    /**
     * @var
     */
    protected $_fileSystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var File
     */
    private $fileSystemIo;

    /**
     * @var Size
     */
    protected $fileSize;

    CONST PROFILE='profile';
    /**
     * @var ProgressBarFactory
     */
    private  $progressBarFactory;
    /**
     * @var CsvImport
     */
    private $csvImport;
    /**
     * @var JsonImport
     */
    private $jsonImport;

    /**
     * @param File $filesystemIo
     * @param Customer $customer
     * @param State $state
     * @param DirectoryList $directoryList
     * @param Size $fileSize
     */
    public function __construct(
        File               $filesystemIo ,
        Customer           $customer ,
        State              $state ,
        DirectoryList      $directoryList ,
        Size               $fileSize ,
        ProgressBarFactory $progressBarFactory ,
        CsvImport          $csvImport ,
        JsonImport         $jsonImport
    ) {
        parent::__construct();
        $this->fileSystemIo = $filesystemIo;
        $this->customer = $customer;
        $this->state = $state;
        $this->directoryList = $directoryList;
        $this->fileSize = $fileSize;
        $this->progressBarFactory = $progressBarFactory;
        $this->csvImport = $csvImport;
        $this->jsonImport = $jsonImport;

    }
    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setName("customer:importer");
        $this->setDescription("Import customers data from csv or json file");
        $this->addOption(
            'profile',
            null,
            InputOption::VALUE_REQUIRED,
            'Profile Name only supports csv and json formats'
        );
        $this->addArgument('source', InputArgument::REQUIRED, 'File Path');
        parent::configure();

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $source = $input->getArgument('source');
            $profile = $input->getOption('profile');
            $varDirectory = $this->directoryList->getPath('var');
            $magentoRootDirectory = $this->directoryList->getRoot();
           //Validate File Type
            $pathInfo = $this->fileSystemIo->getPathInfo($source);
            $extension = $pathInfo['extension'] ?? '';
            if (!$profile) {
                $profile = $extension;
            }
            if ((!($profile === "csv" || $profile === "json")) || ($profile !== $extension)) {
                throw new Exception(__("Invalid file type!"));
            }
            //Validate File Size
            $maxImageSize = $this->fileSize->getMaxFileSizeInMb();
            if ($maxImageSize) {
                $message = __('The total size of the uploadable files can\'t be more than %1M', $maxImageSize);
            } else {
                $message = __('System doesn\'t allow to get file upload settings');
            }
            $profileObj = $this->getProfileObj($profile);

            $sourcePath = $magentoRootDirectory . '/' . $source;
            $destinationPath = $varDirectory . '/' . $source;
            $this->fileSystemIo->cp($sourcePath, $destinationPath);
            $this->state->setAreaCode(Area::AREA_GLOBAL);
            $output->writeln('<info>Execution starts.</info>');
            //Importing customers
            $this->customer->install($destinationPath, $output,$profileObj,$this->progressBarFactory);
            $output->write(PHP_EOL);
            $output->writeln('<info>Execution ends.</info>');
            return Cli::RETURN_SUCCESS;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * @param $profile
     * @return void
     */
    private function getProfileObj($profile) {
        switch ($profile) {
            case "csv":
                return $this->csvImport;
            case "json":
                return $this->jsonImport;
        }
    }
}
