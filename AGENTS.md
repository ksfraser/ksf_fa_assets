# AGENTS.md - ksf_FA_Assets#

## Architecture Overview#

**FA Module** for Asset Management with lifecycle tracking, depreciation, and GL integration.

### Core Principles#
- **SOLID**: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion#
- **DRY**: Don't Repeat Yourself#
- **TDD**: Test-Driven Development#
- **DI**: Dependency Injection#
- **SRP**: Single Responsibility Principle#

## Repository Structure#

```
ksf_FA_Assets/
├── sql/                    # Database schemas (FA TB_PREF tables)#
│   ├── fa_asset_categories.sql#
│   ├── fa_assets.sql#
│   ├── fa_asset_depreciation.sql#
│   ├── fa_asset_maintenance.sql#
│   └── fa_asset_transfers.sql#
├── includes/              # FA-specific DB classes#
│   ├── asset_categories_db.inc#
│   ├── assets_db.inc#
│   ├── depreciation_db.inc#
│   ├── maintenance_db.inc#
│   └── transfers_db.inc#
├── pages/                 # UI pages (FA admin)#
├── hooks.php              # FA module hooks#
├── composer.json#
└── ProjectDocs/           # Project documentation#
    ├── Requirements.md#
    ├── RTM.md#
    ├── BABOK.md#
    └── UML.md#
```

## Coding Standards#

### PHP Compatibility#
- **Target**: PHP 7.3+ (with eye to PHP 8.x upgrades)#
- Use `declare(strict_types=1);` at top of all PHP files#

### Naming Conventions#
- **FA DB files**: `{table_name}_db.inc`#
- **Functions**: `write_{table}()`, `get_{table}()`, `delete_{table}()`#
- **Hooks**: `hooks.php` (FA convention)#

## Testing Strategy#

### TDD Red-Green-Refactor#
1. **RED**: Write failing test#
2. **GREEN**: Write minimal code to pass#
3. **REFACTOR**: Improve code while keeping tests green#

## Design Patterns Used#

### Table Gateway Pattern#
- Each `fa_assets_*` table has corresponding `_db.inc` file#
- CRUD operations: `write_`, `get_`, `delete_`#

### Hook Pattern (FA Native)#
- Uses FA's `update_databases()` for multi-SQL file handling#
- `activate_extension()` processes SQL files in order#

## Version Tagging#

Follow Semantic Versioning (SemVer): `MAJOR.MINOR.PATCH`#

```bash#
git tag -a v1.0.0 -m "Initial Assets module with depreciation"#
git push origin v1.0.0#
```

## Composer/Packagist#

```json#
{#
    "name": "ksfraser/ksf_fa_assets",#
    "description": "Assets Management for FrontAccounting",#
    "type": "frontaccounting-module",#
    "require": {#
        "php": ">=7.3",#
        "ksfraser/ksf_fa_assets_core": "*"#
    }#
}#
```

## RTM (Requirements Traceability Matrix)#

| Req ID | Description | Test Case | Code File | Version |#
|--------|-------------|-----------|----------|---------|#
| REQ-001 | Asset Categories | testCategoryCreate | sql/fa_asset_categories.sql | v1.0.0 |#
| REQ-002 | Asset Tracking | testAssetLifecycle | includes/assets_db.inc | v1.0.0 |#
| REQ-003 | Depreciation Calc | testDepreciationCalc | includes/depreciation_db.inc | v1.1.0 |#

## BABOK Alignment#

### Business Requirements (BABOK)#
- **BR-001**: Asset Management - Track company assets#
- **BR-002**: Depreciation - Automatic depreciation calculations#
- **BR-003**: Maintenance - Schedule and track maintenance#

## Dependencies#

- **ksf_FA_Assets_Core** (business logic - framework-agnostic)#
- **FrontAccounting 2.4+** (FA core)#

## Development Workflow

All development is done in the **devel tree** (`~/Documents/ksf_FA_Assets`). Do **not** edit files in the UAT bind point directly.

### Workflow Steps
1. **Develop** in this repo (feature branches preferred)
2. **Test**: run repo-appropriate tests
3. **Lint**: `php -l` on modified PHP files (no syntax errors)
4. **Commit** and **Push** branch to GitHub
5. **Merge** to `master` when ready
6. **Push** `master` to GitHub
7. **Deploy** to UAT by pulling in the Infrastructure bind point:

   ```
   cd ~/ksf_Infrastructure/fa_modules/ksf_FA_Assets
   git stash -u
   git pull origin master
   git stash pop
   ```

### UAT Bind Point
| Path | Purpose |
|------|---------|
| `~/Documents/ksf_FA_Assets` | Devel tree — all development, testing, commits |
| `~/ksf_Infrastructure/fa_modules/ksf_FA_Assets` | UAT bind point — deployment target, integration testing (if mirrored) |

