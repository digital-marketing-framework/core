<?php

namespace DigitalMarketingFramework\Core\Api\EndPoint;

use DigitalMarketingFramework\Core\Model\Api\EndPoint;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;

class EndPointStorage implements EndPointStorageInterface
{
    /** @var array<string,EndPointInterface> */
    protected array $endpoints = [];

    public function getEndPointByName(string $name): ?EndPointInterface
    {
        return $this->endpoints[$name] ?? null;
    }

    public function getAllEndPoints(): array
    {
        return $this->endpoints;
    }

    public function getEndPointsFiltered(array $navigation): array
    {
        $limit = $navigation['itemsPerPage'];
        $offset = $navigation['itemsPerPage'] * $navigation['page'];

        if ($limit === 0) {
            return $this->endpoints;
        }

        return array_slice($this->endpoints, $offset, $limit);
    }

    public function fetchByIdList(array $ids): array
    {
        $result = [];
        foreach ($this->endpoints as $endPoint) {
            if (in_array($endPoint->getId(), $ids, true)) {
                $result[] = $endPoint;
            }
        }

        return $result;
    }

    public function createEndPoint(string $name): EndPointInterface
    {
        return new EndPoint($name);
    }

    public function addEndPoint(EndPointInterface $endPoint): void
    {
        $this->endpoints[$endPoint->getName()] = $endPoint;
    }

    public function removeEndPoint(EndPointInterface $endPoint): void
    {
        unset($this->endpoints[$endPoint->getName()]);
    }

    public function updateEndPoint(EndPointInterface $endPoint): void
    {
        $this->endpoints[$endPoint->getName()] = $endPoint;
    }

    public function getEndPointCount(): int
    {
        return count($this->endpoints);
    }
}
