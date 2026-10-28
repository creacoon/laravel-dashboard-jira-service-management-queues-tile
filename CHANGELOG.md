# Changelog

All notable changes to `creacoon/laravel-dashboard-jira-queue-tile` will be documented in this file.

## [Unreleased]

## [2.1.1] 2026-10-28

### Fixed
- Fetching a queue with 50 or more issues never finished, because the same first page was requested over and over; it piled up scheduled runs and showed counts like 151100. Queue counts now come from a single request with `includeCount=true`

## [2.1.0] 2026-09-28

### Changed
- Modernised the tile: compact cards with equal font sizes and a highlighted resolved-today count

### Fixed
- "Resolved today" stayed empty because Jira removed `/rest/api/3/search`; the count now comes from `/rest/api/3/search/approximate-count`
- Queues with more than 50 issues only counted the last page

## [2.0.0] 2026-09-23

### Changed
- Require `livewire/livewire` ^4.0
- Require `spatie/laravel-dashboard` ^4.0, which provides the `x-dashboard-tile` component used by the tile view

### Fixed
- Import `Livewire\Component` with the correct namespace casing

## 1.0.0 - 202X-XX-XX

- initial release
