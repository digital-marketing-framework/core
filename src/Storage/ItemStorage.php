<?php

namespace DigitalMarketingFramework\Core\Storage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

/**
 * @template ItemClass of ItemInterface
 *
 * @implements ItemStorageInterface<ItemClass>
 */
abstract class ItemStorage implements ItemStorageInterface
{
    /** @var array<string> */
    protected array $fields;

    protected ?GlobalConfigurationInterface $globalConfiguration = null;

    /**
     * @param class-string<ItemClass> $itemClassName
     */
    public function __construct(
        protected string $itemClassName,
    ) {
        $this->fields = array_keys(static::getSchema()->getProperties());
    }

    public function create(?array $data = null)
    {
        $item = new $this->itemClassName();
        if ($data !== null) {
            $this->updateItem($item, $data);
        }

        return $item;
    }

    /**
     * @param ItemClass $item
     */
    protected function getItemMethod(ItemInterface $item, string $field, string $type): string
    {
        $method = $type . ucfirst(GeneralUtility::underscoredToCamelCase($field));

        if (!method_exists($item, $method)) {
            throw new DigitalMarketingFrameworkException(sprintf('Method "%s" not found in class "%s"', $field, $item::class), 6460437990);
        }

        return $method;
    }

    /**
     * @param ItemClass $item
     * @param array<sting,mixed> $data
     */
    protected function updateItem($item, array $data): void
    {
        $fields = $this->fields;

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $method = $this->getItemMethod($item, $field, 'set');
            $item->$method($data[$field]); // @phpstan-ignore-line dynamic method call based on item schema
        }

        if (isset($data['uid'])) {
            $item->setId($data['uid']);
        }
    }

    /**
     * @param ItemClass $item
     *
     * @return array<string,mixed>
     */
    protected function getItemData($item): array
    {
        $data = [];

        foreach ($this->fields as $field) {
            $method = $this->getItemMethod($item, $field, 'get');
            $data[$field] = $item->$method(); // @phpstan-ignore-line dynamic method call based on item schema
        }

        return $data;
    }

    public function getGlobalConfiguration(): ?GlobalConfigurationInterface
    {
        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
