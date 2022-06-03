<?php

declare(strict_types=1);

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

use Codeception\Util\Autoload;

/*******************************************************************************
 * Load settings and setup
 *******************************************************************************/

/**
 * Ensures that certain folders are always ready for us.
 */
if (!function_exists('loadFolders')) {
    function loadFolders()
    {
        $folders = [
            'logs',
        ];

        foreach ($folders as $folder) {
            $item = outputDir('tests' . DIRECTORY_SEPARATOR . $folder);

            if (true !== file_exists($item)) {
                mkdir($item, 0777, true);
            }
        }
    }
}


/*******************************************************************************
 * Directories
 *******************************************************************************/
/**
 * Returns the output folder
 */
if (!function_exists('dataDir')) {
    function dataDir(string $fileName = ''): string
    {
        return codecept_data_dir() . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('logsDir')) {
    function logsDir(string $fileName = ''): string
    {
        return codecept_output_dir()
            . 'tests' . DIRECTORY_SEPARATOR
            . 'logs' . DIRECTORY_SEPARATOR
            . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('outputDir')) {
    function outputDir(string $fileName = ''): string
    {
        return codecept_output_dir() . $fileName;
    }
}

