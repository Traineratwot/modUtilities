# Changelog

## [2.5.7] - 2020-08-20 BIG UPDATE
### Fixed
  - fix many warning error in modx log   
  - fix csv escape special charset
  - fix REST log
  - fix transport package
### Added
  - class modutilitiesPostFiles
  - modutilities.js - some usefull function for frontend
  - settings
### Renamed
  - modUtilities::print => modUtilities::dump
## [2.2.2] - 2020-07-14

### Added

- fenom modificator util
- function getAllTvValue
- function getAllTvResource
- function getResourceChildren
- function arrayToSqlIn


## [2.1.9] - 2020-06-19
### Added
- REST api ip filter
### Fixed
- bug Fixes

## [2.1.5] - 2020-06-14

### Added

- REST api controller

### Fixed

- bug Fixes

## [2.0.4] - 2020-06-10

### Added

- Add phpUnit test mb_ucfirst

### Changed

- mb_ucfirst 
	add mode FirstLetter,EveryWord,AfterDot


## [2.0.3] - 2020-06-09

### Added
- Add function `util->getUserPhoto()`
- Add phpUnit Test
### Fixed

- Rename class `utilities` on `modutilities`
- Rename class `utilitiesCsv` on `modutilitiesCsv`
- Fixed an error when duplicating a class

## [2.0.2] - 2020-06-08

### Changed

- class csv add method `csv->toHtml('classname')`
