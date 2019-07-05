<?php

namespace DealNews\GetConfig\Tests;

use \DealNews\GetConfig\GetConfig;

class GetConfigTest extends \PHPUnit\Framework\TestCase {

    public function testGet() {

        $config = new GetConfig(__DIR__."/etc/get_config_test.ini");

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get("not.found");

        $this->assertNull($ini);

        $ini = $config->get("test.string");

        $this->assertEquals('example', $ini);
    }

    public function testEnvVarWins() {

        $config = new GetConfig();

        $ini = $config->get("dealnews.test.env.var");

        $this->assertEquals('foo', $ini);
    }

    public function testEnvFile() {

        $config = new GetConfig();

        $ini = $config->get("test.string");

        $this->assertEquals('example2', $ini);
    }

    public function testEnvVarNoFile() {

        $DN_INI_FILE = getenv("DN_INI_FILE");

        putenv("DN_INI_FILE=");
        $config = new GetConfig();

        $ini = $config->get("dealnews.test.env.var");

        $this->assertEquals('foo', $ini);

        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoFile() {
        $config = new GetConfig(__DIR__."/bad_filename.ini");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadFile() {
        $config = new GetConfig(__DIR__."/etc/get_config_empty.ini");
        $ini = $config->get("test.string");
    }
}
