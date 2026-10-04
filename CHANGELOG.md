# Changelog

## 2.0.13 - 2026-10-05

### Added
- Added a standalone Craft integration test harness and regression coverage for count, provider and share-button behavior.

### Changed
- Updated the required version of `verbb/base` to 3.0.19.
- Replaced the CodeKit stylesheet build with Vite and moved web assets to `src/web`.

### Fixed
- Replaced inline share-button JavaScript with CSP-compatible frontend behavior.
- Reduced Facebook share-count requests by caching app access tokens.
- Fixed SMS and email share links encoding spaces incorrectly.
- Fixed failed count scrapes emitting PHP deprecation warnings and normalized cache duration values.
- Fixed incorrect follower-count and OAuth provider documentation.
- Stopped unsupported Reddit, Spotify and Yummly count requests from triggering outbound calls.
- Fixed the post-connect redirect for OAuth providers.
- Fixed minimum share counts hiding freshly fetched counts.
- Fixed multiple moderate-severity resource consumption vulnerabilities.
- Fixed a low-severity resource consumption vulnerability.
- Fixed social count requests potentially waiting indefinitely for third-party providers.
- Fixed a low-severity share URL generation vulnerability.
- Fixed a low-severity sensitive information exposure vulnerability.

## 2.0.12 - 2026-09-30

### Changed
- Mailchimp and Envato follower counts now require API credentials from the site’s own provider accounts.
- Route plugin settings through the plugin’s authorized settings controller.

### Fixed
- Fixed a moderate-severity resource consumption vulnerability.
- Fixed an information disclosure vulnerability.
- Fixed a source credential exposure vulnerability.
- Fixed authorization and request assignment for provider management.
- Fixed OAuth callback transaction validation.
- Fixed authorization for connecting and disconnecting OAuth providers.
- Fixed OAuth callback redirects being evaluated as Twig templates.

### Removed
- Removed Behance and Vimeo follower counts, and LinkedIn and X (Twitter) share counts because their upstream APIs no longer support them.

## 2.0.11 - 2026-09-20

### Fixed
- Fix saving provider settings after settings layout changes.

## 2.0.10 - 2026-09-14

### Changed
- Align documentation filenames with page titles and update internal links.
- Updated documentation for clearer, more consistent guidance.
- Clarified optional PHP configuration with focused examples and linkable setting details.

### Fixed
- Fix the providers admin table after settings normalization.

## 2.0.9 - 2026-09-13

### Changed
- Normalize plugin settings.

## 2.0.8 - 2026-05-03

### Changed
- Bump `verbb/auth` to allow `firebase/php-jwt` 7.x.

## 2.0.7 - 2026-02-07

### Fixed
- Fix a redirect error when connecting to a provider in the control panel.
- Fix an error when creating a new provider.

## 2.0.6 - 2025-11-06

### Fixed
- Fix WhatsApp share link including `+` character for a custom text link.

## 2.0.5 - 2025-07-18

### Changed
- Update English translations.
- Bump `verbb/auth`.

## 2.0.4 - 2025-03-04

### Added
- Add Bluesky provider.

## 2.0.3 - 2024-12-04

### Added
- Add `Provider::getButtonAttributes()` to modify button HTML attributes.

### Fixed
- Fix Print button not working correctly.

## 2.0.2 - 2024-09-07

### Fixed
- Fix an issue when rendering share buttons and some attributes not being present.

## 2.0.1 - 2024-05-29

### Added
- Add support for `headlessMode` redirect URIs.

### Changed
- Update English translations.

## 2.0.0 - 2024-05-13

### Changed
- Now requires PHP `8.2.0+`.
- Now requires Craft `5.0.0+`.

## 1.0.10 - 2026-05-03

### Changed
- Bump `verbb/auth` to allow `firebase/php-jwt` 7.x.

## 1.0.9 - 2025-07-18

### Changed
- Update English translations.
- Bump `verbb/auth`.

## 1.0.8 - 2025-03-04

### Added
- Add Bluesky provider.

## 1.0.7 - 2024-12-04

### Added
- Add `Provider::getButtonAttributes()` to modify button HTML attributes.

### Fixed
- Fix Print button not working correctly.

## 1.0.6 - 2024-05-29

### Changed
- Update English translations.

## 1.0.5 - 2024-04-29

### Added
- Add support for `headlessMode` redirect URIs.

### Changed
- Update English translations.

## 1.0.4 - 2024-04-05

### Added
- Add improved session-handling for authorization and callback methods, to improve failed sessions in some cases.

## 1.0.3 - 2023-09-13

### Added
- Add X (Twitter).

## 1.0.2 - 2023-08-31

### Fixed
- Fix an error when trying to use OAuth for some providers.

## 1.0.1 - 2023-05-27

### Fixed
- Fix Redirect URI not working correctly for multi-sites.

## 1.0.0 - 2023-02-01

### Added
- Initial release
