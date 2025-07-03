# Command Execution Feature

This feature allows authenticated users to execute terminal commands directly from the browser by accessing specific URLs.

## Features

- Execute terminal commands via URL (e.g., `https://chambersapi.logicera.in/run/php artisan optimize`)
- Authentication required to execute commands
- Whitelist of allowed commands for security
- Beautiful output display with syntax highlighting
- JSON API endpoint for programmatic access
- Web view for human-readable output

## Usage

### Web Interface

Access the command execution interface by visiting:

```
https://chambersapi.logicera.in/run/{command}
```

Replace `{command}` with the command you want to execute, for example:

- `https://chambersapi.logicera.in/run/php artisan optimize`
- `https://chambersapi.logicera.in/run/git pull origin main`
- `https://chambersapi.logicera.in/run/composer update`

### API Endpoint

For programmatic access, use the API endpoint:

```
https://chambersapi.logicera.in/api/run/{command}
```

This will return a JSON response with the command output.

## Security

- Only authenticated users can execute commands
- Commands are restricted to a whitelist of allowed prefixes:
  - `php artisan`
  - `git`
  - `composer`
  - `npm`
  - `ls`
  - `cat`
  - `echo`
  - `tail`
  - `grep`

## Implementation Details

The feature is implemented using:

1. A `CommandController` with two methods:
   - `executeCommand` - Returns JSON response for API usage
   - `executeCommandView` - Returns HTML view for web interface

2. Two Blade templates:
   - `command.result` - Displays successful command execution
   - `command.error` - Displays error messages

3. Routes in `web.php` protected by the `auth:sanctum` middleware

## Examples

### Artisan Commands

- `https://chambersapi.logicera.in/run/php artisan optimize`
- `https://chambersapi.logicera.in/run/php artisan migrate`
- `https://chambersapi.logicera.in/run/php artisan cache:clear`

### Git Commands

- `https://chambersapi.logicera.in/run/git pull origin main`
- `https://chambersapi.logicera.in/run/git status`
- `https://chambersapi.logicera.in/run/git stash`

### Other Commands

- `https://chambersapi.logicera.in/run/composer update`
- `https://chambersapi.logicera.in/run/npm run dev`
- `https://chambersapi.logicera.in/run/ls -la`