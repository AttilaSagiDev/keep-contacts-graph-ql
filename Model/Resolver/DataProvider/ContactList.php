<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContactsGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\Store;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ContactList
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * Constructor
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ContactRepositoryInterface $contactRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Get contact list
     *
     * @param CustomerInterface $customer
     * @param int $pageSize
     * @param int $currentPage
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getContactList(CustomerInterface $customer, int $pageSize, int $currentPage, int $storeId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Store::STORE_ID, [$storeId, Store::DEFAULT_STORE_ID], 'in')
            ->addFilter(ContactInterface::EMAIL, $customer->getEmail())
            ->setPageSize($pageSize)
            ->setCurrentPage($currentPage)->create();

        $contactListResults = $this->contactRepository->getList($searchCriteria);

        if (!$contactListResults->getTotalCount()) {
            throw new NoSuchEntityException(__('The contact list is empty.'));
        }

        $listSize = $contactListResults->getTotalCount();
        $totalPages = 0;
        if ($listSize > 0 && $pageSize > 0) {
            $totalPages = ceil($listSize / $pageSize);
        }

        $contactList = [];
        foreach ($contactListResults->getItems() as $contact) {
            $contactList['items'][] = [
                ContactInterface::CONTACT_ID => $contact->getId(),
                ContactInterface::NAME => $contact->getName(),
                ContactInterface::EMAIL => $contact->getEmail(),
                ContactInterface::TELEPHONE => $contact->getTelephone() !== null ? $contact->getTelephone() : '',
                ContactInterface::COMMENT => $contact->getComment(),
                ContactInterface::ANSWER => $contact->getAnswer() !== null ? $contact->getAnswer() : '',
                ContactInterface::CREATION_TIME => $contact->getCreationTime(),
                ContactInterface::UPDATE_TIME => $contact->getUpdateTime(),
                ContactInterface::IS_ANSWERED => $contact->isAnswered()
            ];
        }

        $contactList['page_info'] = [
            'total_pages' => $totalPages,
            'page_size' => $pageSize,
            'current_page' => $currentPage,
        ];
        $contactList['total_count'] = $listSize;

        return $contactList;
    }
}
