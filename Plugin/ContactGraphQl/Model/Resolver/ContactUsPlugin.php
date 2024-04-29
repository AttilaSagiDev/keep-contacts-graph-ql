<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContactsGraphQl\Plugin\ContactGraphQl\Model\Resolver;

use Space\KeepContacts\Api\Data\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\ContactGraphQl\Model\Resolver\ContactUs;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ContactUsPlugin
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param ContactUs $subject
     * @param $result
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $resolveInfo
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function afterResolve(
        ContactUs $subject,
        $result,
        Field $field,
        ContextInterface $context,
        ResolveInfo $resolveInfo,
        array $value = null,
        array $args = null
    ): array {
        if ($this->config->isEnabled()) {
            $this->logger->debug('afterResolve KeepContacts');
        }

        return $result;
    }
}
