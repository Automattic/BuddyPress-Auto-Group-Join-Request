# BuddyPress Auto Group Join Request

Automatically processes BuddyPress group join requests.

## Project Knowledge

| Property | Value |
|----------|-------|
| **Main file** | `buddypress-auto-group-join-request.php` |
| **Text domain** | `buddypress-auto-group-join-request` |
| **Namespace** | `Automattic\BuddyPressAutoGroupJoinRequest` (tests only) |
| **Source directory** | Root level (single-file plugin) |
| **Version** | 1.0.0 |
| **Requires PHP** | 8.2+ |

### Directory Structure

```
buddypress-auto-group-join-request/
├── tests/
│   ├── Unit/               # Unit tests
│   └── Integration/        # Integration tests (wp-env)
├── languages/              # Translation files
├── .github/workflows/      # CI: cs-lint, integration
└── .phpcs.xml.dist         # PHPCS configuration
```

### Dependencies

- **Runtime**: BuddyPress (required — plugin does nothing without it)
- **Dev**: `automattic/vipwpcs`, `yoast/wp-test-utils`

## Commands

```bash
composer cs                # Check code standards (PHPCS)
composer cs-fix            # Auto-fix code standard violations
composer lint              # PHP syntax lint
composer test:unit         # Run unit tests
composer test:integration  # Run integration tests (requires wp-env)
composer test:integration-ms  # Run multisite integration tests
composer coverage          # Run tests with HTML coverage report
```

## Conventions

Follow the standards documented in `~/code/plugin-standards/` for full details. Key points:

- **Commits**: Use the `/commit` skill. Favour explaining "why" over "what".
- **PRs**: Use the `/pr` skill. Squash and merge by default.
- **Branch naming**: `feature/description`, `fix/description` from `develop`.
- **Testing**: Write integration tests for WordPress-dependent behaviour, unit tests for isolated logic. Use `Yoast\WPTestUtils\WPIntegration\TestCase` for integration, `Yoast\WPTestUtils\BrainMonkey\YoastTestCase` for unit.
- **Code style**: WordPress coding standards via PHPCS. Tabs for indentation.
- **i18n**: All user-facing strings must use the `buddypress-auto-group-join-request` text domain.

## Architectural Decisions

- **Single-file plugin**: All plugin logic lives in the main PHP file at the root. This is intentional given the plugin's small scope — do not split into multiple classes unless complexity genuinely warrants it.
- **BuddyPress dependency**: The plugin hooks into BuddyPress group join workflows. It assumes BuddyPress is active and does not provide fallback behaviour without it.
- **Tier 3 plugin**: Currently needs modernisation work (see PLUGIN_AUDIT.md). Improvements are welcome but should be incremental.

## Common Pitfalls

- Do not edit WordPress core files or BuddyPress core files.
- This plugin depends on BuddyPress — test environments must have BuddyPress installed and active.
- Run `composer cs` before committing. CI will reject code standard violations.
- Integration tests require `npx wp-env start` running first.
- Do not add complex class hierarchies to a single-file plugin. If the plugin grows significantly, propose a restructure rather than bolting on classes.
