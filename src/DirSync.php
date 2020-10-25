<?php
/**
 * Created by PhpStorm.
 * User: tomas.vitek
 * Date: 24.10.2020
 * Time: 20:54
 */

namespace StrasnyLada\DirSync;

use StrasnyLada\DirSync\Exception\ExceptionInterface;

class DirSync extends DirSyncBase
{
    /**
     * @param array $options [optional] Additional options for the directory sync process
     * @param array $actions [optional] Action classes allowed within sync process
     * @return DirSyncInterface
     */
    public function sync($options = [], $actions = [])
    {
        try {
            $syncStructure = new SyncStructure();
            $syncStructure->setRootDir($this->getRootDir());
            $syncStructure->setJsonInput($this->getJsonInput());
            $syncStructure->sync($options);
        } catch (ExceptionInterface $e) {
            error_log($e->getMessage());
        }

        try {
            $syncActions = new SyncActions();
            $syncActions->setRootDir($this->getRootDir());
            $syncActions->setJsonInput($this->getJsonInput());
            $syncActions->sync($options, $actions);
        } catch (ExceptionInterface $e) {
            error_log($e->getMessage());
        }

        return $this;
    }
}