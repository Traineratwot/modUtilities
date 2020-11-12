# Changelog
## [2.8.3] - 2020-11-06
   * add
     - modutilitiesRest
       - support config key in permission and param [[++key]]
   * fix
     - modutilities
       - jsonValidate fix
## [2.8.2] - 2020-11-06
   * add
     - settings
       - use_modUtilFrontJs_resource
       - use_modUtilFrontJs_user
   * fix
     - modutilities.js
       - security fix
## [2.8.0] - 2020-11-05
   * change
     - modutilities.js
       - move js from page to file* 
   * add
      - modutilities.js
        - modx.util.convert - аналог $modx->util->convert()
## [2.7.7] - 2020-11-03
   ###fixed
   * plugin 
        - modutilities
        - modUtilitiesPathGen
   * function
        - member
        - likeString
        - arrayToSqlIn
   ### add   
   * function
        - urlBuild
        - randomColor
        - jsonValidate
## [2.7.2] - 2020-08-31
   ###fixed
   * plugin 
     - modUtilitiesPathGen
   * change
     - modutilities.js
       - change: modx.util.moseX => modx.util.mouse.moseX  
       - change: modx.util.moseU => modx.util.mouse.moseY  
       - add: modx.util.mouse.pageX  
       - add: modx.util.mouse.pageX
       
## [2.7.2] - 2020-08-31
   ###fixed
   * function 
     - translit(add 2 param isCpu) and faster
## [2.7.0] - 2020-08-31
### Added
   * csv class
     - bugfix
     - getAssoc()
## [2.6.20] - 2020-08-31
   ### Added
   * function 
     - randomColor()
     - download()
     - baseExt()
     - baseName()
     - expandBrackets()
     - updateTv()
   * csv class
     - bugfix
     - rainbow for html table
   * rest class
     - bugfix
     - files class
   ### bugFix
## [2.6.9] - 2020-08-31
   bugFix
## [2.6.1] - 2020-08-20 update csv
### Added
   fast mod in csv class
   ```
   $csv = $modx->util->csv(['mode' => 'fast', 'output_file' => 'output/output.csv']);
   ```
## [2.6.0] - 2020-08-20 BIG UPDATE
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
