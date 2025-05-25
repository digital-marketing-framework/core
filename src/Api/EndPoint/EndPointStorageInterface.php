<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

interface EndPointStorageInterface
{
    public function getEndPointByName(string $name): ?EndPointInterface;

    /**
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<EndPointInterface>
     */
    public function getEndPointsFiltered(array $navigation): array;

    /**
     * @return array<EndPointInterface>
     */
    public function getAllEndPoints(): array;

    public function getEndPointCount(): int;

    /**
     * @param array<int> $ids
     *
     * @return array<EndPointInterface>
     */
    public function fetchByIdList(array $ids): array;

    public function createEndPoint(string $name): EndPointInterface;

    public function addEndPoint(EndPointInterface $endPoint): void;

    public function removeEndPoint(EndPointInterface $endPoint): void;

    public function updateEndPoint(EndPointInterface $endPoint): void;
}
