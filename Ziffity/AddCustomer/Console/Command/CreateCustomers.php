<?php
declare(strict_types=1);

namespace Ziffity\AddCustomer\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\File\Size;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ziffity\AddCustomer\Model\Customer;
use Ziffity\AddCustomer\Model\Import\CsvImport;
use Ziffity\AddCustomer\Model\Import\JsonImport;

class CreateCustomers extends Command
{
    const PROFILE = 'profile';
    const CSV = 'csv';
    const JSON = 'json';
    /**
     * @var Size
     */
    protected $fileSize;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var State
     */
    private $state;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var File
     */
    private $fileSystemIo;
    /**
     * @var ProgressBarFactory
     */
    private $progressBarFactory;
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
     * @param ProgressBarFactory $progressBarFactory
     * @param CsvImport $csvImport
     * @param JsonImport $jsonImport
     */
    public function __construct(
        File               $filesystemIo,
        Customer           $customer,
        State              $state,
        DirectoryList      $directoryList,
        Size               $fileSize,
        ProgressBarFactory $progressBarFactory,
        CsvImport          $csvImport,
        JsonImport         $jsonImport
    )
    {
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
            self::PROFILE,
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
                throw new \RuntimeException(__("Invalid file type!"));
            }

            $profileObj = $this->getProfileObj($profile);
            $sourcePath = $magentoRootDirectory . '/' . $source;
            $destinationPath = $varDirectory . '/' . $source;
            $this->fileSystemIo->cp($sourcePath, $destinationPath);
            //Validate File Size
           $fileSize= filesize($sourcePath);
           $fileSizeMb=$this->fileSize->getFileSizeInMb($fileSize);
            $maxFileSize = $this->fileSize->getMaxFileSizeInMb();
            if ($maxFileSize<$fileSizeMb) {
                throw new \RuntimeException(__("Invalid Size!"));
            }

            $this->state->setAreaCode(Area::AREA_GLOBAL);
            $output->writeln('<info>Execution starts.</info>');
            //Importing customers
            $this->customer->install($destinationPath, $output, $profileObj, $this->progressBarFactory);
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
     */
    private function getProfileObj($profile)
    {
        switch ($profile) {
            case self::JSON:
                return $this->jsonImport;
            case self::CSV:
            default:
                return $this->csvImport;
        }
    }
}
