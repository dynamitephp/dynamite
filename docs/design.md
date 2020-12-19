# Dynamite Design

How was dynamite designed? Which problems it solves? How does it solve?

## Problem: Primary key pair case transforming

DynamoDB is case-sensitive, so it means that items with PK `User#TonyStark` and `USER#TONYSTARK` will be two different items.
Dynamite can keep an eye of this problem and automatically lowercase/uppercase keys for you.
To achieve that, add an `transform` property to `PartitionKeyFormat` configuration:

```diff
use Dynamite\Configuration as Dynamite;

/**
 * @Dynamite\Item()
 * @Dynamite\ItemType('USER')
 * @Dynamite\PartitionKey('pk')
- * @Dynamite\PartitionKeyFormat('{itemType}#{username}')
+ * @Dynamite\PartitionKeyFormat('{itemType}#{username}', transform="upper")
 * @Dynamite\SortKey('sk')
 * @Dynamite\SortKeyFormat('{itemType}')
 */
class User {

}

```

While putting item, both `itemType` and `username` will be uppercased in Partition Key. This only works for attributes
injected to string, the rest of your partition key will be passed as-is; 

For `User#{username}` and username = `GolDfInCh1`, Key will be transformed to `User#GOLDFINCH1`.

To lowercase your attributes, use `transform="lower"`. This works also for `SortKeyFormat`.