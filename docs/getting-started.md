# Getting Started with Dynamite

## Installation and configuration

Use composer to download a package:

``composer require dynamite/dynamite``

Create new instance of `Dynamite\ItemManagerRegistryFactory` class and wire them with some services:

```php
$dynamiteFactory = new Dynamite\ItemManagerRegistryFactory();

// Logger (for logging purposes)
$dynamiteFactory->withLogger($myLogger);

// doctrine/annotations Reader (for reading mapping from annotations, Doctrine ORM style):
$dynamiteFactory->withAnnotationReader($myReader);
```

Then prepare your table(s) schema, and put them into a `TableSchema` object:

```php
$tableSchema = new \Dynamite\TableSchema(
    'MyProjectSingleTableName', // DynamoDB Table name (not ARN or something, a name!)
    'pk', // Partition Key
    'sk',  // Sort Key Name
    [ // GSI/LSI indexes (optional)
        'GSI1' => ['pk' => 'gsi1pk', 'sk' => 'gsi1sk'],
        'GSI2' => ['pk' => 'gsi2pk', 'sk' => 'gsi2sk']
    ]   
);
```

We are almost home! We need to define a Item Manager:

```php
/** @var \Dynamite\ItemManagerRegistryFactory $dynamiteFactory **/
/** @var \Dynamite\TableSchema $tableSchema **/
/** @var \Aws\DynamoDb\DynamoDbClient $myDynamoDbClient **/

// At this moment leave this array empty, we will come back here later 
$managedItems = [];

$dynamiteFactory->addNativeDynamoDbClientItemManager(
    $myDynamoDbClient,
    $tableSchema,
    $managedItems
);
```

Build an instance of `ItemManagerRegistry`:

```php
/** @var \Dynamite\ItemManagerRegistryFactory $dynamiteFactory **/

$itemManagerRegistry = $dynamiteFactory->build();
```


And we are done! Next step is to configure a new item. 


## Creating an Item

Start with configuring some annotations at class level:

```diff
<?php

namespace App\Domain;

use Dynamite\Configuration as Dynamite; 

/**
 * @Dynamite\Item(objectType="USERINVITATION")
 * @Dynamite\PartitionKeyFormat("USER#{userId}#INVITATION")
 * @Dynamite\SortKeyFormat("INV#{invitationId}")
 */
class UserInvitation {

}

```

`Item` and `PartitionKeyFormat` are required, `SortKeyFormat` is required only if there is an sort key in your table.

Next, add some properties to your class, and some annotations which map them to DynamoDB Attributes:

```
class UserInvitation {
+    /**
+     * @Dynamite\Attribute(name="id", type="string")
+     * @var string 
+     */
+    protected string $invitationId;
+   
+   /**
+     * @Dynamite\Attribute(name="uid", type="string")
+     * @var string 
+     */
+    protected string $userId;
}
```











Your placeholders in `PartitionKeyFormat` and `SortKeyFormat` are referring to class properties. 