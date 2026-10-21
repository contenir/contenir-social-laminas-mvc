# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased] - 2026-10-21

### Changed

- Requires contenir/social `^0.1`, from its repository until it is listed on Packagist.

### Added

- `socialProfiles()`, which writes the site's profiles from `config['settings']`, as the CMS writes them, including
  accounts the core Social Media Links plugin stored as handles.
- `share()`, which writes the page's share buttons from the site's share settings and the page's own metadata
  (`og:url`, the canonical link or the current address; `og:title` or the title; `og:description` or the
  description; `og:image`), and, once per page, the script for "Copy link" and "Share…" with the page's CSP nonce.
- `NonceProviderInterface`, for an application to give the nonce once.
- Default options for both helpers under `contenir_social.options`.
