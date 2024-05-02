<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContactsGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Space\KeepContactsGraphQl\Model\Resolver\DataProvider\ContactList as ContactListDataProvider;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\GraphQl\Model\Query\ContextInterface as QueryContext;

class CustomerContacts implements ResolverInterface
{
    /**
     * @var ContactListDataProvider
     */
    private ContactListDataProvider $contactListDataProvider;

    /**
     * @var GetCustomer
     */
    private GetCustomer $getCustomer;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param ContactListDataProvider $contactListDataProvider
     * @param GetCustomer $getCustomer
     * @param ConfigInterface $config
     */
    public function __construct(
        ContactListDataProvider $contactListDataProvider,
        GetCustomer $getCustomer,
        ConfigInterface $config
    ) {
        $this->contactListDataProvider = $contactListDataProvider;
        $this->getCustomer = $getCustomer;
        $this->config = $config;
    }

    /**
     * Resolve
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        if (!$this->config->isEnabled()) {
            throw new GraphQlInputException(__('Keep Contacts module is not enabled.'));
        }

        /** @var QueryContext $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }

        try {
            $customer = $this->getCustomer->execute($context);
            $customerContactList = $this->contactListDataProvider->getContactList(
                $customer,
                $args['pageSize'],
                $args['currentPage'],
                $storeId
            );
        } catch (LocalizedException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }

        return $customerContactList;
    }
}
