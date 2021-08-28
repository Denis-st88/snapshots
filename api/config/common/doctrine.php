<?php

declare(strict_types=1);

use DI\Container;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManagerInterface::class => function (Container $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     metadata_dirs:array,
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     cache_dir:?string,
         *     types:array<string,class-string<Doctrine\DBAL\Types\Type>>,
         *     subscribers:string[],
         *     connection:array<string, mixed>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ?
                DoctrineProvider::wrap(new FilesystemAdapter('', 0, $settings['cache_dir'])) :
                DoctrineProvider::wrap(new ArrayAdapter()),
            false
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        return EntityManager::create(
            $settings['connection'],
            $config
        );
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'name' => getenv('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'metadata_dirs' => []
        ]
    ]
];