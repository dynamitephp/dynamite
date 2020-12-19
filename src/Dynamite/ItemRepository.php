<?php
declare(strict_types=1);

namespace Dynamite;

use Dynamite\Exception\DynamiteException;
use Dynamite\Exception\ItemRepositoryException;
use Dynamite\Mapping\ItemMapping;
use Dynamite\Repository\AccessPatternsProviderInterface;

/**
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class ItemRepository
{
    private SingleTableService $singleTableService;
    private string $itemName;
    private ItemMapping $itemMapping;
    private ItemSerializer $itemSerializer;
    /**
     * @var AccessPattern[]
     */
    private array $accessPatterns = [];


    public function __construct(
        SingleTableService $singleTableService,
        string $itemName,
        ItemMapping $itemMapping,
        ItemSerializer $itemSerializer
    )
    {
        $this->singleTableService = $singleTableService;
        $this->itemName = $itemName;
        $this->itemMapping = $itemMapping;
        $this->itemSerializer = $itemSerializer;

        if ($this instanceof AccessPatternsProviderInterface) {
            $this->accessPatterns = $this->registerAccessPatterns();
        }
    }

    /**
     * When passing a string to $partitionKey or $sortKey, Dynamite will pass them to DB directly.
     * But when passing an array, it will build an key using data from SortKeyFormat and PrimaryKeyFormat annotations.
     * Remember to pass all "fragments" of key in array in given format:
     * [ fieldName => value ]
     *
     * @param array<string, string>|string $partitionKey
     * @param array<string, string>|string $sortKey
     * @return object
     */
    public function getItem($partitionKey, $sortKey): object
    {
        throw new \Exception('not implemented yet');
    }

    public function queryItem($partitionKey, $sortKey, ?string $indexName = null): object
    {
        throw new \Exception('not implemented yet');
    }

    public function getQueryBuilder()
    {
        throw new \Exception('not implemented yet');

    }

    /**
     * @param string $patternName
     * @param array $arguments
     * @return object|object[]
     * @throws \Exception
     */
    public function executeAccessPattern(string $patternName, array $arguments = [])
    {
        foreach ($this->accessPatterns as $accessPattern) {
            if ($accessPattern->getName() === $patternName) {
                if ($accessPattern->getOperation()->isQuery()) {
                    $queryResult = $this->singleTableService->simpleQuery(
                        $accessPattern->getPartitionKeyFormat(),
                        $accessPattern->getSortKeyFormat(),
                        $accessPattern->getIndex()
                    );

                    $output = [];
                    foreach ($queryResult as $item) {
                        $output[] = $this->itemSerializer->hydrateObject($this->itemName, $this->itemMapping, $item);
                    }

                    return $output;
                }

                throw new \Exception('access pattern operation not implemented yet');
            }
        }

        throw new \Exception('access pattern not found');
    }

    public function store(object $item)
    {
        if (!($item instanceof $this->itemName)) {
            throw ItemRepositoryException::objectNotSupported(get_class($item), $this->itemName);
        }

        $serializedValues = $this->itemSerializer->serialize($item, $this->itemMapping);
        $partitionKeyAttr = $this->itemMapping->getPartitionKeyProperty();
        $partitionKeyFormat = $this->itemMapping->getPartitionKeyFormat();
        $primaryKeyPlaceholders = [];

        foreach ($serializedValues as $property => $value) {
            if (is_array($value)) {
                foreach ($value as $valueKey => $val) {
                    if (is_array($val)) {
                        throw new DynamiteException('FIXME: more than one nested array');
                    }
                    $primaryKeyPlaceholders[sprintf('{%s.%s}', $property, $valueKey)] = $val;
                }
            } else {
                $primaryKeyPlaceholders[sprintf('{%s}', $property)] = $value;
            }
        }

        $partitionKeyValue = \str_replace(
            array_keys($primaryKeyPlaceholders),
            array_values($primaryKeyPlaceholders),
            $partitionKeyFormat
        );

        $sortKeyValue = null;
        if ($this->itemMapping->getSortKeyFormat() !== null) {
            $sortKeyValue = \str_replace(
                array_keys($primaryKeyPlaceholders),
                array_values($primaryKeyPlaceholders),
                $this->itemMapping->getSortKeyFormat()
            );
        }

        $serializedValues['objectType'] = $this->itemMapping->getObjectType();

        $this->singleTableService->putItem(
            $partitionKeyValue,
            $serializedValues,
            $sortKeyValue
        );


    }

}