# Handling DateTime Objects

When you need to store `DateTime` values, use `\DateTimeInterface` as your property typehint and  `datetime`
or `timestamp` as your attribute type. Use `format` property to format your value. 

````
use DateTimeInterface;
use Dynamite\Configuration as Dynamite;


class Employee {
    
    /**
     * @Dynamite\Attribute(name="hired", type="timestamp")
     */
    protected DateTimeInterface $hiredAt;

    /**
     * @Dynamite\Attribute(name="birth", type="datetime", format="Y-m-d")
     */
    protected DateTimeInterface $birthDate;

}

````

When using `datetime`, a `format` property is required. [Default Date formating apply.](https://www.php.net/manual/en/datetime.format.php)

`timestamp` acts as a `datetime` field with forced `U` format.


