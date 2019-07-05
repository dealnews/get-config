<?php

namespace DealNews\GetConfig;

/**
 * Gets configuration settings from dealnews.ini
 *
 * Example:
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
 */

class GetConfig {

    /**
     * ini file name
     * @var string
     */
    private $ini_file;

    /**
     * ini array
     * @var array
     */
    private $ini_options = [];

    /**
     * Creates the object
     * @param string $file ini file name
     */
    public function __construct(string $file = null) {
        if ($file === null) {
            // Check for an environment varible
            // that has the etc directory
            $file = getenv("DN_INI_FILE");
        }

        if (!empty($file)) {
            if (is_readable($file)) {
                $this->ini_file = $file;
            } else {
                throw new \InvalidArgumentException("File not found or is not readable: $file");
            }
        }
    }

    /**
     * Gets a value from the environment or ini file
     *
     * @param  string $var Config option name
     * @return mixed       Value or null
     */
    public function get(string $var) {

        // always check env vars first as once we read in
        // an ini file, some values will be set.
        $check_vars = [
            $var,
            strtoupper($var),
            str_replace(".", "_", strtoupper($var))
        ];
        foreach ($check_vars as $check_var) {
            $val = getenv($check_var);
            if ($val !== false) {
                $this->ini_options[$var] = $val;
                break;
            }
        }

        if (!array_key_exists($var, $this->ini_options)) {

            if (!empty($this->ini_file)) {
                $this->ini_options = parse_ini_file($this->ini_file);
                if (!is_array($this->ini_options) || empty($this->ini_options)) {
                    throw new \RuntimeException("File is not a valid ini file.");
                }
                // set to null so we don't parse it twice
                $this->ini_file = null;
            }

            // default to null
            if (!array_key_exists($var, $this->ini_options)) {
                $this->ini_options[$var] = null;
            }
        }

        return $this->ini_options[$var];
    }

    /**
     * Singleton
     *
     * @return GetConfig
     */
    public static function init() {
        static $inst;
        if (empty($inst)) {
            $inst  = new GetConfig();
        }
        return $inst;
    }
}
