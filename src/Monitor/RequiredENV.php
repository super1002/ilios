<?php

declare(strict_types=1);

namespace App\Monitor;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class RequiredENV implements CheckInterface
{
    private const REQUIRED_ENV = [
        'ILIOS_DATABASE_URL',
        'ILIOS_MAILER_URL',
        'ILIOS_LOCALE',
        'ILIOS_SECRET'
    ];
    private const INSTRUCTIONS_URL = 'https://github.com/ilios/ilios/blob/master/docs/env_vars_and_config.md';
    private const UPDATE_URL = 'https://github.com/ilios/ilios/blob/master/docs/update.md';

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        $missingVariables = array_filter(self::REQUIRED_ENV, function ($name) {
            return !getenv($name) && !isset($_ENV[$name]);
        });

        if (count($missingVariables)) {
            $missing = implode("\n", $missingVariables);

            return new Failure(
                "\nMissing:\n" . $missing . "\n For help see: \n " . self::INSTRUCTIONS_URL
            );
        }

        if (getenv('ILIOS_DATABASE_MYSQL_VERSION') || isset($_ENV['ILIOS_DATABASE_MYSQL_VERSION'])) {
            return new Failure(
                "\nILIOS_DATABASE_MYSQL_VERSION should be migrated. See \n " . self::UPDATE_URL .
                " for details.\n"
            );
        }
        return new Success('All required ENV variables are setup');
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'ENV variables';
    }
}
