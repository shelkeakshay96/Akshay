<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details. *
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Console
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
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
use Symfony\Component\Console\Output\ConsoleOutput;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Wm\Import\Api\ImporterInterface;

/**
 * Console command to import customer
 *
 * @category Console
 * @package  Wm\Import\Console
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class CustomerImport extends Command
{
    private const CONSOLE_COMMAND = 'customer:import';
    private const PARAM_PROFILE = 'profile-name';
    private const PARAM_SOURCE = 'source';

    /**
     * Directory List
     *
     * @var DirectoryList
     */
    protected $dir;

    /**
     * File Obj
     *
     * @var File
     */
    protected $file;

    /**
     * Read Factory
     *
     * @var ReadFactory
     */
    protected $directoryReadFactory;

    /**
     * Console Output
     *
     * @var ConsoleOutput
     */
    protected $consoleOutput;

    /**
     * Importer Interface
     *
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * Io File
     *
     * @var IoFile
     */
    protected $ioFile;

    /**
     * Allowed Extensions
     *
     * @var array array
     */
    public array $allowedExtensions;

    /**
     * Console command for import customers
     *
     * @param DirectoryList     $dir                  Parameter
     * @param File              $file                 Parameter
     * @param ReadFactory       $directoryReadFactory Parameter
     * @param ImporterInterface $importer             Parameter
     * @param ConsoleOutput     $consoleOutput        Parameter
     * @param IoFile            $ioFile               Parameter
     * @param array             $allowedExtensions    Parameter
     */
    public function __construct(
        DirectoryList $dir,
        File $file,
        ReadFactory $directoryReadFactory,
        ImporterInterface $importer,
        ConsoleOutput $consoleOutput,
        IoFile $ioFile,
        array $allowedExtensions
    ) {
        $this->dir = $dir;
        $this->file = $file;
        $this->directoryReadFactory = $directoryReadFactory;
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
        $this->setName(self::CONSOLE_COMMAND)
            ->setDescription('import customers from a sample CSV or JSON: "php magento run:cron <profile-name> <source>"') //phpcs:ignore
            ->setDefinition(
                [
                    new InputArgument(
                        self::PARAM_PROFILE,
                        InputArgument::REQUIRED,
                        'File type (Profile)',
                        null
                    ),
                    new InputArgument(
                        self::PARAM_SOURCE,
                        InputArgument::REQUIRED,
                        'File Name',
                        null
                    ),
                ]
            )
            ->setHelp(
                <<<EOT
Steps to import a customer:
1. add a json /csv file at magento root.
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
     * @param InputInterface  $input  Parameter
     * @param OutputInterface $output Parameter
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
     * @param string $profileName Parameter
     * @param string $source      Parameter
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
        if (!$extension || ($extension != $fileExtension) || (!in_array($extension, $this->allowedExtensions))) { //phpcs:ignore
            throw new LocalizedException(__('Invalid profile(extension) name'));
        }

        return $filename;
    }
}
