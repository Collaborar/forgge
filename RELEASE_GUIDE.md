# Release Guide

This guide covers all the steps required to release a new version for all packages. The order of packages must be followed but packages that do not have a new version to be released can be skipped (keeping the rest in order).

## 1. collaborar/forgge

1. Update and commit FORGGE_VERSION in `config.php` with the latest version.
2. Create a new release: https://github.com/Collaborar/forgge/releases/new

## 2. collaborar/forgge-cli

1. Update and commit `composer.json` with the latest version of this package (otherwise packagist.org will not update).
2. Create a new release: https://github.com/Collaborar/forgge-cli/releases/new

## 3. collaborar/forgge-blade

1. Update and commit `composer.json` with the latest `collaborar/forgge` version requirement.
2. Create a new release: https://github.com/Collaborar/forgge-blade/releases/new

## 4. collaborar/forgge-twig

1. Update and commit `composer.json` with the latest `collaborar/forgge` version requirement.
2. Create a new release: https://github.com/Collaborar/forgge-twig/releases/new

## 5. collaborar/forgge-app-core

1. Update and commit `composer.json` with the latest `collaborar/forgge` version requirement.
2. Create a new release: https://github.com/Collaborar/forgge-app-core/releases/new

## 6. collaborar/forgge-theme

1. Run `yarn i18n`.
2. Update `composer.json` with the latest version requirements for:
    - `collaborar/forgge`
    - `collaborar/forgge-app-core`
    - `collaborar/forgge-cli`
3. Update `composer.json` with the latest version of this package (otherwise packagist.org will not update).
4. Update call to `my_app_should_load_forgge()` with the latest minimum version required.
5. Commit.
6. Create a new release: https://github.com/Collaborar/forgge-theme/releases/new
7. Update the composer example in the Quickstart docs for Bedrock with the exact new version number.

## 7. collaborar/forgge-plugin

1. Run `yarn i18n`.
2. Update `composer.json` with the latest version requirements for:
    - `collaborar/forgge`
    - `collaborar/forgge-app-core`
    - `collaborar/forgge-cli`
3. Update `composer.json` with the latest version of this package (otherwise packagist.org will not update).
4. Update call to `my_app_should_load_forgge()` with the latest minimum version required.
5. Commit.
6. Create a new release: https://github.com/Collaborar/forgge-plugin/releases/new
