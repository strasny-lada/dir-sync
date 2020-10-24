<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 13:42
 */

namespace StrasnyLada\DirSync;

use PHPUnit\Framework\TestCase;
use StrasnyLada\DirSync\Exception\DirectoryDoesNotExistExceptionInterface;
use StrasnyLada\DirSync\Exception\FileDoesNotExistExceptionInterface;
use StrasnyLada\DirSync\Exception\InvalidJsonInputExceptionInterface;

/**
 * @covers StrasnyLada\DirSync\DirSync
 */
class SyncTest extends TestCase
{
    /**
     * @var DirSync
     */
    private $dirSync;

    protected function setUp()
    {
        $this->dirSync = new DirSync;
    }

    public function testCreateSync()
    {
        $dst = self::getStructurePath();
        self::mrProper($dst);

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_CREATE_ONLY,
                ]);
        } catch (DirectoryDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (FileDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (InvalidJsonInputExceptionInterface $e) {
            // TODO
        }

        $this->assertDirectoryExists($dst . '/src/actions');
        $this->assertDirectoryExists($dst . '/tests/tmp');
        $this->assertDirectoryNotExists($dst . '/example/#copy');

        self::mrProper($dst);
    }

    public function testRemoveSync()
    {
        $dst = self::getStructurePath();
        self::mrProper($dst);

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_CREATE_ONLY,
                ]);
        } catch (DirectoryDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (FileDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (InvalidJsonInputExceptionInterface $e) {
            // TODO
        }

        $this->assertDirectoryExists($dst . '/src/actions');
        $this->assertDirectoryExists($dst . '/tests/tmp');
        $this->assertDirectoryNotExists($dst . '/example/#copy');

        $removeDirPath = $dst . '/xxx';
        mkdir($removeDirPath);
        $this->assertDirectoryExists($removeDirPath);

        try {
            $this->dirSync
                ->setRootDir($dst)
                ->fromFile(self::getJsonInputFilePath())
                ->sync([
                    DirSync::SYNC_REMOVE_ONLY,
                ]);
        } catch (DirectoryDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (FileDoesNotExistExceptionInterface $e) {
            // TODO
        } catch (InvalidJsonInputExceptionInterface $e) {
            // TODO
        }

        $this->assertDirectoryExists($dst . '/src/actions');
        $this->assertDirectoryExists($dst . '/tests/tmp');
        $this->assertDirectoryNotExists($removeDirPath);
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

    /**
     * @param string $dir
     * @return bool
     */
    private static function mrProper($dir, $self = false)
    {
        foreach (array_diff(scandir($dir), ['.','..']) as $file) {
            (is_dir("$dir/$file")) ? self::mrProper("$dir/$file", true) : unlink("$dir/$file");
        }
        return $self ? rmdir($dir) : true;
    }
}