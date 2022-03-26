# Getting Started with Dynamite

## 1. Installation and configuration

Use composer to download a package:

``composer require dynamite/dynamite``

Then prepare your table(s) schema, and put them into a `TableSchema` object:


````php
$tableSchema = new \Dynamite\TableSchema(
    'MyProjectSingleTableName', // DynamoDB Table name (not ARN or something, a name!)
    'pk', // Partition Key
    'sk',  // Sort Key Name
    [ // GSI/LSI indexes (optional)
        'GSI1' => ['pk' => 'gsi1pk', 'sk' => 'gsi1sk'],
        'GSI2' => ['pk' => 'gsi2pk', 'sk' => 'gsi2sk']
    ]   
);


````

## 3. Creating an Item

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