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

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

return [
    'app'        => [
        'version'      => envValue('VERSION', time()),
        'timezone'     => envValue('APP_TIMEZONE', 'UTC'),
        'debug'        => envValue('APP_DEBUG', false),
        'env'          => envValue('APP_ENV', 'development'),
        'devMode'      => boolval(
            'development' === envValue('APP_ENV', 'development')
        ),
        'baseUri'      => envValue('APP_BASE_URI'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time'         => microtime(true),
    ],
    'cache'      => [
        'data'     => [
            'front' => [
                'adapter' => 'Data',
                'options' => [
                    'lifetime' => envValue('CACHE_LIFETIME'),
                ],
            ],
            'back'  => [
                'dev'  => [
                    'adapter' => 'File',
                    'options' => [
                        'cacheDir' => appPath('storage/cache/data/'),
                    ],
                ],
                'prod' => [
                    'adapter' => 'Libmemcached',
                    'options' => [
                        'servers' => [
                            [
                                'host'   => envValue('DATA_API_MEMCACHED_HOST'),
                                'port'   => envValue('DATA_API_MEMCACHED_PORT'),
                                'weight' => envValue('DATA_API_MEMCACHED_WEIGHT'),
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'metadata' => [
            'dev'  => [
                'adapter' => 'Memory',
                'options' => [],
            ],
            'prod' => [
                'adapter' => 'Files',
                'options' => [
                    'metaDataDir' => appPath('storage/cache/metadata/'),
                ],
            ],
        ],
    ],
];
