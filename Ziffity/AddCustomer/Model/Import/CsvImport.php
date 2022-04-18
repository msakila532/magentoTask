<?php

namespace Ziffity\AddCustomer\Model\Import;

use Magento\Framework\File\Csv;
use Ziffity\AddCustomer\Profiles\ProfileInterface;

class CsvImport implements ProfileInterface
{
    /**
     * @var Csv
     */
    private $csvParser;
    /**
     * @param Csv $csvParser
     */
    public function __construct
    (
        Csv $csvParser
    )
    {
        $this->csvParser = $csvParser;
    }
    /**
     * @param $file
     * @return array|void
     */
    public function getData($file)
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
                //return $chunks;
             } catch (\Exception $e) {
           $e->getMessage();
        }
        return $chunks;
    }
}
