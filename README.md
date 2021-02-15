# dynamite/dynamite

Work with AWS DynamoDB and Single-Table-Designed tables in your apps.

Requires `aws/aws-sdk-php` with `3.*` version. 

## Some important things you need to know:

### Your table schema
- Dynamite assumes that there is a table with partition key and sort key created.
- When developing locally, use [DynamoDB Local](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/DynamoDBLocal.html).
- Dynamite assumes that tables/indexes are created and active.

### You need to bring your own ID Generator
There are some proposals you can use:

#### 1. [ramsey/uuid](https://github.com/ramsey/uuid)
  - Literally an UUID
  - Really low chance of collision
  
#### 2. [robinvdvleuten/ulid](https://github.com/robinvdvleuten/php-ulid)  ([**see ULID spec**](https://github.com/ulid/spec))
  - Time-based
  - Lexicographically sortable
  - UUID compliant
  - Really low chance of collision
  - Higher precision than KSUID
#### 3. [tuupola/ksuid](https://github.com/tuupola/ksuid)  ([**Read more about KSUID**](https://github.com/segmentio/ksuid))
  - Time-based
  - Lexicographically sortable
  - Naturally ordered
  - Really low chance of collision

## The unordered roadmap to `v1.0.0`
- [x] Creating an item
- [ ] Document how to create an item
- [x] Storing an item
- [ ] Document how to store an item
- [x] Getting item
- [ ] Document how to get an item
- [x] `ItemRepository`
- [ ] Access Pattern Operations
- [ ] Bulk operations
- [ ] Condition Expressions
- [ ] Unit of Work
- [ ] Support for all operations in `SingleTableService`
- [x] `DynamiteRegistry` to work with more than one table
- [ ] Psalm pipeline must be green
- [ ] PHPUnit coverage > 90%
- [ ] Support for PHP8 Attributes 
- [x] `@DuplicateTo` annotation - duplicate some attributes to additional items while putting them to DynamoDB
- [ ] Some Console commands for Items mapping validation and project maintenance


## Documentation

### Item Duplication

**Item duplication requires `dynamodb:BatchWriteItem` enabled.**

When you need to duplicate an object to many items at once, you can use ``@DuplicateTo`` annotation:

````
/**
 * @Dynamite\Item(objectType="USER")
 * @Dynamite\PartitionKeyFormat("USER#{id}")
 * @Dynamite\SortKeyFormat("USER")
 * @Dynamite\DuplicateTo(pk="UDATA#{email}", sk="UDATA", props={"id", "username"})
 * @Dynamite\DuplicateTo(pk="UDATA#{username}", sk="UDATA", props={"id", "email"})
*/
class User {
    //...props
}

$user = new User('123', 'user@example.com', 'mickey')
````

In this case, There will be 3 items sent to DynamoDB:
- PK: `USER#123` SK:`USER` with **all** attributes defined in item;
- PK: `UDATA#user@example` SK: `UDATA` with `id` and `username` props;  
- PK: `UDATA#mickey` SK: `UDATA` with `id` and `email` props.


You can add a `transform` param to annotation to fill Primary key pair with lowercased/uppercased params:


````

//In this case PK: UDATA#MICKEY 
@Dynamite\DuplicateTo(pk="UDATA#{username}", sk="UDATA", props={"id", "email"}, transform="UPPER")

//In this case PK: UDATA#mickey
@Dynamite\DuplicateTo(pk="UDATA#{username}", sk="UDATA", props={"id", "email"}, transform="LOWER") 
````

By default params are passed as-is. `transform` works only for params injected to key, key template remains untouched.


### Creating an Item

@TODO

### Annotations

Dynamite uses `doctrine/annotation` under the hood to parse all item annotations to provide `doctrine/orm`-like mapping configuration.

#### PrimaryKeyFormat and SortKeyFormat 

Allows you to define the format of primary key pair of Item stored in table.

**When Partition Key or Sort Key will be passed to build Partition Key or Sort Key, Dynamite will break.**

```
/**
 * Use class properties from your object wrapped with {} as a placeholders for values.
 * Warning: {itemType} will be taken from @ItemType annotation.
 * @Item(objectType="USER")
 * @PartitionKeyFormat('{itemType}#{username}')
 * @SortKeyFormat('{itemType}')
 */
class User {
    
    public $email;
    
    public $username;

}

$user = new User();
$user->username = 'tonystark';
//In this example, object will be stored with "USER#tonystark" PK and "USER" Sort key
```


### Nested items

Nested item is... an item nested in another item. It cannot have a Partition Key as it would be taken from parent (or first
non-nested item when multiple nested) object. 

Nested item cannot have both `@Item` and `@NestedItem` annotation.


## Ideas for future:
- `NestedValueObjectAttribute#type should not be required when property is defined`

## License 

MIT
