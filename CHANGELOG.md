# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.4] - 2022-06-05
### Changed:
- Renamed `Dynamite` class to `ItemManager`
- Renamed `DynamiteRegistry` to `ItemManagerRegistry`

### Removed
- Moved Symfony Bundle to separate project

## [0.0.3] - 2021-03-13
### Added:
- Changing case of params injected to keys in `@DuplicateTo`

## [0.0.2] - 2021-01-30
### Added
- Support for collection of objects passed to `NestedValueObjectAttribute`
### Fixed
- `AbstractAttribute#assertPropertiesPresence` now returns an exception with valid Attribute FQCN
- changed test paths in psalm config


## [0.0.1] - 2021-01-09
### Added
- DynamiteRegistry for managing multiple DynamoDbClients
- Added `DuplicateTo` annotation handling
- Added `DynamiteTestSuiteHelperTrait` util for unit tests
- Added Symfony bundle and `jadob/jadob` service provider
- Added `SingleTableService#writeRequestBatch` method
- Added class stubs for DynamoDB API requests/responses 
- Added `QueryIterator`
- Added `CachedItemMappingReader`
- Getting and putting an item in `ItemRepository`
- Support for custom `ItemRepository` classes

### Changed
- New project directory structure
- NestedItem config is now stored in `ItemMapping`

## [0.0.0] - 2020-12-20
### Added
- Extracted and open-sourced project stubs. 
