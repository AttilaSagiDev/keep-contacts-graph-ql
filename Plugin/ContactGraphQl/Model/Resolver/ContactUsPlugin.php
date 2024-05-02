<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContactsGraphQl\Plugin\ContactGraphQl\Model\Resolver;

use Space\KeepContacts\Model\ContactFactory;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\ContactGraphQl\Model\Resolver\ContactUs;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;

class ContactUsPlugin
{
    /**
     * @var ContactFactory
     */
    private ContactFactory $contactFactory;

    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

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
     * @param ContactFactory $contactFactory
     * @param ContactRepositoryInterface $contactRepository
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContactFactory $contactFactory,
        ContactRepositoryInterface $contactRepository,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->contactFactory = $contactFactory;
        $this->contactRepository = $contactRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * After resolve
     *
     * @param ContactUs $subject
     * @param array $result
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $resolveInfo
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterResolve( //NOSONAR
        ContactUs $subject, //NOSONAR
        array $result,
        Field $field, //NOSONAR
        ContextInterface $context,
        ResolveInfo $resolveInfo, //NOSONAR
        array $value = null, //NOSONAR
        array $args = null
    ): array {
        if ($this->config->isEnabled()
            && isset($result['status'])
            && $result['status'] === true
        ) {
            try {
                $contact = $this->contactFactory->create();
                $contact->setName($args['input']['name']);
                $contact->setEmail($args['input']['email']);
                $contact->setTelephone($args['input']['telephone']);
                $contact->setComment($args['input']['comment']);
                $contact->setStoreId((int)$context->getExtensionAttributes()->getStore()->getId());
                $this->contactRepository->save($contact);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }

        }

        return $result;
    }
}
