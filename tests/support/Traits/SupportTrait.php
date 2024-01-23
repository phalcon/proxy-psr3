<?php

namespace Phalcon\Proxy\Psr3\Tests\Support\Traits;

use function dirname;
use function file_exists;
use function gc_collect_cycles;
use function glob;
use function is_dir;
use function is_file;
use function rmdir;
use function substr;
use function uniqid;
use function unlink;

use const GLOB_MARK;

trait SupportTrait
{
    /**
     * @return string
     */
    private function getLogsDirectory(): string
    {
        return dirname(dirname(dirname(__FILE__))) . '/support/output/';
    }

    /**
     * Returns a unique file name
     *
     * @param string $prefix A prefix for the file
     * @param string $suffix A suffix for the file
     *
     * @return string
     */
    private function getNewFileName(string $prefix = '', string $suffix = 'log'): string
    {
        $prefix = ($prefix) ? $prefix . '_' : '';
        $suffix = ($suffix) ?: 'log';

        return uniqid($prefix, true) . '.' . $suffix;
    }

    /**
     * @param string $directory
     */
    private function safeDeleteDirectory(string $directory): void
    {
        $files = glob($directory . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/') {
                $this->safeDeleteDirectory($file);
            } else {
                unlink($file);
            }
        }

        if (is_dir($directory)) {
            rmdir($directory);
        }
    }

    /**
     * @param string $filename
     */
    private function safeDeleteFile(string $filename): void
    {
        if (file_exists($filename) && is_file($filename)) {
            gc_collect_cycles();
            unlink($filename);
        }
    }
}
