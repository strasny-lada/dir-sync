<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 20:42
 */

namespace StrasnyLada\DirSync;

use PHPUnit\Framework\TestCase;
use StrasnyLada\DirSync\Action\Copy;
use StrasnyLada\DirSync\Action\SymLink;
use StrasnyLada\DirSync\Exception\ExceptionInterface;

/**
 * Class ActionsSyncTest
 * @package StrasnyLada\DirSyncBase
 * @covers StrasnyLada\DirSync\SyncActions
 */
final class SyncActionsTest extends TestCase
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
     * @covers ::runActions
     * @covers StrasnyLada\DirSync\Action\Copy
     */
    public function testCopyActions()
    {
        $dst = self::getStructurePath();

        // create structure
        $this->createStructure();

        shell_exec(sprintf('cp -r %s %s', __DIR__ . '/testStructure/', __DIR__ . '/tmp'));

        // run actions
        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync(
                    [DirSync::SYNC_ACTIONS_ONLY],
                    [Copy::class]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertDirectoryExists($dst . '/app/web/log/apache');
        $this->assertFileExists($dst . '/app/web/log/apache/error.log');
        $this->assertDirectoryExists($dst . '/app/web/log/app/frontend');
        $this->assertFileExists($dst . '/app/web/log/app/app.log');

        $this->assertFileExists($dst . '/app/data.txt');
        $this->assertFileExists($dst . '/app/controller/TestController.php');
        $this->assertDirectoryExists($dst . '/app/validator');
    }

    /**
     * @covers ::runActions
     * @covers StrasnyLada\DirSync\Action\SymLink
     */
    public function testSymLinkActions()
    {
        $dst = self::getStructurePath();

        // create structure
        $this->createStructure();

        shell_exec(sprintf('cp -r %s %s', __DIR__ . '/testStructure/', __DIR__ . '/tmp'));

        // run actions
        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync(
                    [DirSync::SYNC_ACTIONS_ONLY],
                    [SymLink::class]);
        } catch (ExceptionInterface $e) {
            print($e->getMessage());
        }

        $this->assertFileExists($dst . '/src/apache');
        $this->assertFileExists($dst . '/src/apache/access.log');
    }

    private function createStructure()
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

        $this->assertDirectoryExists($dst . '/app');
        $this->assertDirectoryExists($dst . '/app/web');
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