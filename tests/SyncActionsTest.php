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
use StrasnyLada\DirSync\Exception\ExceptionInterface;

/**
 * Class ActionsSyncTest
 * @package StrasnyLada\DirSyncBase
 * @covers StrasnyLada\DirSync\DirSync
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

    public function testCopyActions()
    {
        $dst = self::getStructurePath();

        // create structure
        $this->createStructure();

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