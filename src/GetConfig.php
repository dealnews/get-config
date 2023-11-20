<?php

namespace DealNews\GetConfig;

use Sarhan\Flatten\Flatten;

/**
 * Gets configuration settings
 *
 * Settings are loaded from environment variables first or ini files second.
 * The default location for the main ini file is `[app directory]/etc/config.ini`.
 * Multiple ini files can be loaded from a directory in addition to or instead
 * of the main ini file. The default directory for loading multiple ini files
 * is `[app directory]/etc/config.d/`. An environment can be used to override a
 * single configuration settting, the default ini file location, and the
 * default ini configuration directory.
 *
 * ## Preference Order
 *
 * When calling the `get()` method, values are searched for in the following
 * order:
 *
 * 1. Environment Variables - If getting the value of foo.bar, the code will
 *    look for an environment variable named `foo.bar`, `FOO.BAR`,
 *    and `FOO_BAR`.
 * 2. Values in ini files. The last value set will be the value returned. For
 *    example, if `foo.bar` is set to 100 in `[app directory]/etc/config.ini` and
 *    `foo.bar` is set to 200 in `[app directory]/etc/config.d/01-other.ini`, the
 *    `get()` method will return 200 as the files in the config dir are loaded
 *    after the main ini file. Likewise, if there was a file
 *    `[app directory]/etc/config.d/02-another.ini` with `foo.bar` set to 300, the
 *    `get()` method will return 300 as the additional ini files are loaded
 *    in alphabetical order with the values from last file parsed being used.
 *
 * ## Environment Variables
 *
 *     DN_INI_FILE - Overrides the default ini file location
 *     DN_ETC_DIR  - Overrides the directory where config.ini is located
 *     DN_INI_DIR  - Overrides the default location where mulitple ini
 *                   files are located
 *
 * ## Example Usage
 *
 *     $config = \DealNews\GetConfig\GetConfig::init();
 *     $value1 = $config->get("dealnews.some.var1");
 *     $value2 = $config->get("dealnews.some.var2");
 *     $value3 = $config->get("dealnews.some.var3");
 *
 *     OR
 *
 *     $value = \DealNews\GetConfig\GetConfig::init()
 *         ->get("dealnews.some.var");
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     GetConfig
 *
 * @phan-suppress PhanUnreferencedClass
 */
class GetConfig {

    /**
     * Set default dir to what would be a normal base directory
     * relative to this file being installed via composer.
     *
     * @var        string
     */
    const DEFAULT_ETC_DIR = __DIR__ . '/../../../../etc';

    /**
     * Default ini file
     */
    const DEFAULT_INI_FILE = self::DEFAULT_ETC_DIR . '/config.ini';

    /**
     * Default ini directory where multiple ini files can be found
     */
    const DEFAULT_INI_DIR = self::DEFAULT_ETC_DIR . '/config.d';

    /**
     * array of ini file names
     * @var array
     */
    protected array $ini_files = [];

    /**
     * ini array
     * @var array
     */
    protected array $ini_options = [];

    /**
     * Holds config values found in config files
     *
     * @var        array
     */
    protected array $config = [];

    /**
     * Location of main config file and config directory
     *
     * @var string
     */
    protected string $etc_dir = self::DEFAULT_ETC_DIR;

    /**
     * Location of additional config files
     *
     * @var string
     */
    protected string $ini_dir = self::DEFAULT_INI_DIR;

    /**
     * Singleton
     *
     * @return self
     */
    public static function init() {
        static $inst;

        if (empty($inst)) {
            $class = get_called_class();
            $inst  = new $class();
        }

        return $inst;
    }

    /**
     * Creates the object
     *
     * @param ?string $file     config file name
     * @param ?string $ini_dir  dir were multiple config files can be found
     */
    public function __construct(?string $file = null, ?string $ini_dir = null) {
        if ($file !== null) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("File not found $file");
            }
            $this->etc_dir = dirname($file);
        } else {
            // Check for an environment varible
            // that has the etc directory
            $file = getenv('DN_INI_FILE');
            if (empty($file)) {
                $env_etc_dir = getenv('DN_ETC_DIR');
                if (!empty($env_etc_dir)) {
                    $this->etc_dir = $env_etc_dir;
                    $file          = $this->etc_dir . '/' . basename(self::DEFAULT_INI_FILE);
                }
            }
        }

        if (empty($file)) {
            if (file_exists(self::DEFAULT_INI_FILE)) {
                $this->ini_files = [self::DEFAULT_INI_FILE];
            }
        } else {
            if (file_exists($file)) {
                $this->ini_files = [$file];
            } else {
                throw new \InvalidArgumentException("File not found $file");
            }
        }

        if ($ini_dir === null) {
            $ini_dir = getenv('DN_INI_DIR');

            if (empty($ini_dir)) {
                $ini_dir = $this::DEFAULT_INI_DIR;
            }
        }

        if (!empty($ini_dir)) {
            $this->ini_dir = rtrim($ini_dir, '/');
            $files         = array_merge(
                glob("{$this->ini_dir}/*.ini"),
                glob("{$this->ini_dir}/*.env"),
                glob("{$this->ini_dir}/*.yaml"),
                glob("{$this->ini_dir}/*.yml"),
                glob("{$this->ini_dir}/*.json"),
            );
            if (!empty($files)) {
                sort($files);
                $this->ini_files = array_merge(
                    $this->ini_files,
                    $files
                );
            }
        }
    }

    /**
     * Gets a value from the ini file
     * @param  string $var Config option name
     * @return ?string     Value or null
     */
    public function get(string $var): ?string {
        if (!array_key_exists($var, $this->config)) {
            $value = null;

            $check_vars = [
                str_replace('.', '_', strtoupper($var)),
                str_replace('.', '_', strtolower($var)),
                $var,
                strtoupper($var),
                strtolower($var),
            ];

            // Check environment variables first.
            foreach ($check_vars as $check_var) {
                $env = getenv($check_var);
                if ($env !== false) {
                    $value = $env;
                    break;
                }
            }

            if ($value === null) {
                if (!empty($this->ini_files)) {
                    // if the option does not exist, and there
                    // are files to parse, parse the files and
                    // check again.
                    do {
                        $ini_file = array_shift($this->ini_files);

                        $this->ini_options = array_merge(
                            $this->ini_options,
                            $this->parseFile($ini_file)
                        );
                    } while (!empty($this->ini_files));
                }

                // Check for alternate forms of the desired configuration name
                foreach ($check_vars as $check_var) {
                    if (array_key_exists($check_var, $this->ini_options)) {
                        $value = $this->ini_options[$check_var];
                        break;
                    }
                }
            }

            foreach ($check_vars as $check_var) {
                $this->config[$check_var] = $value;
            }
        }

        return $this->config[$var];
    }

    /**
     * Finds a file in the configuration directories
     *
     * @param      string       $filename  The filename
     *
     * @return     null|string  Returns the found filename or null
     */
    public function findFile(string $filename): ?string {
        $found_file = null;

        if (file_exists($this->etc_dir . '/' . $filename)) {
            $found_file = $this->etc_dir . '/' . $filename;
        } elseif (file_exists($this->ini_dir . '/' . $filename)) {
            $found_file = $this->ini_dir . '/' . $filename;
        }

        return $found_file;
    }

    /**
     * Parses a config file
     *
     * @param      string             $file   The file
     *
     * @throws     \RuntimeException
     *
     * @return     array
     */
    protected function parseFile(string $file): array {

        static $flatten;

        $flatten = new Flatten();

        $values = null;

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        // Some shared filesystems can cause a file to exist
        // and not be read or complete at the same time somehow.
        // So, we try a few times, clearing the stat cache after
        // each failure.
        $tries = 0;
        do {
            switch ($ext) {
                case 'yaml':
                case 'yml':
                    $values = yaml_parse_file($file);
                    if (!is_array($values) || empty($values)) {
                        $values = null;
                    } else {
                        $values = $flatten->flattenToArray($values);
                    }
                    break;
                case 'json':
                    $values = json_decode(file_get_contents($file), true);
                    if (!is_array($values) || empty($values)) {
                        $values = null;
                    } else {
                        $values = $flatten->flattenToArray($values);
                    }
                    break;
                case 'ini':
                case 'env': // env files should parse as ini files
                    $values = @parse_ini_file($file);
                    break;
                default:
                    throw new \RuntimeException('File ' . basename($file) . ' is not a valid configuration file type.');
            }

            if (!is_array($values)) {
                clearstatcache();
            }

            $tries++;
        } while (!is_array($values) && $tries < 3);

        if (!is_array($values)) {
            throw new \RuntimeException('File ' . basename($file) . " is not a valid $ext configuration file. Attempted to load it $tries times.");
        }

        return $values;
    }
}
