<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 20:55
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Exception\ExceptionInterface;
use StrasnyLada\DirSync\Exception\InvalidJsonInputException;
use StrasnyLada\DirSync\Exception\UnableToCreateDirectoryException;
use StrasnyLada\DirSync\Exception\UnableToRemoveDirectoryException;
use StrasnyLada\DirSync\Transformer\ObjectToArrayTransformer;
use StrasnyLada\DirSync\Transformer\TreeToFlatDirectoryStructureTransformer;

final class SyncStructure extends DirSyncBase
{
    const FLAG_CREATE_EMPTY = null;
    const FLAG_IGNORE_CONTENT = false;

    /**
     * @param array $options [optional] Additional options for the directory sync process
     * @return DirSyncInterface
     */
    public function sync($options = [])
    {
        $options = $options ?: [
            DirSync::SYNC_CREATE_ONLY,
            DirSync::SYNC_REMOVE_ONLY,
        ];

        if (in_array(DirSync::SYNC_CREATE_ONLY, $options)) {
            try {
                $this->create();
            } catch (ExceptionInterface $e) {
                error_log($e->getMessage());
            }
        }

        if (in_array(DirSync::SYNC_REMOVE_ONLY, $options)) {
            try {
                $this->remove();
            } catch (ExceptionInterface $e) {
                error_log($e->getMessage());
            }
        }

        return $this;
    }

    /**
     * @throws InvalidJsonInputException
     * @throws UnableToCreateDirectoryException
     * @throws UnableToRemoveDirectoryException
     */
    private function create()
    {
        $srcData = $this->getJsonPaths();
        $dstData = $this->getCurrentPaths();

        // create directories missing by JSON structure
        $pathsToCreate = array_diff(array_keys($srcData), $dstData);
        foreach ($pathsToCreate as $path) {
            if (!$this->mkdir($this->getRootDir() . DIRECTORY_SEPARATOR . $path)) {
                throw new UnableToCreateDirectoryException($path);
            }
        }

        // clean structure
        foreach($srcData as $path => $value) {
            if ($value === self::FLAG_CREATE_EMPTY) {
                // directory should be cleaned
                $absolutePath = $this->getRootDir() . DIRECTORY_SEPARATOR . $path;
                if (file_exists($absolutePath) && !$this->mrProper($absolutePath)) {
                    throw new UnableToRemoveDirectoryException($path);
                }
            }
        }
    }

    /**
     * @throws InvalidJsonInputException
     * @throws UnableToRemoveDirectoryException
     */
    private function remove()
    {
        $srcData = $this->getJsonPaths();
        $dstData = $this->getCurrentPaths();

        // clean structure
        foreach($srcData as $path => $value) {
            if ($value === self::FLAG_CREATE_EMPTY) {
                // directory should be cleaned
                $absolutePath = $this->getRootDir() . DIRECTORY_SEPARATOR . $path;
                if (file_exists($absolutePath) && !$this->mrProper($absolutePath)) {
                    throw new UnableToRemoveDirectoryException($path);
                }
            }
        }

        // remove directories outside the JSON structure
        $dirsToRemove = array_diff($dstData, array_keys($srcData));
        foreach ($dirsToRemove as $path) {
            $absolutePath = $this->getRootDir() . DIRECTORY_SEPARATOR . $path;
            if (!file_exists($absolutePath)) continue;

            // first level directories should be removed
            $isCandidateToRemove = true;

            // try to find parent in structure and apply defined rule
            $ruleCheckPath = $path;
            while(($slashPosition = strrpos($ruleCheckPath, DIRECTORY_SEPARATOR)) !== false) {
                $ruleCheckPath = substr($ruleCheckPath, 0, $slashPosition);
                if (self::hasArrayKey($srcData, $ruleCheckPath)) {
                    $isCandidateToRemove = self::getArrayValue($srcData, $ruleCheckPath) === self::FLAG_CREATE_EMPTY;
                    break;
                }
            }

            // clear directory if it's candidate
            if ($isCandidateToRemove && !$this->mrProper($absolutePath, true)) {
                throw new UnableToRemoveDirectoryException($path);
            }
        }
    }

    /**
     * Transform JSON structure into flat structure in the array
     *
     * @return array
     * @throws InvalidJsonInputException
     */
    private function getJsonPaths()
    {
        $structure = json_decode($this->jsonInput);
        if (!$structure) {
            throw new InvalidJsonInputException($this->jsonInput);
        }

        // convert object structure to array
        $treeStructure = (new ObjectToArrayTransformer())->transform($structure);
        // get flat structure paths
        $flatStructure = (new TreeToFlatDirectoryStructureTransformer())->transform($treeStructure);

        // filter out actions
        $directoryPaths = [];
        foreach ($flatStructure as $path => $value) {
            // simple directory sync
            if (strpos($path, self::PREFIX_ACTION) === false) {
                $directoryPaths[$path] = $value === '' ? self::FLAG_IGNORE_CONTENT : $value;
            } // directory connected with action
            else {
                $pathChunks = [];
                foreach (explode(DIRECTORY_SEPARATOR, $path) as $directory) {
                    // identify action
                    if (strpos($directory, self::PREFIX_ACTION) !== false) break;

                    // create subdirectories until an action is identified
                    $pathChunks[] = $directory;
                }
                $directoryPaths[implode(DIRECTORY_SEPARATOR, $pathChunks)] = self::FLAG_IGNORE_CONTENT;
            }
        }

        return $directoryPaths;
    }

    /**
     * Returns current structure in the root dir
     *
     * @return array
     */
    private function getCurrentPaths()
    {
        $paths = [];
        $basePathLength = mb_strlen($this->getRootDir(), 'utf-8');

        // scan content of the root directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->getRootDir(),
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        // iterate items and store directories
        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                // store relative path under root dir
                $paths[] = substr($item->getPathname(), $basePathLength + 1);
            }
        }

        sort($paths);

        return $paths;
    }
}