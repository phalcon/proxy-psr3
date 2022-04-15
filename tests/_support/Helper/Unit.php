<?php

namespace Helper;

use Codeception\Module;
use ReflectionClass;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends Module
{


    /**
     * Returns a unique file name
     *
     * @param string $prefix A prefix for the file
     * @param string $suffix A suffix for the file
     *
     * @return string
     */
    public function getNewFileName(string $prefix = '', string $suffix = 'log')
    {
        $prefix = ($prefix) ? $prefix . '_' : '';
        $suffix = ($suffix) ?: 'log';

        return uniqid($prefix, true) . '.' . $suffix;
    }

    /**
     * @throws ReflectionException
     */
    public function getProtectedProperty($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);

        $property = $reflection->getProperty($prop);

        $property->setAccessible(true);

        return $property->getValue($obj);
    }

    /**
     * @param string $directory
     */
    public function safeDeleteDirectory(string $directory)
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
    public function safeDeleteFile(string $filename)
    {
        if (file_exists($filename) && is_file($filename)) {
            gc_collect_cycles();
            unlink($filename);
        }
    }
}
