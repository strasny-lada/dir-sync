<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 20:11
 */

namespace StrasnyLada\DirSync\Action;

use StrasnyLada\DirSync\Exception\InvalidActionParametersException;
use StrasnyLada\DirSync\Exception\UnableToCreateDirectoryException;
use StrasnyLada\DirSync\Exception\UnableToCreateFileException;

class Copy extends ActionBase
{
    /** @var string */
    private $srcPath;

    /** @var string */
    private $dstPath;

    /**
     * @throws UnableToCreateDirectoryException
     * @throws UnableToCreateFileException
     */
    public function process()
    {
        $srcPath = $this->getSourcePath();
        $dstPath = $this->getDestinationPath();

        $srcPathLength = mb_strrpos($srcPath, DIRECTORY_SEPARATOR, 0, 'utf-8') + 1;

        // scan content of the source directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $srcPath,
                \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // copy directory content
        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            $relativePath = ltrim(substr($item->getPathname(), $srcPathLength), DIRECTORY_SEPARATOR);
            $itemDstPath = $dstPath . DIRECTORY_SEPARATOR . $relativePath;

            if (
                $item->isDir()
                &&
                !file_exists($itemDstPath)
            ) {
                $this->addMessage(sprintf('Copy action: create directory %s', $itemDstPath));
                if (!$this->mkdir($itemDstPath)) {
                    throw new UnableToCreateDirectoryException($itemDstPath);
                }
            } elseif (
                !$item->isDir()
                &&
                !file_exists($itemDstPath)
            ) {
                $this->addMessage(sprintf('Copy action: copy file from %s to %s', $item->getPathname(), $itemDstPath));
                if (!copy($item->getPathname(), $itemDstPath)) {
                    throw new UnableToCreateFileException($itemDstPath);
                }
            }
        }
    }

    /**
     * @throws InvalidActionParametersException
     */
    public function validate()
    {
        if (empty($this->parameters)) {
            throw new InvalidActionParametersException($this->parameters, 'Empty parameters');
        } elseif (!is_array($this->parameters)) {
            throw new InvalidActionParametersException($this->parameters, 'Invalid parameters');
        } elseif (count($this->parameters) !== 2) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Action requires source and destination directories in parameters'
            );
        } elseif (!($this->parameters[0] ?? null) || !($this->parameters[1] ?? null)) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Action parameters should be in indexed array'
            );
        } elseif (!file_exists($this->getSourcePath())) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Source directory does not exist'
            );
        } elseif (!file_exists($this->getDestinationPath())) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Destination directory does not exist'
            );
        }
    }

    /**
     * @return string
     */
    private function getSourcePath()
    {
        if (!$this->srcPath) {
            $this->srcPath = $this->getAbsolutePath($this->parameters[0]);
        }
        return $this->srcPath;
    }

    /**
     * @return string
     */
    private function getDestinationPath()
    {
        if (!$this->dstPath) {
            $this->dstPath = $this->getAbsolutePath($this->parameters[1]);
        }
        return $this->dstPath;
    }
}