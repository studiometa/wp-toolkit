# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## v2.0.1 - 2024.03.09

### Fixed

- Remove some typing that can cause errors at runtime ([6053ce0](https://github.com/studiometa/wp-toolkit/commit/6053ce0))

## v2.0.0 - 2024.03.08

### Added

- Add an `EmailManager` to configure `PHPMailer` via environment variables ([#22](https://github.com/studiometa/wp-toolkit/pull/22))
- Add `enqueue_script($handle, $path)` and `enqueue_style($handle, $path)` method to the `AssetsManager` class ([#23](https://github.com/studiometa/wp-toolkit/pull/23))
- Add a `Plugin::disable` method to the `Plugin` helper class ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
- Add a `request` helper function  ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
- Add a `Request` helper class ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
- Add a `env` helper function  ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
- Add a `Env` helper class ([#26](https://github.com/studiometa/wp-toolkit/pull/26))

### Changed

- ⚠️ Rename the `PluginHelper` class to `Plugin` ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
- **CleanupManager:** Disable XML-RPC by default ([#21](https://github.com/studiometa/wp-toolkit/pull/21))
- Remove WordPress Code Standard sniffs for PHPCS ([#26](https://github.com/studiometa/wp-toolkit/pull/26))
