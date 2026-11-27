# timmit/console

Helpers for interactive command-line input and output. This package is a fork of [clio](https://github.com/nramenta/clio), reduced to the console. Process control lives in [timmit/daemon](https://github.com/timmit-nl/daemon).

## Install

```bash
composer require timmit/console
```

Requires PHP >= 8.0, `ext-pcntl`, and `ext-posix`. `posix_isatty()` decides whether color codes are kept when writing to a terminal.

When `DAEMON_STDIN`, `DAEMON_STDOUT`, or `DAEMON_STDERR` is defined, input and output use that stream instead of `STDIN`, `STDOUT`, or `STDERR`. `timmit/daemon` defines those constants in the child process.

## Output

`stdout()` prints a string. `output()` appends `PHP_EOL`. `stderr()` and `error()` do the same for the error stream. `error()` may be called without text.

Color codes in the text are translated on a TTY and stripped when the stream is not a TTY. Pass `true` as the second argument to write the text unchanged.

```php
use TiMMiT\Console;

Console::stdout('Hello, %gWorld%n!');
Console::output('done');
```

| Code | Text | Bold text | Background |
| --- | --- | --- | --- |
| `%k` `%K` `%0` | black | dark grey | black |
| `%r` `%R` `%1` | red | bold red | red |
| `%g` `%G` `%2` | green | bold green | green |
| `%y` `%Y` `%3` | yellow | bold yellow | yellow |
| `%b` `%B` `%4` | blue | bold blue | blue |
| `%m` `%M` `%5` | magenta | bold magenta | magenta |
| `%p` `%P` | magenta (purple) | | |
| `%c` `%C` `%6` | cyan | bold cyan | cyan |
| `%w` `%W` `%7` | white | bold white | white |
| `%F` | blinking | | |
| `%U` | underline | | |
| `%8` | reverse | | |
| `%_` `%9` | bold | | |
| `%n` | reset | | |
| `%%` | a single `%` | | |

`colorize()` applies that table. `decolorize()` and `strip()` remove the codes. `color()` builds one ANSI code from a foreground, style, and background. A `null` color or style is skipped. The foreground `reset` returns the reset code.

## Input

`stdin()` reads one line and trims `PHP_EOL` unless `$raw` is `true`. It returns `null` when no string was read. `input()` prints an optional prompt and then calls `stdin()`.

## Prompts

`prompt()` keeps asking until the input matches the options. `required` demands a non-empty value. `default` is used when the input is empty, and then `required` is not enforced. `pattern` is a regular expression. `validator` is a callable that returns `true` or `false`. `error` is the message printed when the check fails.

A validator can set its own message through the second parameter. That parameter has to be a reference.

```php
use TiMMiT\Console;

$host = Console::prompt('database host', ['default' => 'localhost']);

$file = Console::prompt('File', [
    'required' => true,
    'validator' => function (string $input, ?string &$error = null): bool {
        if (is_readable($input)) {
            return true;
        }
        $error = 'Path given is not a readable file';
        return false;
    },
]);
```

`confirm()` accepts `y`, `n`, `Y`, and `N`, and returns a bool.

```php
$sure = Console::confirm('are you sure?');
```

`select()` asks for one key. `?` is added automatically and prints the explanations.

```php
$opt = Console::select('apply this patch?', [
    'y' => 'yes',
    'n' => 'no',
    'a' => 'all',
]);
```

## Result line

`outputResultTable()` prints one padded line. The status `OK`, `FAILED`, or `SKIP` selects green, red, or yellow.

```php
use TiMMiT\Console;

Console::outputResultTable('OK', 'sync', 'items', '12 written');
```

The widths default to 8, 20, 25, and 15. Override them with `lengthStatus`, `lengthLabel`, `lengthSecondaryLabel`, and `lengthResultLabel`. Override the status letters with `statusColorOK`, `statusColorFailed`, and `statusColorSkip`.

## License

MIT.

## Credits

- Nofriandi Ramenta, author of the original [clio](https://github.com/nramenta/clio) source
- Stefan Walk, author of PEAR Console_Color, the source of the color table
- Tim Schoondergang
