<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item(objectType="BANKACC")
 * @Dynamite\PartitionKeyFormat("BANKACC#{id}")
 * @Dynamite\SortKeyFormat("BANKACC")
 */
class BankAccount
{

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    private string $pk;

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    private string $sk;

    /**
     * @Dynamite\NestedValueObjectAttribute(type="Dynamite\Fixtures\Valid\CurrencyNestedValueObject", property="value", collection=true, name="cur")
     * @var CurrencyNestedValueObject[]
     */
    protected array $supportedCurrencies = [];

    public function addSupportedCurrency(CurrencyNestedValueObject $currencyNestedValueObject)
    {
        $this->supportedCurrencies[] = $currencyNestedValueObject;
    }

}