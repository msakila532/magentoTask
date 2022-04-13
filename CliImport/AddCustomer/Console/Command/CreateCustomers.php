<?php

namespace CliImport\AddCustomer\Console\Command;

use Exception;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Filesystem\Io\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CliImport\AddCustomer\Model\Customer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateCustomers extends Command
{
    private $customer;
    private $state;
    CONST PROFILE='profile';
    protected $_fileSystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var File
     */
    private $fileSystemIo;

    public function __construct(
        File $filesystemIo,
        Customer $customer,
        State $state,
        DirectoryList $directoryList

    ) {
        parent::__construct();
        $this->fileSystemIo = $filesystemIo;
        $this->customer = $customer;
        $this->state = $state;
        $this->directoryList = $directoryList;

    }
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
    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $source = $input->getArgument('source');
            $profile = $input->getOption('profile');
            $varDirectory = $this->directoryList->getPath('var');
            $magentoRootDirectory = $this->directoryList->getRoot();
            $sourcePath = $magentoRootDirectory . '/' . $source;
            $destinationPath = $varDirectory . '/' . $source;
            $this->fileSystemIo->cp($sourcePath, $destinationPath);
            $this->state->setAreaCode(Area::AREA_GLOBAL);
            $this->customer->install($destinationPath, $output,$profile);
            return Cli::RETURN_SUCCESS;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Cli::RETURN_FAILURE;
        }
    }
}
