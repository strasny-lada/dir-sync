<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 22.10.2020
 * Time: 19:06
 */

namespace StrasnyLada\DirSync;

use PHPUnit\Framework\TestCase;

/**
 * Class BaseTest
 * @package StrasnyLada\DirSync
 */
final class BaseTest extends TestCase
{
    /** @var DirSync */
    private $dirSync;

    protected function setUp()
    {
        $this->dirSync = new DirSync();
        $this->dirSync->mrProper(self::getStructurePath());
    }

    protected function tearDown()
    {
        $this->dirSync->mrProper(self::getStructurePath());
    }

    /**
     * Test write permissions
     */
    public function testAbilityToWrite(): void
    {
        $this->assertDirectoryIsWritable(self::getStructurePath());

        $path = self::getStructurePath() . '/AbilityToWriteTestDir';
        if (file_exists($path)) rmdir($path);

        $this->assertDirectoryNotExists($path);

        mkdir($path);
        $this->assertDirectoryExists($path);

        rmdir($path);
        $this->assertDirectoryNotExists($path);
    }

    /**
     * Returns path to testing place
     *
     * @return string
     */
    protected static function getStructurePath() {
        return __DIR__ . '/tmp';
    }
}