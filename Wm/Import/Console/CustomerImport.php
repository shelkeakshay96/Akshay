<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Wm\Import\Console;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Wm\Import\Api\ImporterInterface;

/**
 * Console command to import customer
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
class CustomerImport extends Command
{
    private const COMMAND_CUSTOMER_IMPORT = 'customer:import';

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var ReadFactory
     */
    protected $directoryReadFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ConsoleOutput
     */
    protected $consoleOutput;

    /**
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * @var IoFile
     */
    protected $ioFile;

    /**
     * @var array
     */
    public array $allowedExtensions;

    /**
     * Console command for import customers
     *
     * @param DirectoryList         $dir
     * @param File                  $file
     * @param ReadFactory           $directoryReadFactory
     * @param StoreManagerInterface $storeManager
     * @param ImporterInterface     $importer
     * @param ConsoleOutput         $consoleOutput
     * @param IoFile                $ioFile
     * @param array                 $allowedExtensions
     */
    public function __construct(
        DirectoryList $dir,
        File $file,
        ReadFactory $directoryReadFactory,
        StoreManagerInterface $storeManager,
        ImporterInterface $importer,
        ConsoleOutput $consoleOutput,
        IoFile $ioFile,
        array $allowedExtensions
    ) {
        $this->dir = $dir;
        $this->file = $file;
        $this->directoryReadFactory = $directoryReadFactory;
        $this->storeManager = $storeManager;
        $this->importer = $importer;
        $this->consoleOutput = $consoleOutput;
        $this->ioFile = $ioFile;
        $this->allowedExtensions = $allowedExtensions;
        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_CUSTOMER_IMPORT)
            ->setDescription('import customers from a sample CSV or JSON: "php magento run:cron <profile-name> <source>"') //phpcs:ignore
            ->setDefinition(
                [
                    new InputArgument(
                        'profile-name',
                        InputArgument::REQUIRED,
                        'File type',
                        null
                    ),
                    new InputArgument(
                        'source',
                        InputArgument::REQUIRED,
                        'File Name',
                        null
                    ),
                ]
            )
            ->setHelp(
                <<<EOT
Steps to import a customer:
1. add a csv / json file at magento root.
2. rum command: bin/magento customer:import <profile-name> <source>
eq: bin/magento customer:import sample-csv sample.csv

NOTES:
1. Customers will be imported for default website (base) with website id 1.
2. Customers will be imported for customer group general with group id 1.
3. If customer email already exists for default website,
   importing will skip that customer
EOT
            );

        parent::configure();
    }

    /**
     * Export customer console line execution
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return boolean
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $profileName = $input->getArgument('profile-name');
            $source = $input->getArgument('source');

            $filename = $this->validateParams($profileName, $source);

            $this->importer->execute($filename, $profileName);
            $result = Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            $result = Cli::RETURN_FAILURE;
        }

        return $result;
    }

    /**
     * Validate parameters and path
     *
     * @param string $profileName
     * @param string $source
     *
     * @return void
     */
    protected function validateParams($profileName, $source)
    {
        $profiles = explode('-', $profileName);
        $extension = $profiles[1] ?? $profiles[0];
        $path = $this->dir->getRoot();
        $filename = $path . '/' . $source;
        if (!$this->file->isExists($filename)) {
            throw new LocalizedException(__($source . ' file not exists'));
        }

        $path_info = $this->ioFile->getPathInfo($filename);
        $fileExtension = $path_info['extension'] ?? '';
        if (!$extension || ($extension != $fileExtension) || (!in_array($extension, $this->allowedExtensions))) {
            throw new LocalizedException(__('Invalid profile(extension) name'));
        }

        return $filename;
    }
}
