<?php

namespace Valet;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Define constants.
 */
if (!defined('VALET_HOME_PATH')) {
    if (testing()) {
        define('VALET_HOME_PATH', __DIR__.'/../../tests/config/valet');
        define('OLD_VALET_HOME_PATH', __DIR__.'/../../tests/old-config/valet');

    } else {
        define('VALET_HOME_PATH', $_SERVER['HOME'].'/.config/valet');
        define('OLD_VALET_HOME_PATH', $_SERVER['HOME'].'/.valet');
    }
}

if (!defined('VALET_STATIC_PREFIX')) {
    define('VALET_STATIC_PREFIX', '41c270e4-5535-4daa-b23e-c269744c2f45');
}
define('VALET_LOOPBACK', '127.0.0.1');
define('VALET_ROOT_PATH', realpath(__DIR__.'/../../'));
define('VALET_SERVER_PATH', realpath(__DIR__.'/../../server.php'));
define('ISOLATED_PHP_VERSION', 'ISOLATED_PHP_VERSION');

/**
 * Return whether the app is in the testing environment.
 */
function testing(): bool
{
    return strpos($_SERVER['SCRIPT_NAME'], 'phpunit') !== false;
}

if (!function_exists('resolve')) {
    /**
     * Resolve the given class from the container.
     * @return mixed
     * @throws BindingResolutionException
     */
    function resolve(string $class)
    {
        return Container::getInstance()->make($class);
    }
}

/**
 * Swap the given class implementation in the container.
 * @param mixed  $instance
 */
function swap(string $class, $instance): void
{
    Container::getInstance()->instance($class, $instance);
}

if (!function_exists('retry')) {
    /**
     * Retry the given function N times.
     * @throws Exception
     * @return mixed
     */
    function retry(int $retries, callable $fn, int $sleep = 0)
    {
        beginning:
        try {
            return $fn();
        } catch (Exception $e) {
            if (!$retries) {
                throw $e;
            }

            $retries--;

            if ($sleep > 0) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }
}

if (!function_exists('tap')) {
    /**
     * Tap the given value.
     * @param mixed    $value
     * @return mixed
     */
    function tap($value, callable $callback)
    {
        $callback($value);

        return $value;
    }
}

/**
 * Get the user.
 */
function user(): string
{
    if (!isset($_SERVER['SUDO_USER'])) {
        return $_SERVER['USER'];
    }

    return $_SERVER['SUDO_USER'];
}

/**
 * Get the user's group.
 * @return string|false
 */
function group()
{
    if (!isset($_SERVER['SUDO_USER'])) {
        return exec('id -gn '.$_SERVER['USER']);
    }

    return exec('id -gn '.$_SERVER['SUDO_USER']);
}

/**
 * Search and replace using associative array.
 */
function strArrayReplace(array $searchAndReplace, string $subject): string
{
    return str_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $subject);
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars)
    {
        // Set content type for better HTML rendering
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<style>
            .dd-container {
                font-family: "SF Mono", Monaco, Inconsolata, "Roboto Mono", Consolas, "Courier New", monospace;
                background: #1e1e1e;
                color: #d4d4d4;
                padding: 20px;
                margin: 10px 0;
                border-radius: 8px;
                border-left: 4px solid #007acc;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                overflow-x: auto;
                line-height: 1.4;
            }
            .dd-header {
                color: #569cd6;
                font-weight: bold;
                margin-bottom: 10px;
                border-bottom: 1px solid #333;
                padding-bottom: 5px;
            }
            .dd-type {
                color: #4ec9b0;
                font-weight: bold;
            }
            .dd-string {
                color: #ce9178;
            }
            .dd-number {
                color: #b5cea8;
            }
            .dd-bool {
                color: #569cd6;
            }
            .dd-null {
                color: #808080;
                font-style: italic;
            }
            .dd-key {
                color: #9cdcfe;
            }
            .dd-trace {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #333;
                font-size: 0.9em;
                color: #808080;
            }
        </style>';

        // Get backtrace info
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file = isset($trace[0]['file']) ? $trace[0]['file'] : 'unknown';
        $line = isset($trace[0]['line']) ? $trace[0]['line'] : 'unknown';

        echo '<div class="dd-container">';
        echo '<div class="dd-header">🔍 DD Debug Output</div>';

        // If no arguments provided, just show the call location
        if (empty($vars)) {
            echo '<div class="dd-null">No variables to dump</div>';
        } else {
            foreach ($vars as $index => $var) {
                if (count($vars) > 1) {
                    echo '<div class="dd-header">Variable #'.($index + 1).':</div>';
                }
                echo formatVariable($var);
                if ($index < count($vars) - 1) {
                    echo '<hr style="border: 1px solid #333; margin: 15px 0;">';
                }
            }
        }

        echo '<div class="dd-trace">📍 Called from: '.basename($file).':'.$line.'</div>';
        echo '</div>';

        // End the script
        die(1);
    }
}

if (!function_exists('formatVariable')) {
    /**
     * Format a variable for display
     *
     * @param mixed $var
     * @param int $depth
     * @return string
     */
    function formatVariable($var, $depth = 0)
    {
        $indent = str_repeat('  ', $depth);
        $output = '';

        if (is_null($var)) {
            $output .= '<span class="dd-null">null</span>';
        } elseif (is_bool($var)) {
            $output .= '<span class="dd-type">bool</span> <span class="dd-bool">'.($var ? 'true' : 'false').'</span>';
        } elseif (is_int($var)) {
            $output .= '<span class="dd-type">int</span> <span class="dd-number">'.$var.'</span>';
        } elseif (is_float($var)) {
            $output .= '<span class="dd-type">float</span> <span class="dd-number">'.$var.'</span>';
        } elseif (is_string($var)) {
            $length = strlen($var);
            $preview = $length > 100 ? substr($var, 0, 100).'...' : $var;
            $output .= '<span class="dd-type">string</span>('.$length.') <span class="dd-string">"'.htmlspecialchars($preview).'"</span>';
        } elseif (is_array($var)) {
            $count = count($var);
            $output .= '<span class="dd-type">array</span>('.$count.')';

            if ($count > 0 && $depth < 5) { // Prevent infinite recursion
                $output .= ' [<br>';
                foreach ($var as $key => $value) {
                    $output .= $indent.'  <span class="dd-key">'.(is_string($key) ? '"'.htmlspecialchars($key).'"' : $key).'</span> => ';
                    $output .= formatVariable($value, $depth + 1).'<br>';
                }
                $output .= $indent.']';
            } elseif ($count > 0) {
                $output .= ' [...]'; // Too deep
            } else {
                $output .= ' []';
            }
        } elseif (is_object($var)) {
            $className = get_class($var);
            $output .= '<span class="dd-type">object</span>('.$className.')';

            if ($depth < 3) { // Prevent infinite recursion
                $reflection = new ReflectionClass($var);
                $properties = $reflection->getProperties();

                if (!empty($properties)) {
                    $output .= ' {<br>';
                    foreach ($properties as $property) {
                        $property->setAccessible(true);
                        $name = $property->getName();
                        $visibility = $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private');

                        try {
                            $value = $property->getValue($var);
                            $output .= $indent.'  <span style="color: #ce9178;">'.$visibility.'</span> <span class="dd-key">$'.$name.'</span> => ';
                            $output .= formatVariable($value, $depth + 1).'<br>';
                        } catch (Exception $e) {
                            $output .= $indent.'  <span style="color: #ce9178;">'.$visibility.'</span> <span class="dd-key">$'.$name.'</span> => <span class="dd-null">uninitialized</span><br>';
                        }
                    }
                    $output .= $indent.'}';
                } else {
                    $output .= ' {}';
                }
            } else {
                $output .= ' {...}'; // Too deep
            }
        } elseif (is_resource($var)) {
            $output .= '<span class="dd-type">resource</span>('.get_resource_type($var).')';
        } else {
            $output .= '<span class="dd-type">'.gettype($var).'</span> '.htmlspecialchars(print_r($var, true));
        }

        return $output;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump the passed variables without ending the script.
     *
     * @param mixed ...$vars
     * @return void
     */
    function dump(...$vars)
    {
        // Set content type for better HTML rendering
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<style>
            .dump-container {
                font-family: "SF Mono", Monaco, Inconsolata, "Roboto Mono", Consolas, "Courier New", monospace;
                background: #2d2d30;
                color: #d4d4d4;
                padding: 15px;
                margin: 10px 0;
                border-radius: 6px;
                border-left: 4px solid #f39c12;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                overflow-x: auto;
                line-height: 1.4;
            }
            .dump-header {
                color: #f39c12;
                font-weight: bold;
                margin-bottom: 8px;
                font-size: 0.9em;
            }
        </style>';

        // Get backtrace info
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file = isset($trace[0]['file']) ? $trace[0]['file'] : 'unknown';
        $line = isset($trace[0]['line']) ? $trace[0]['line'] : 'unknown';

        echo '<div class="dump-container">';
        echo '<div class="dump-header">🔧 DUMP from '.basename($file).':'.$line.'</div>';

        if (empty($vars)) {
            echo '<div class="dd-null">No variables to dump</div>';
        } else {
            foreach ($vars as $index => $var) {
                if (count($vars) > 1) {
                    echo '<div style="color: #f39c12; margin-bottom: 5px;">Variable #'.($index + 1).':</div>';
                }
                echo formatVariable($var);
                if ($index < count($vars) - 1) {
                    echo '<hr style="border: 1px solid #444; margin: 10px 0;">';
                }
            }
        }

        echo '</div>';
    }
}
