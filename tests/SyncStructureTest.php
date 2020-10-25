<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 13:42
 */

namespace StrasnyLada\DirSync;

use PHPUnit\Framework\TestCase;
use StrasnyLada\DirSync\Exception\ExceptionInterface;

/**
 * Class SyncStructureTest
 * @package StrasnyLada\DirSync
 * @covers StrasnyLada\DirSync\DirSync
 */
final class SyncStructureTest extends TestCase
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

    public function testCreateSyncIntoEmptyDir()
    {
        $dst = self::getStructurePath();

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_CREATE_ONLY,
                ]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertDirectoryExists($dst . '/src/actions');
        $this->assertDirectoryExists($dst . '/tests/tmp');
        $this->assertDirectoryExists($dst . '/vendor');
        $this->assertDirectoryExists($dst . '/app/web');
        $this->assertDirectoryNotExists($dst . '/app/#copy');
    }

    public function testCreateSyncIntoNonEmptyDir()
    {
        $dst = self::getStructurePath();

        shell_exec(sprintf('cp -r %s %s', __DIR__ . '/testStructure/', __DIR__ . '/tmp'));

        $this->assertDirectoryNotExists($dst . '/src/actions');
        $this->assertDirectoryExists($dst . '/src/validator');
        $this->assertFileExists($dst . '/src/controller/TestController.php');

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_CREATE_ONLY,
                ]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertDirectoryExists($dst . '/src/actions');
    }

    public function testRemoveSync()
    {
        $dst = self::getStructurePath();

        shell_exec(sprintf('cp -r %s %s', __DIR__ . '/testStructure/', __DIR__ . '/tmp'));

        $this->assertDirectoryExists($dst . '/log/apache');
        $this->assertFileExists($dst . '/log/apache/access.log');
        $this->assertDirectoryExists($dst . '/log/app/frontend');
        $this->assertDirectoryExists($dst . '/RemoveSyncTestDir');
        $this->assertFileExists($dst . '/src/controller/TestController.php');

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_REMOVE_ONLY,
                ]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertDirectoryExists($dst . '/log/apache');
        $this->assertFileNotExists($dst . '/log/apache/access.log');
        $this->assertDirectoryNotExists($dst . '/log/app/frontend');
        $this->assertDirectoryNotExists($dst . '/RemoveSyncTestDir');
        $this->assertFileExists($dst . '/src/controller/TestController.php');
    }

    public function testSync()
    {
        $dst = self::getStructurePath();

        shell_exec(sprintf('cp -r %s %s', __DIR__ . '/testStructure/', __DIR__ . '/tmp'));

        $this->assertDirectoryExists($dst . '/log/apache');
        $this->assertFileExists($dst . '/log/apache/access.log');
        $this->assertDirectoryExists($dst . '/log/app/frontend');
        $this->assertDirectoryExists($dst . '/RemoveSyncTestDir');
        $this->assertDirectoryNotExists($dst . '/src/actions');
        $this->assertFileExists($dst . '/src/controller/TestController.php');
        $this->assertDirectoryExists($dst . '/src/validator');

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_CREATE_ONLY,
                    DirSync::SYNC_REMOVE_ONLY,
                ]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertDirectoryExists($dst . '/app/web');
        $this->assertDirectoryNotExists($dst . '/app/#copy');
        $this->assertDirectoryExists($dst . '/log/apache');
        $this->assertFileNotExists($dst . '/log/apache/access.log');
        $this->assertDirectoryNotExists($dst . '/log/app/frontend');
        $this->assertDirectoryNotExists($dst . '/RemoveSyncTestDir');
        $this->assertDirectoryExists($dst . '/src/actions');
        $this->assertFileExists($dst . '/src/controller/TestController.php');
        $this->assertDirectoryExists($dst . '/tests/tmp');
        $this->assertDirectoryExists($dst . '/vendor');
    }

    /**
     * Returns path to testing place
     *
     * @return string
     */
    private static function getJsonInputFilePath()
    {
        return __DIR__ . '/testStructure.json';
    }

    /**
     * Returns path to testing place
     *
     * @return string
     */
    private static function getStructurePath()
    {
        return __DIR__ . '/tmp';
    }
}