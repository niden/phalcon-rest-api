<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Cli\Tasks;

use Phalcon\Cache;
use Phalcon\Cli\Task as PhTask;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function in_array;
use function Phalcon\Api\Core\appPath;
use const PHP_EOL;

/**
 * Class ClearcacheTask
 *
 * @property Cache $cache
 */
class ClearcacheTask extends PhTask
{
    /**
     * Clears the data cache from the application
     */
    public function mainAction()
    {
        $this->clearFileCache();
        $this->clearMemCached();
    }

    /**
     * Clears file based cache
     */
    private function clearFileCache()
    {
        echo 'Clearing Cache folders' . PHP_EOL;

        $fileList    = [];
        $whitelist   = ['.', '..', '.gitignore'];
        $path        = appPath('storage/cache');
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator    = new RecursiveIteratorIterator(
            $dirIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /**
         * Get how many files we have there and where they are
         */
        foreach ($iterator as $file) {
            if (true !== $file->isDir() && true !== in_array($file->getFilename(), $whitelist)) {
                $fileList[] = $file->getPathname();
            }
        }

        echo sprintf('Found %s files', count($fileList)) . PHP_EOL;
        foreach ($fileList as $file) {
            echo '.';
            unlink($file);
        }

        echo PHP_EOL . 'Cleared Cache folders' . PHP_EOL;
    }

    /**
     * Clears memcached data cache
     */
    private function clearMemCached()
    {
        echo 'Clearing data cache' . PHP_EOL;
        $options   = include appPath('cli/config/cache.php');
        $servers   = $options['servers'] ?? [];
        $memcached = new \Memcached();
        foreach ($servers as $server) {
            $memcached->addServer($server['host'], $server['port'], $server['weight']);
        }

        $keys = $memcached->getAllKeys();
        echo sprintf('Found %s keys', count($keys)) . PHP_EOL;
        foreach ($keys as $key) {
            if ('api-data' === substr($key, 0, 8)) {
                $server     = $memcached->getServerByKey($key);
                $result     = $memcached->deleteByKey($server['host'], $key);
                $resultCode = $memcached->getResultCode();
                if (true === $result && $resultCode !== \Memcached::RES_NOTFOUND) {
                    echo '.';
                } else {
                    echo 'F';
                }
            }
        }

        echo  PHP_EOL . 'Cleared data cache'  . PHP_EOL;
    }
}
