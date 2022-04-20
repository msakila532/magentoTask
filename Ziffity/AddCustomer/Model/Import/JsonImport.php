<?php
declare(strict_types=1);

namespace Ziffity\AddCustomer\Model\Import;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\SerializerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ziffity\AddCustomer\Profiles\ProfileInterface;


class JsonImport implements ProfileInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var File
     */
    private File $file;


    /**
     * @param File $file
     * @param SerializerInterface $serializer
     */
    public function __construct(
        File                $file,
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
        $this->file = $file;
    }

    /**
     * @param string $file
     * @param OutputInterface $output
     * @return array
     */
    public function getData(string $file, OutputInterface $output): array
    {
        try {
            $contents = $this->file->fileGetContents($file);
            $chunks = $this->serializer->unserialize($contents);
        } catch (FileSystemException $e) {
            return $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
        return $chunks;
    }
}

