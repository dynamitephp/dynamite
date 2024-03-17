<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;

#[Dynamite\Item(objectType:"BANKACC")]
#[Dynamite\PartitionKeyFormat("BANKACC#{id}")]
#[Dynamite\SortKeyFormat("BANKACC")]
class BankAccount
{
    #[Dynamite\PartitionKey()]
    private string $pk;

    #[Dynamite\SortKey()]
    private string $sk;

    /**
     * @var CurrencyNestedValueObject[]
     */
    #[Dynamite\NestedValueObjectAttribute(
        type: "Dynamite\Fixtures\Valid\CurrencyNestedValueObject",
        name: "cur",
        property: "value",
        collection: true
    )]
    protected array $supportedCurrencies = [];

    public function addSupportedCurrency(CurrencyNestedValueObject $currencyNestedValueObject)
    {
        $this->supportedCurrencies[] = $currencyNestedValueObject;
    }

    /**
     * @return CurrencyNestedValueObject[]
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }
}