<?php
declare(strict_types=1);

namespace Ziffity\AddCustomer\Model\Import;

use Exception;
use Magento\Framework\File\Csv;
use Symfony\Component\Console\Output\OutputInterface;
use Ziffity\AddCustomer\Profiles\ProfileInterface;

class CsvImport implements ProfileInterface
{
    /**
     * @param Csv
     */
    private $csvParser;

    public function __construct
    (
        Csv $csvParser
    )
    {
        $this->csvParser = $csvParser;
    }

    /**
     * @param $file
     * @param OutputInterface $output
     * @return array
     */
    public function getData(string $file, OutputInterface $output): array
    {
        try {
            $chunks = [];
            $data = [];
            $contents = $this->csvParser->getData($file);
            $headers = !empty($contents) ? $contents[0] : [];
            foreach ($contents as $row => $values) {
                if ($row > 0) {
                    foreach ($values as $key => $value) {
                        $data[$headers[$key]] = $value;
                    }
                    $chunks[] = $data;
                }
            }
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
        return $chunks;
    }
}
