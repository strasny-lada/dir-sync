<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 26.10.2020
 * Time: 00:29
 */

namespace StrasnyLada\DirSync\Action;

use StrasnyLada\DirSync\Exception\InvalidActionParametersException;
use StrasnyLada\DirSync\Exception\UnableToCreateLinkException;

class SymLink extends ActionBase
{
    /** @var string */
    private $srcPath;

    /**
     * @throws UnableToCreateLinkException
     */
    public function process()
    {
        $srcPath = $this->getSourcePath();
        $dstPath = $this->path . DIRECTORY_SEPARATOR . basename($srcPath);

        if (
            !file_exists($dstPath)
            &&
            !symlink($srcPath, $dstPath)
        ) {
            throw new UnableToCreateLinkException($dstPath);
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
        } elseif (count($this->parameters) < 1) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Action requires source directory in parameters'
            );
        } elseif (!($this->parameters[0] ?? null)) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Action parameters should be in indexed array'
            );
        } elseif (!file_exists($this->getSourcePath())) {
            throw new InvalidActionParametersException(
                $this->parameters,
                'Source directory does not exist'
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
}