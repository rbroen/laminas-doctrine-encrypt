<?php declare(strict_types=1);

namespace Keet\Encrypt;

use Doctrine\Common\Annotations\AnnotationReader;
use Keet\Encrypt\Adapter\EncryptionAdapter;
use Keet\Encrypt\Adapter\HashingAdapter;
use Keet\Encrypt\Factory\Adapter\EncryptionAdapterFactory;
use Keet\Encrypt\Factory\Adapter\HashingAdapterFactory;
use Keet\Encrypt\Factory\Service\EncryptionServiceFactory;
use Keet\Encrypt\Factory\Service\HashingServiceFactory;
use Keet\Encrypt\Factory\Subscriber\EncryptionSubscriberFactory;
use Keet\Encrypt\Factory\Subscriber\HashingSubscriberFactory;
use Keet\Encrypt\Service\EncryptionService;
use Keet\Encrypt\Service\HashingService;

/**
 * Config provider for Laminas Doctrine Encrypt config
 */
class ConfigProvider
{
    /**
     * @return mixed[]
     */
    public function __invoke(): array
    {
        return [
            'doctrine_factories' => $this->getDoctrineFactoryConfig(),
            'doctrine' => $this->getDoctrine(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Factory mappings - used to define which factory to use to instantiate a particular doctrine service type
     *
     * @return mixed[]
     */
    public function getDoctrineFactoryConfig(): array
    {
        return [
            'encryption' => EncryptionSubscriberFactory::class,
            'hashing'    => HashingSubscriberFactory::class,
        ];
    }

    /**
     * Default configuration for Doctrine module
     *
     * Notice that the Doctrine event manager has key 'event_manager'
     *
     * @return mixed[]
     */
    public function getDoctrine(): array
    {
        return [
            'encryption'   => [
                'orm_default' => [
                    'adapter' => 'encryption_adapter',
                    'reader'  => AnnotationReader::class,
                ],
            ],
            'hashing'      => [
                'orm_default' => [
                    'adapter' => 'hashing_adapter',
                    'reader'  => AnnotationReader::class,
                ],
            ],
            'event_manager' => [
                'orm_default' => [
                    'subscribers' => [
                        'doctrine.encryption.orm_default',
                        'doctrine.hashing.orm_default',
                    ],
                ],
            ],
        ];
    }

    /**
     * Return application-level dependency configuration
     *
     * @return mixed[]
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                // Using aliases so someone else can use own adapter/factory
                'encryption_adapter' => EncryptionAdapter::class,
                'encryption_service' => EncryptionService::class,
                'hashing_adapter'    => HashingAdapter::class,
                'hashing_service'    => HashingService::class,
            ],
            'factories' => [
                EncryptionAdapter::class => EncryptionAdapterFactory::class,
                EncryptionService::class => EncryptionServiceFactory::class,
                HashingAdapter::class    => HashingAdapterFactory::class,
                HashingService::class    => HashingServiceFactory::class,
            ],
        ];
    }
}
