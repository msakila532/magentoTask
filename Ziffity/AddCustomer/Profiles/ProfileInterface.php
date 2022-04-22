<?php
declare(strict_types=1);

namespace Ziffity\AddCustomer\Profiles;

use Symfony\Component\Console\Output\OutputInterface;

interface ProfileInterface
{
    /**
     * @param string $file
     * @param OutputInterface $output
     * @return array
     */
    public function getData(string $file, OutputInterface $output): array;
}
