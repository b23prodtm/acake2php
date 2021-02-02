# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

# 2.13.0 - 2018-05-24

# v4.7.1
## (2020-10-22)

* Add deprecation note to Readme [Paulo Castro]

# v4.7.0
## (2020-09-29)

* Add BalenaSettingsPermissionError [josecoelho]

# v4.6.0
## (2020-09-24)

* Add Organization membership role not found error [Amit Solanki]

# v4.5.1
## (2020-09-10)

* karmaConfig: fix tests against node 10 [JSReds]
* Fix catch-uncommited check on newer balenaCI [Thodoris Greasidis]
* Test on travis against more modern node versions [Thodoris Greasidis]
* balena-config-karma: update to v3.0.0 [JSReds]

# v4.5.0
## (2020-09-08)

* Errors: add authentication errors [JSReds]

# v4.4.1
## (2020-07-03)

* Update dependencies [Pagan Gazzard]

# v4.4.0
## (2020-05-01)

* Add Application membership role not found error [Amit Solanki]

# v4.3.2
## (2020-04-03)

* Mark BalenaSupervisorLockedError token parameter as optional [Pagan Gazzard]

# v4.3.1
## (2020-04-03)

* Use balena-lint for linting [Pagan Gazzard]

# v4.3.0
## (2020-03-25)

* Allow string argument in the BalenaReleaseNotFound constructor [Thodoris Greasidis]

## 4.2.1 - 2020-01-20

* Remove the token from the error message to avoid leaking [Pagan Gazzard]

## 4.2.0 - 2019-09-12

* Add BalenaOrganizationNotFound [Thodoris Greasidis]

## 4.1.2 - 2019-09-12

* Explicitly include build output in packaged releases [Thodoris Greasidis]
* Test that the non-ignored emitted output is committed [Thodoris Greasidis]
* Use a prepare step that doesn't run tests, to be balenaCI compliant [Thodoris Greasidis]
* Exclude the build output from the repo [Thodoris Greasidis]
* Add missing build & README output [Thodoris Greasidis]

## 4.1.1 - 2019-09-11

* Bump TS version for tests & test typings against the previous version [Thodoris Greasidis]

## 4.1.0 - 2019-05-31

* Add BalenaAmbiguousRelease error [Thodoris Greasidis]

## v4.0.1 - 2018-10-23

* Emit ES6 TS output [Tim Perry]

## v4.0.0 - 2018-10-22

* Require Node 6+ [Tim Perry]
* Generate type definitions & update typed-error [Tim Perry]

## v3.0.0 - 2018-10-16

* Remove the deprecated BuildNotFound error [Tim Perry]
* Rename everything 'resin' to 'balena' [Tim Perry]

* Add ResinServiceNotFound

# 2.12.0 - 2017-11-14

- Add ImageNotFound error, for multicontainer

# 2.11.0 - 2017-11-13

- Add ReleaseNotFound error, and deprecate BuildNotFound, for multicontainer

# [2.10.0] - 2017-09-22

- Add `ResinDiscontinuedDeviceType` error

# [2.9.0] - 2017-09-05

- Add `ResinSupervisorLockedError` error
- Use the resin.io shared typings packages

# [2.8.0] - 2017-08-02

- Converted to TypeScript
- Switched to testing in Chrome Headless, updated the list of browsers used for testing in Sauce Labs

# [2.7.0] - 2017-07-03

- Add `ResinInvalidParameterError` error.

# [2.6.0] - 2017-03-09

- Stop running tests for Node < 4
- Add `ResinBuildNotFound` error.

## [2.5.0] - 2017-02-21

- Include request options in `ResinRequestError`

## [2.4.0] - 2016-04-28

- Add `ResinAmbiguousApplication` error.

## [2.3.0] - 2016-03-03

- Add `ResinExpiredToken` error.

## [2.2.0] - 2016-01-24

- Add `statusCode` property to `ResinRequestError`.

## [2.1.0] - 2016-01-12

### Added

- Add `ResinAmbiguousDevice` error.

## [2.0.1] - 2015-12-04

### Changed

- Omit tests from NPM package.

[2.10.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.9.0...v2.10.0
[2.9.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.8.0...v2.9.0
[2.8.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.7.0...v2.8.0
[2.7.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.6.0...v2.7.0
[2.6.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.5.0...v2.6.0
[2.5.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.4.0...v2.5.0
[2.4.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/resin-io-modules/resin-errors/compare/v2.0.1...v2.1.0
[2.0.1]: https://github.com/resin-io-modules/resin-errors/compare/v2.0.0...v2.0.1
