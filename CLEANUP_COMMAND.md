# Project Cleanup Command

A comprehensive Artisan command to clean up projects by removing files, Docker containers, and database records.

## Usage

```bash
php artisan projects:cleanup [options]
```

## Options

| Option | Description |
|--------|-------------|
| `--all` | Clean up all projects (NO confirmation required) + reset auto-increment counters |
| `--project=ID` | Clean up specific project by ID |
| `--force` | Skip confirmation prompts (for specific projects) |
| `--docker-only` | Only clean Docker resources |
| `--files-only` | Only clean project files |
| `--database-only` | Only clean database records |
| `--dry-run` | Show what would be cleaned without actually doing it |

## Examples

### Clean all projects (NO confirmation required + reset auto-increment)
```bash
php artisan projects:cleanup --all
```

### Clean specific project
```bash
php artisan projects:cleanup --project=1
```

### Clean only Docker resources
```bash
php artisan projects:cleanup --all --docker-only
```

### Clean only project files
```bash
php artisan projects:cleanup --all --files-only
```

### Clean only database records
```bash
php artisan projects:cleanup --all --database-only
```

### Dry run (see what would be cleaned)
```bash
php artisan projects:cleanup --all --dry-run
```

### Force cleanup without confirmation
```bash
php artisan projects:cleanup --all --force
```

## What Gets Cleaned

### Docker Resources
- Stops and removes Docker containers
- Removes Docker images
- Cleans up container-related database records

### Project Files
- Removes entire project directory from `storage/app/projects/{id}`
- Deletes all generated files (source code, configs, etc.)

### Database Records
- Removes project record
- Removes related containers
- Removes related prompts
- Maintains referential integrity

### Auto-Increment Reset (--all only)
- Resets auto-increment counters to 1
- Next project will start with ID 1
- Next container will start with ID 1
- Fresh start for development

## Safety Features

- **Confirmation prompts** (unless `--force` is used)
- **Dry run mode** to preview changes
- **Detailed reporting** of what was cleaned
- **Error handling** with detailed error messages
- **Validation** of command options

## Use Cases

- **Development cleanup**: Remove test projects and free up space
- **Docker cleanup**: Clean up orphaned containers and images
- **Database cleanup**: Remove old project records
- **Selective cleanup**: Clean only specific resources
- **Testing**: Use dry-run to see what would be affected

## Output

The command provides detailed output including:
- Cleanup plan showing what will be affected
- Progress indicators for each project
- Summary of results (containers, files, records removed)
- Error reporting if any issues occur
- Confirmation of successful completion
