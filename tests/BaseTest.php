<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 22.10.2020
 * Time: 19:06
 */

namespace StrasnyLada\DirSync;

use PHPUnit\Framework\TestCase;

final class BaseTest extends TestCase
{
    /**
     * Test write permissions
     */
    public function testAbilityToWrite(): void
    {
        $this->assertDirectoryIsWritable(self::getStructurePath());

        $path = self::getStructurePath() . '/xxx';

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
    private static function getStructurePath() {
        return __DIR__ . '/tmp';
    }
}