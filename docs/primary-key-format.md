# Primary Key Formatting

There is a chance that your partition key (or sort key) will be a string consisting from some item properties glued 
(and probably lower/uppercased) together, shaped to meet some access patterns. Dynamite can help you with this by 
taking values from your item, transforming them, and finally filling them into your key format string.

## Item values interpolation

Inject your class properties names wrapped with `{}` to substitute it with given property value.

Take a look for this example:

````
/**
 * @Dynamite\PartitionKeyFormat("USER#{id}")
 * @Dynamite\SortKeyFormat("USER")
*/
class User {

    protected string $id = "mickey";

}
````
When saved in DB, primary key will be looking as follows:
- Partition Key = `User#mickey`
- Sort Key = `USER`

You can use as many interpolations in both Partition Key and Sort Key as you want. 

## Transform item values before interpolation

There is also a feature that allows you to automatically apply some filters to substituted values before interpolation.
To enable a filter, append a filter name, and a colon before property name.


This example will result with an item with PK `USER#MIKI@EXAMPLE.COM` saved in DB:
````
/**
 * @Dynamite\PartitionKeyFormat("USER#{upper:email}")
 * @Dynamite\SortKeyFormat("USER")
*/
class User {

    protected string $email = "miki@example.com";

}
````


You can add more than one filter:
````
/**
 * @Dynamite\PartitionKeyFormat("USER#{upper:md5:email}")
 * @Dynamite\SortKeyFormat("USER")
*/
class User {

    protected string $email = "miki@example.com";

}
````

But heeey, watch for filter order! Things are processed from left to right, so `{upper:md5:email}` will result with totally
different value than `{md5:upper:email}`.

### Available filters:
- `upper`
- `ucfirst`
- `lower`
- `md5`,
- `date_Y`
- `date_m`
- `date_d`
- `date_H`
- `date_i`
- `date_s`


## Special characters protection

If you use some special chars in your access patterns, and there might be a chance that given chars would be present in
interpolated value, you can restrict them in Dynamite configuration while instantiating the whole library:


```
$dynamiteFactory->enableSpecialCharProtection([
    '#',
    '!',
    '$$'  // more than one character is also allowed!
})
```
When enabled, an Exception will be thrown when one of phrases will appear while interpolation.






