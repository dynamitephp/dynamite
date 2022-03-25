# Bring your own ID generator

DynamoDB does not offer any `SERIAL` or `AUTO_INCREMENT` features, so it is required to use your own. 

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
- Really low chance of collision (but much higher comparing to ULID)