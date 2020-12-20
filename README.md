# pizzaminded/dynamite

Work with AWS DynamoDB and Single-Table-Designed tables in your apps.

Requires `aws/aws-sdk-php` with `3.*` version. 

## Some important things you need to know:

- DynamoDB is a NoSQL database, and it does not have JOINs. 
- Dynamite is not an ORM. 
- Dynamite is just a wrapped DynamoDBClient with some features that i find usable during my adventure with DynamoDB and PHP. 
- You still need to bring some additional tools like UUID Generator.
- DynamoDB does not have `AUTO_INCREMENT` feature. 
- Dynamite assumes that there is a table with given name and primary key pair created. 
- When developing locally, use [DynamoDB Local](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/DynamoDBLocal.html).
- **Dynamite Assumes that tables/indexes are created and active.**


## The unordered roadmap to `v1.0.0`
- [x] Creating an item
- [ ] Document how to create an item
- [x] Storing an item
- [ ] Document how to store an item
- [x] Getting item
- [ ] Document how to get an item
- [ ] `ItemRepository`
- [ ] Access Pattern Operations
- [ ] Bulk operations
- [ ] Condition Expressions
- [ ] Unit of Work
- [ ] Support for all operations in `SingleTableService`
- [ ] `DynamiteRegistry` to work with more than one table
- [ ] Psalm pipeline must be green
- [ ] PHPUnit coverage > 90%
- [ ] Support for PHP8 Attributes 
- [ ] `@DenormalizeTo` annotation - duplicate some attributes to additional items while putting them to DynamoDB
- [ ] Some Console commands for Items mapping validation and project maintenance


## Documentation


### Gotchas
- When any of attribute is instance of \DateTimeInterface, it would be converted to timestamp

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
