# Dynamite Item Repositories

## Accessing Item Repository

```php
/** @var \Dynamite\ItemManagerRegistry $itemManagerRegistry */
/** @var \Dynamite\ItemRepository $userRepository */
$userRepository = $itemManagerRegistry->getItemRepositoryFor(User::class);
```

By default you will get an instance of `ItemRepository` class which provides these methods:

### `ItemRepository#getItem`

Performs `dynamodb:GetItem` under the hood and returns a single object with given primary key, or `ItemNotFoundException`
if nothing found. 

You need to provide the exact partition key (and sort key if needed) to search for an item:
```php
/** @var \Dynamite\ItemRepository $userRepository */
$userRepository = $itemManagerRegistry->getItemRepositoryFor(User::class);

$user = $userRepository->getItem('USER#mickey', 'USER');
```

Or an arrays with parameters used in `PartitionKeyFormat` attribute:

```php
// Assuming PartionKeyFormat('USER#{username}')

/** @var \Dynamite\ItemRepository $userRepository */
$userRepository = $itemManagerRegistry->getItemRepositoryFor(User::class);

$user = $userRepository->getItem(['username' => 'mickey'], 'USER');
```

> [!NOTE]  
> You can pass an array in both partion key and sort key.