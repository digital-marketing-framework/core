<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @template ItemClass of ItemInterface
 */
abstract class ListSectionController extends SectionController
{
    protected const LIST_SCRIPT = 'PKG:digital-marketing-framework/core/res/assets/scripts/backend/list.js';

    protected const PAGINATION_ITEMS_EACH_SIDE = 3;

    protected function addListScript(): void
    {
        $this->addScript(static::LIST_SCRIPT, 'list');
    }

    /**
     * @return array{search?:string,advancedSearch?:bool,searchExactMatch?:bool,minCreated?:string,maxCreated?:string,minChanged?:string,maxChanged?:string,type?:array<string,string>,status?:array<string>} $filters
     */
    protected function getFilters(): array
    {
        return $this->getParameters()['filters'] ?? [];
    }

    /**
     * @return array{page?:int|string,itemsPerPage?:int|string,sorting?:array<string,string>} $navigation
     */
    protected function getNavigation(): array
    {
        return $this->getParameters()['navigation'] ?? [];
    }

    protected function getIdentifier(): int|string|null
    {
        return $this->getParameters()['id'] ?? null;
    }

    /**
     * @return array<string|int,string|int>
     */
    protected function getSelectedItems(): array
    {
        $list = $this->getParameters()['list'] ?? [];

        return array_values(array_filter($list));
    }

    protected function getPage(): ?int
    {
        return $this->getParameters()['page'] ?? null;
    }

    protected function getCurrentAction(string $default): string
    {
        return $this->getParameters()['currentAction'] ?? $default;
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function cleanupArguments(array &$arguments): void
    {
        // TODO can we filter out default values in addition to empty values?
        foreach (array_keys($arguments) as $key) {
            if (is_array($arguments[$key])) {
                $this->cleanupArguments($arguments[$key]);
                if ($arguments[$key] === []) {
                    unset($arguments[$key]);
                }
            } elseif ($arguments[$key] === '') {
                unset($arguments[$key]);
            }
        }
    }

    /**
     * @param array{search?:string,advancedSearch?:bool,searchExactMatch?:bool,minCreated?:string,maxCreated?:string,minChanged?:string,maxChanged?:string,type?:array<string,string>,status?:array<string>} $filters
     * @param array{page?:int|string,itemsPerPage?:int|string,sorting?:array<string,string>} $navigation
     */
    protected function getPermanentUri(string $action, array $filters = [], array $navigation = []): string
    {
        $arguments = ['filters' => $filters, 'navigation' => $navigation];
        $this->cleanupArguments($arguments['filters']);

        return $this->uriBuilder->build('page.' . $this->getSection() . '.' . $action, $arguments);
    }

    /**
     * @param array{search?:string,advancedSearch?:bool,searchExactMatch?:bool,minCreated?:string,maxCreated?:string,minChanged?:string,maxChanged?:string,type?:array<string,string>,status?:array<string>} $filters
     * @param array{page?:int|string,itemsPerPage?:int|string,sorting?:array<string,string>} $navigation
     */
    protected function assignCurrentRouteData(string $defaultAction, array $filters = [], array $navigation = []): void
    {
        $currentAction = $this->getCurrentAction($defaultAction);
        $this->viewData['current'] = $currentAction;

        $permanentUri = $this->getPermanentUri($defaultAction, $filters, $navigation);
        $this->viewData['permanentUri'] = $permanentUri;

        $resetUri = $this->getPermanentUri($defaultAction);
        $this->viewData['resetUri'] = $resetUri;

        $returnUrl = $this->getReturnUrl();
        if ($returnUrl !== null) {
            $this->viewData['returnUrl'] = $returnUrl;
        }
    }

    /**
     * @param array<string,mixed> $filters
     *
     * @return array<string,mixed>
     */
    protected function getFilterBounds(array $filters): array
    {
        return [];
    }

    /**
     * @param array<string,mixed> $filters
     *
     * @return array<string,mixed>
     */
    protected function transformInputFilters(array $filters): array
    {
        return $filters;
    }

    /**
     * @return ?ItemStorageInterface<ItemClass>
     */
    protected function getItemStorage()
    {
        return null;
    }

    /**
     * @param array<string,mixed> $filters
     */
    protected function fetchFilteredCount(array $filters): int
    {
        $storage = $this->getItemStorage();
        if (!$storage instanceof ItemStorageInterface) {
            throw new BadMethodCallException('Not default item storage given to perform filtered count.');
        }

        return $storage->countFiltered($filters);
    }

    /**
     * @param array<ItemClass> $list
     *
     * @return array<ItemInterface>
     */
    protected function postProcessFetched(array $list): array
    {
        return $list;
    }

    /**
     * @param array<string,mixed> $filters
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<ItemClass>
     */
    protected function fetchFiltered(array $filters, array $navigation): array
    {
        $storage = $this->getItemStorage();
        if (!$storage instanceof ItemStorageInterface) {
            throw new BadMethodCallException('Not default item storage given to perform filtered search.');
        }

        return $storage->fetchFiltered($filters, $navigation);
    }

    /**
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function getLimitFromNavigation(array $navigation): int
    {
        return $navigation['itemsPerPage'];
    }

    /**
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function getOffsetFromNavigation(array $navigation): int
    {
        return $navigation['itemsPerPage'] * $navigation['page'];
    }

    /**
     * @param array<string,mixed> $filters
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     * @param array<string> $sortFields
     *
     * @return array{numberOfPages:int,pages:array<int>,sort:array<string>,sortDirection:array<string>}
     */
    protected function getNavigationBounds(array $filters, array $navigation, array $sortFields): array
    {
        $numberOfItems = $this->fetchFilteredCount($filters);
        $numberOfPages = 1;
        if ($navigation['itemsPerPage'] > 0 && $numberOfItems > $navigation['itemsPerPage']) {
            $numberOfPages = ceil($numberOfItems / $navigation['itemsPerPage']);
        }

        return [
            'numberOfPages' => $numberOfPages,
            'numberOfItems' => $numberOfItems,
            'pages' => array_keys(array_fill(0, $numberOfPages, 1)),
            'sort' => $sortFields,
            'sortDirection' => ['', 'ASC', 'DESC'],
        ];
    }

    /**
     * @param array{page?:int|string,itemsPerPage?:int|string,sorting?:array<string,string>} $navigation
     * @param array<string,string> $defaultSorting
     *
     * @return array{page:int,itemsPerPage:int,sorting:array<string,string>}
     */
    protected function transformInputNavigation(array $navigation, array $defaultSorting = []): array
    {
        return [
            'page' => (int)($navigation['page'] ?? 0),
            'itemsPerPage' => (int)($navigation['itemsPerPage'] ?? 20),
            'sorting' => $navigation['sorting'] ?? $defaultSorting,
        ];
    }

    /**
     * @param array<int> $pages
     *
     * @return array<string|int>
     */
    protected function getPagesForPagination(array $pages, int $currentPage, int $totalPages): array
    {
        // Limit Pagination page links
        if ($totalPages > (4 * static::PAGINATION_ITEMS_EACH_SIDE + 3)) {
            $pages = [];
            $startPage = $currentPage - static::PAGINATION_ITEMS_EACH_SIDE;
            $endPage = $currentPage + static::PAGINATION_ITEMS_EACH_SIDE;

            if ($currentPage <= 2 * static::PAGINATION_ITEMS_EACH_SIDE + 1) {
                // Current page close to beginning
                $startPage = 0;
                $endPage = (3 * static::PAGINATION_ITEMS_EACH_SIDE) + 1;
            } elseif ($currentPage >= $totalPages - (2 * static::PAGINATION_ITEMS_EACH_SIDE + 2)) {
                // Current page close to end
                $startPage = $totalPages - (3 * static::PAGINATION_ITEMS_EACH_SIDE) - 2;
                $endPage = $totalPages - 1;
            }

            if ($startPage > 0) {
                $pages = array_keys(array_fill(0, static::PAGINATION_ITEMS_EACH_SIDE, 1));
            }

            if ($startPage > 1) {
                $pages[] = '...';
            }

            $pages = [...$pages, ...array_keys(array_fill($startPage, $endPage - $startPage + 1, 1))];
            if ($endPage < $totalPages - static::PAGINATION_ITEMS_EACH_SIDE) {
                $pages[] = '...';
            }

            if ($endPage < $totalPages - 1) {
                $pages = [...$pages, ...array_keys(array_fill($totalPages - static::PAGINATION_ITEMS_EACH_SIDE, static::PAGINATION_ITEMS_EACH_SIDE, 1))];
            }
        }

        return $pages;
    }

    /**
     * @param array<string,string> $defaultSorting
     */
    protected function setUpListView(string $defaultAction = 'list', array $defaultSorting = []): void
    {
        $this->addListScript();

        $page = $this->getPage();
        $filters = $this->getFilters();
        $navigation = $this->getNavigation();

        $transformedFilters = $this->transformInputFilters($filters);
        $transformedNavigation = $this->transformInputNavigation($navigation, $defaultSorting);
        $filterBounds = $this->getFilterBounds($transformedFilters);
        $navigationBounds = $this->getNavigationBounds($transformedFilters, $transformedNavigation, array_keys($defaultSorting));

        if ($page !== null) {
            $transformedNavigation['page'] = $page;
        }

        if ($transformedNavigation['page'] >= $navigationBounds['numberOfPages']) {
            $transformedNavigation['page'] = $navigationBounds['numberOfPages'] - 1;
        }

        $navigationBounds['pages'] = $this->getPagesForPagination($navigationBounds['pages'], $transformedNavigation['page'], $navigationBounds['numberOfPages']);

        $this->assignCurrentRouteData($defaultAction, $filters, $transformedNavigation);

        $this->viewData['filters'] = $filters;
        $this->viewData['navigation'] = $transformedNavigation;

        $this->viewData['filterBounds'] = $filterBounds;
        $this->viewData['navigationBounds'] = $navigationBounds;

        $list = $this->fetchFiltered($transformedFilters, $transformedNavigation);
        $this->viewData['list'] = $this->postProcessFetched($list);
    }

    protected function listAction(): Response
    {
        $this->setUpListView('list');

        return $this->render();
    }

    protected function editAction(): Response
    {
        throw new BadMethodCallException('Edit action not implemented in this controller');
    }

    protected function saveAction(): Response
    {
        throw new BadMethodCallException('Save action not implemented in this controller');
    }

    protected function deleteAction(): Response
    {
        $storage = $this->getItemStorage();
        if (!$storage instanceof ItemStorageInterface) {
            throw new BadMethodCallException('Delete action not implemented in this controller');
        }

        $ids = $this->getSelectedItems();
        $items = $storage->fetchByIdList($ids);
        foreach ($items as $item) {
            $storage->remove($item);
        }

        return $this->redirect('page.' . $this->section . '.list');
    }
}
