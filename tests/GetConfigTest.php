<?php

namespace DealNews\GetConfig\Tests;

use \DealNews\GetConfig\GetConfig;

class GetConfigTest extends \PHPUnit\Framework\TestCase {
    public function testFindFile() {
        $config = new GetConfig(__DIR__ . '/etc/config.ini', __DIR__ . '/etc/config.d/');

        $this->assertEquals(
            __DIR__ . '/etc/config.json',
            $config->findFile('config.json')
        );

        $this->assertEquals(
            __DIR__ . '/etc/config.d/01-config.ini',
            $config->findFile('01-config.ini')
        );

        $this->assertNull($config->findFile('not-found.ini'));
    }

    public function testGetEnv() {
        $config = new GetConfig(__DIR__ . '/etc/config.env', '');

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $ini = $config->get('test.string');

        $this->assertEquals('example-env', $ini);
    }

    public function testGetYaml() {
        $config = new GetConfig(__DIR__ . '/etc/config.yaml', '');

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $ini = $config->get('test.string');

        $this->assertEquals('example-yaml', $ini);
    }

    public function testGetJson() {
        $config = new GetConfig(__DIR__ . '/etc/config.json', '');

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $ini = $config->get('test.string');

        $this->assertEquals('example-json', $ini);
    }

    public function testGetDir() {
        $DN_INI_FILE = getenv('DN_INI_FILE');
        $DN_ETC_DIR  = getenv('DN_ETC_DIR');

        putenv('DN_ETC_DIR=');
        putenv('DN_INI_FILE=');

        putenv('DN_INI_DIR=' . __DIR__ . '/etc/config.d/');

        $config = new GetConfig();

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $this->assertEquals('example-config-ini-1', $config->get('test.string'));

        $this->assertEquals('example-config-env-2', $config->get('test.string2'));

        $this->assertEquals('example-config-yaml-3', $config->get('test.string3'));

        $this->assertEquals('example-config-json-4', $config->get('test.string4'));

        $this->assertEquals('yep', $config->get('test.override.me'));


        putenv('DN_INI_DIR=');
        putenv("DN_ETC_DIR=$DN_ETC_DIR");
        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testGetDirPassedIn() {
        $DN_INI_FILE = getenv('DN_INI_FILE');
        $DN_ETC_DIR  = getenv('DN_ETC_DIR');

        putenv('DN_ETC_DIR=');
        putenv('DN_INI_FILE=');

        $config = new GetConfig(null, __DIR__ . '/etc/config.d/');

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $this->assertEquals('example-config-ini-1', $config->get('test.string'));

        $this->assertEquals('example-config-env-2', $config->get('test.string2'));

        $this->assertEquals('example-config-yaml-3', $config->get('test.string3'));

        $this->assertEquals('example-config-json-4', $config->get('test.string4'));

        $this->assertEquals('yep', $config->get('test.override.me'));

        putenv("DN_ETC_DIR=$DN_ETC_DIR");
        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testGet() {
        $DN_INI_FILE = getenv('DN_INI_FILE');
        $DN_ETC_DIR  = getenv('DN_ETC_DIR');

        putenv('DN_ETC_DIR=');
        putenv('DN_INI_FILE=');

        $config = new GetConfig(__DIR__ . '/etc/get_config_test.ini');

        // check for an unset value first, should
        // return null and not throw errors
        $ini = $config->get('not.found');

        $this->assertNull($ini);

        $ini = $config->get('test.string');

        $this->assertEquals('example', $ini);

        putenv("DN_ETC_DIR=$DN_ETC_DIR");
        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testEnvVarWins() {
        $config = GetConfig::init();

        // defined in both the ini file and in
        // the phpunit.xml.dist file
        $ini = $config->get('config.test.env.var');

        $this->assertEquals('foo', $ini);


        putenv('CONFIG_TEST_ENV_VAR=bar');
        $config = new GetConfig();

        // check dummy value to load up ini files
        $config->get('config.test.env.dummy');

        // env var should still be bar even though it has
        // not been checked yet.
        $ini = $config->get('config.test.env.var');

        $this->assertEquals('bar', $ini);

        // restore the previous value
        putenv('CONFIG_TEST_ENV_VAR=foo');
    }

    public function testEnvDotVar() {
        $config = new GetConfig();

        $ini = $config->get('config.test.env.dot.var');

        $this->assertEquals('foobar', $ini);
    }

    public function testEnvFile() {
        $config = new GetConfig();

        $ini = $config->get('test.string');

        $this->assertEquals('example-env', $ini);
    }

    public function testEmptyValue() {
        $config = new GetConfig();

        $ini = $config->get('test.empty');
        $this->assertEquals('', $ini);

        $ini = $config->get('test.zero');
        $this->assertEquals(0, $ini);
    }

    public function testEnvDir() {
        $DN_INI_FILE = getenv('DN_INI_FILE');
        putenv('DN_INI_FILE=');

        $config = new GetConfig();

        $ini = $config->get('test.string');

        $this->assertEquals('example-ini', $ini);

        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testEnvVarNoFile() {
        $DN_INI_FILE = getenv('DN_INI_FILE');
        $DN_ETC_DIR  = getenv('DN_ETC_DIR');

        putenv('DN_ETC_DIR=');
        putenv('DN_INI_FILE=');
        $config = new GetConfig();

        $ini = $config->get('config.test.env.var');

        $this->assertEquals('foo', $ini);

        putenv("DN_ETC_DIR=$DN_ETC_DIR");
        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testNoFile() {
        $this->expectException(\InvalidArgumentException::class);
        $config = new GetConfig(__DIR__ . '/bad_filename.ini');
    }

    public function testBadIniFile() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Attempted to load it 3 times\\./');
        $config = new GetConfig(__DIR__ . '/etc/get_config_bad.ini');
        $ini    = $config->get('test.string');
    }

    public function testBadJsonFile() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Attempted to load it 3 times\\./');
        $config = new GetConfig(__DIR__ . '/etc/get_config_bad.json');
        $ini    = $config->get('test.string');
    }

    public function testBadYamlFile() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Attempted to load it 3 times\\./');
        $config = new GetConfig(__DIR__ . '/etc/get_config_bad.yaml');
        $ini    = $config->get('test.string');
    }

    public function testMissingFileEnvVar() {
        $DN_INI_FILE = getenv('DN_INI_FILE');

        putenv('DN_INI_FILE=some_missing_filename.ini');

        $this->expectException(\InvalidArgumentException::class);
        $config = new GetConfig();

        putenv("DN_INI_FILE=$DN_INI_FILE");
    }

    public function testBadFileType() {
        $this->expectException(\RuntimeException::class);
        $config = new GetConfig(__DIR__ . '/etc/get_config_bad.txt');
        $config->get('foo');
    }

}
