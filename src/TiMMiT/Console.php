<?php

/**
 * A very simple class for using Console.
 *
 * PHP version 8.0+
 *
 * @package TiMMiT/Console
 * @author  Tim Schoondergang <tim.schoondergang@TiMMiT.nl>
 */

declare(strict_types=1);

namespace TiMMiT;

/**
 * De Console class
 */
class Console
{
    /**
     * @var int[] $FGCOLOR Text foreground colors.
     */
    protected static array $FGCOLOR = [
        'black'  => 30,
        'red'    => 31,
        'green'  => 32,
        'brown'  => 33,
        'blue'   => 34,
        'purple' => 35,
        'cyan'   => 36,
        'grey'   => 37,
        'yellow' => 33,
    ];

    /**
     * @var int[] $STYLE Text styling.
     */
    protected static array $STYLE = [
        'normal'     => 0,
        'bold'       => 1,
        'light'      => 1,
        'underscore' => 4,
        'underline'  => 4,
        'blink'      => 5,
        'inverse'    => 6,
        'hidden'     => 8,
        'concealed'  => 8,
    ];

    /**
     * @var int[] $BGCOLOR Text background color.
     */
    protected static array $BGCOLOR = [
        'black'  => 40,
        'red'    => 41,
        'green'  => 42,
        'brown'  => 43,
        'yellow' => 43,
        'blue'   => 44,
        'purple' => 45,
        'cyan'   => 46,
        'grey'   => 47,
    ];

    /**
     * @var mixed[] $CONVERSIONS Color specifier conversion table. Taken from PEAR's Console_Color.
     */
    protected static array $CONVERSIONS = [
        '%y' => ['yellow', null, null],
        '%g' => ['green', null, null],
        '%b' => ['blue', null, null],
        '%r' => ['red', null, null],
        '%p' => ['purple', null, null],
        '%m' => ['purple', null, null],
        '%c' => ['cyan', null, null],
        '%w' => ['grey', null, null],
        '%k' => ['black', null, null],
        '%n' => ['reset', null, null],
        '%Y' => ['yellow', 'light', null],
        '%G' => ['green', 'light', null],
        '%B' => ['blue', 'light', null],
        '%R' => ['red', 'light', null],
        '%P' => ['purple', 'light', null],
        '%M' => ['purple', 'light', null],
        '%C' => ['cyan', 'light', null],
        '%W' => ['grey', 'light', null],
        '%K' => ['black', 'light', null],
        '%N' => ['reset', 'light', null],
        '%3' => [null, null, 'yellow'],
        '%2' => [null, null, 'green'],
        '%4' => [null, null, 'blue'],
        '%1' => [null, null, 'red'],
        '%5' => [null, null, 'purple'],
        '%6' => [null, null, 'cyan'],
        '%7' => [null, null, 'grey'],
        '%0' => [null, null, 'black'],
        // Don't use this, I can't stand flashing text
        '%F' => [null, 'blink', null],
        '%U' => [null, 'underline', null],
        '%8' => [null, 'inverse', null],
        '%9' => [null, 'bold', null],
        '%_' => [null, 'bold', null],
    ];

    /**
     * Create ANSI-control codes for text foreground and background colors, and
     * styling.
     *
     * @param string|null $fgcolor Text foreground color
     * @param string|null $style   Text style
     * @param string|null $bgcolor Text background color
     *
     * @return string ANSI-control code
     */
    public static function color(?string $fgcolor, ?string $style, ?string $bgcolor): string
    {
        $code = [];
        if ($fgcolor == 'reset') {
            return "\033[0m";
        }
        if (isset(static::$FGCOLOR[$fgcolor])) {
            $code[] = static::$FGCOLOR[$fgcolor];
        }
        if (isset(static::$STYLE[$style])) {
            $code[] = static::$STYLE[$style];
        }
        if (isset(static::$BGCOLOR[$bgcolor])) {
            $code[] = static::$BGCOLOR[$bgcolor];
        }
        if (count($code) === 0) {
            $code[] = 0;
        }
        return "\033[" . implode(';', $code) . 'm';
    }

    /**
     * Taken from PEAR's Console_Color:
     *
     * Converts colorcodes in the format %y (for yellow) into ansi-control
     * codes. The conversion table is: ('bold' meaning 'light' on some
     * terminals). It's almost the same conversion table irssi uses.
     * <pre>
     *                  text      text            background
     *      ------------------------------------------------
     *      %k %K %0    black     dark grey       black
     *      %r %R %1    red       bold red        red
     *      %g %G %2    green     bold green      green
     *      %y %Y %3    yellow    bold yellow     yellow
     *      %b %B %4    blue      bold blue       blue
     *      %m %M %5    magenta   bold magenta    magenta
     *      %p %P       magenta (think: purple)
     *      %c %C %6    cyan      bold cyan       cyan
     *      %w %W %7    white     bold white      white
     *
     *      %F     Blinking, Flashing
     *      %U     Underline
     *      %8     Reverse
     *      %_,%9  Bold
     *
     *      %n     Resets the color
     *      %%     A single %
     * </pre>
     * First param is the string to convert, second is an optional flag if
     * colors should be used. It defaults to true, if set to false, the
     * colorcodes will just be removed (And %% will be transformed into %)
     *
     * @param string $text  String to color
     * @param bool $color color it
     *
     * @return string
     */
    public static function colorize(string $text, bool $color = true): string
    {
        $text = str_replace('%%', '% ', $text);
        foreach (static::$CONVERSIONS as $key => $value) {
            if (is_array($value)) {
                list($fgcolor, $style, $bgcolor) = $value;
            } else {
                $fgcolor = null;
                $style = null;
                $bgcolor = null;
            }

            if (!is_string($fgcolor)) {
                $fgcolor = null;
            }
            if (!is_string($style)) {
                $style = null;
            }
            if (!is_string($bgcolor)) {
                $bgcolor = null;
            }

            $text = str_replace(
                $key,
                $color ? static::color($fgcolor, $style, $bgcolor) : '',
                $text
            );
        }
        return str_replace('% ', '%', $text);
    }

    /**
     * Strips a string from color specifiers.
     *
     * @param string $text String to strip
     *
     * @return string
     */
    public static function decolorize(string $text): string
    {
        return static::colorize($text, false);
    }

    /**
     * Strips a string of ansi-control codes.
     *
     * @param string $text String to strip
     *
     * @return string
     */
    public static function strip(string $text): string
    {
        return preg_replace('/\033\[(\d+)(;\d+)*m/', '', $text) ?? '';
    }

    /**
     * Gets input from STDIN and returns a string right-trimmed for EOLs.
     *
     * @param bool $raw If set to true, returns the raw string without trimming
     *
     * @return string|null
     */
    public static function stdin(bool $raw = false): ?string
    {
        if (defined('DAEMON_STDIN')) {
            $stdin = constant('DAEMON_STDIN');
        } else {
            $stdin = STDIN;
        }
        $input = fgets($stdin);
        if (is_string($input)) {
            if (!$raw) {
                $input = rtrim($input, PHP_EOL);
            }
            return $input;
        }
        return null;
    }

    /**
     * Asks the user for input. Ends when the user types a PHP_EOL. Optionally
     * provide a prompt.
     *
     * @param string|null $prompt String prompt (optional)
     * @param bool   $raw    If set to true, returns the raw string without trimming
     *
     * @return string|null User input
     */
    public static function input(?string $prompt = null, bool $raw = false): ?string
    {
        if (isset($prompt)) {
            static::stdout($prompt);
        }
        return static::stdin($raw);
    }

    /**
     * Prints text to STDOUT.
     *
     * @param string $text String to write to STDOUT
     * @param bool   $raw  Write string as-is; defaults to false
     *
     * @return int|null Number of bytes printed or false on error
     */
    public static function stdout(string $text, bool $raw = false): ?int
    {
        if (defined('DAEMON_STDOUT')) {
            $stdout = constant('DAEMON_STDOUT');
        } else {
            $stdout = STDOUT;
        }
        if (is_resource($stdout)) {
            if ($raw) {
                $return = fwrite($stdout, $text);
            } elseif (extension_loaded('posix') && posix_isatty($stdout)) {
                $return = fwrite($stdout, static::colorize($text));
            } else {
                $return = fwrite($stdout, static::decolorize($text));
            }
            if ($return !== false) {
                return $return;
            }
        }
        return null;
    }

    /**
     * Prints text to STDOUT appended with a PHP_EOL.
     *
     * @param string $text String to write to STDOUT
     * @param bool   $raw  Write string as-is; defaults to false
     *
     * @return int|null Number of bytes printed or false on error
     */
    public static function output(string $text = '', bool $raw = false): ?int
    {
        return static::stdout((string)($text ?? '') . PHP_EOL, $raw);
    }

    /**
     * Prints text to STDERR.
     *
     * @param string $text String to write to STDERR
     * @param bool   $raw  Write string as-is; defaults to false
     *
     * @return int|null Number of bytes printed or false on error
     */
    public static function stderr(string $text, bool $raw = false): ?int
    {
        if (defined('DAEMON_STDERR')) {
            $stderr = constant('DAEMON_STDERR');
        } else {
            $stderr = STDERR;
        }
        if ($raw) {
            $return = fwrite($stderr, $text);
        } elseif (extension_loaded('posix') && posix_isatty($stderr)) {
            $return = fwrite($stderr, static::colorize($text));
        } else {
            $return = fwrite($stderr, static::decolorize($text));
        }
        if ($return !== false) {
            return $return;
        }
        return null;
    }

    /**
     * Prints text to STDERR appended with a PHP_EOL.
     *
     * @param string|null $text String to write to STDERR
     * @param bool   $raw  Write string as-is; defaults to false
     *
     * @return int|null Number of bytes printed or false on error
     */
    public static function error(?string $text = null, bool $raw = false): ?int
    {
        return static::stderr($text . PHP_EOL, $raw);
    }

    /**
     * Prompts the user for input
     *
     * @param string $text    Prompt string
     * @param mixed[]  $options Set of options
     *
     * @return string
     */
    public static function prompt(string $text, array $options = []): string
    {
        $options = $options + [
            'required'  => false,
            'default'   => null,
            'pattern'   => null,
            'validator' => null,
            'error'     => 'Input unacceptable.',
        ];

        top:
        if ((bool)$options['default']) {
            $input = static::input("$text [" . $options['default'] . ']: ');
        } else {
            $input = static::input("$text: ");
        }

        if (is_null($input)) {
            $input = '';
        }

        /** @var string|false */
        $error = false;
        if (strlen($input) > 0) {
            if (is_string($options['default'])) {
                $input = $options['default'];
            } elseif ($options['required']) {
                static::output($options['error']);
                goto top;
            }
        } elseif (is_string($options['pattern']) && false === preg_match($options['pattern'], $input)) {
            static::output($options['error']);
            goto top;
        } elseif (is_callable($options['validator']) && false === call_user_func_array($options['validator'], [$input, &$error])) {
            if (is_string($error)) {
                static::output($error);
            } else {
                static::output($options['error']);
            }
            goto top;
        }

        return $input;
    }

    /**
     * Asks the user for a simple yes/no confirmation.
     *
     * @param string $text Prompt string
     *
     * @return bool Either true or false
     */
    public static function confirm(string $text): bool
    {
        top:
        $input = strtolower(static::input("$text [y/n]: ") ?? '');
        if (!in_array($input, ['y', 'n'], true)) {
            goto top;
        }
        if ($input === 'y') {
            return true;
        }
        return false;
    }

    /**
     * Gives the user an option to choose from. Giving '?' as an input will show
     * a list of options to choose from and their explanations.
     *
     * @param string $text    Prompt string
     * @param string[]  $options Key-value array of options to choose from
     *
     * @return string An option character the user chose
     */
    public static function select(string $text, array $options = []): string
    {
        top:
        static::stdout("$text [" . implode(',', array_keys($options)) . ",?]: ");
        $input = static::stdin() ?? '';
        if ($input === '?') {
            foreach ($options as $key => $value) {
                echo " $key - $value\n";
            }
            echo " ? - Show help\n";
            goto top;
        } elseif (!in_array($input, array_keys($options), true)) {
            goto top;
        }
        return $input;
    }
}
