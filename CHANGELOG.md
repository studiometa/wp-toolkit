# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Helpers:**
  + Add a `RequestHelper` class 
  + Add a `request(): Request` function based on [symfony/http-foundation](https://github.com/symfony/http-foundation)
  + Add an `EnvHelper` class 
  + Add an `env( string $key ): string` function 
- **CleanupManager:** Disable XML-RPC by default ([#21](https://github.com/studiometa/wp-toolkit/pull/21))
