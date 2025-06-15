<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

/**
 * @template EndPointClass of EndPointInterface
 */
interface EndPointStorageInterface
{
    /**
     * @return ?EndPointClass
     */
    public function getEndPointByName(string $name): ?EndPointInterface;

    /**
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<EndPointClass>
     */
    public function getEndPointsFiltered(array $navigation): array;

    /**
     * @return array<EndPointClass>
     */
    public function getAllEndPoints(): array;

    public function getEndPointCount(): int;

    /**
     * @param array<int> $ids
     *
     * @return array<EndPointClass>
     */
    public function fetchByIdList(array $ids): array;

    /**
     * @return EndPointClass
     */
    public function createEndPoint(string $name): EndPointInterface;

    /**
     * @param EndPointClass $endPoint
     */
    public function addEndPoint(EndPointInterface $endPoint): void;

    /**
     * @param EndPointClass $endPoint
     */
    public function removeEndPoint(EndPointInterface $endPoint): void;

    /**
     * @param EndPointClass $endPoint
     */
    public function updateEndPoint(EndPointInterface $endPoint): void;
}
