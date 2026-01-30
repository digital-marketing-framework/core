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
    protected const UID_FIELD = 'uid';

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
     * Map a data (array) field value to an item (object) field value.
     */
    protected function mapDataField(string $name, mixed $value): mixed
    {
        return $value;
    }

    /**
     * Map an item (object) field value to a data (array) field value.
     */
    protected function mapItemField(string $name, mixed $value): mixed
    {
        return $value;
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

            $value = $this->mapDataField($field, $data[$field]);
            $method = $this->getItemMethod($item, $field, 'set');
            $item->$method($value); // @phpstan-ignore-line dynamic method call based on item schema
        }

        if (isset($data[static::UID_FIELD])) {
            $item->setId($data[static::UID_FIELD]);
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
            $value = $item->$method(); // @phpstan-ignore-line dynamic method call based on item schema

            $data[$field] = $this->mapItemField($field, $value);
        }

        return $data;
    }

    public function getGlobalConfiguration(): GlobalConfigurationInterface
    {
        if (!$this->globalConfiguration instanceof GlobalConfigurationInterface) {
            throw new DigitalMarketingFrameworkException('Global configuration not injected into item storage!');
        }

        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }
}
