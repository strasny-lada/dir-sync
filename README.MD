DirSync
============

A simple library for directory synchronization by a given JSON string with an option of a simple actions.

Installation
============

  1. Install by Composer

      ```sh
      composer require strasny-lada/dir-sync
      ```

  1. Add script into composer.json if you would like to launch DirSync through Composer

      ```json
      {
          "scripts": {
              "dir-sync": [
                  "StrasnyLada\\DirSync\\ScriptHandler::sync"
              ]
          }
      }
      ```

Parameters
============

- root-dir - absolute path to sync target directory
- config-file - [optional] absolute path to JSON file with synchronized structure
- config-json - [optional] structure in JSON format to be synchronized
- verbose - [optional] boolean parameter to render synchronization log
- options - [optional] options separated by comma for the directory sync process:
    - create - creating directories only
    - remove - only removing directories
    - actions - just run the action but do not change the directory tree in any way

Launch
============

  1. Through Composer

      ```json
      {
          "scripts": {
              "post-install-cmd": [
                  "@dir-sync parameters"
              ]
          }
      }
      ```

     or from command line

      ```sh
      composer dir-sync parameters
      ```

     possibly inside your application

      ```php
      $dirsync = new \StrasnyLada\DirSync\DirSync();
      $dirsync->setRootDir('/path/to/target');
      $dirsync->fromFile('/path/to/structure.json');
      $dirsync->sync([options]);
      ```

  1. Examples of use

      ```json
      {
          "scripts": {
              "post-install-cmd": [
                  "@dir-sync verbose=1 root-dir=/path/to/target config-file=/path/to/structure.json options=create,remove"
              ]
          }
      }
      ```

      ```sh
      composer dir-sync verbose=1 root-dir=/path/to/target config-file=/path/to/structure.json options=create,remove
      ```

      ```php
      $dirsync = new \StrasnyLada\DirSync\DirSync();
      $dirsync->setRootDir('/path/to/target');
      $dirsync->fromFile('/path/to/structure.json');
      $dirsync->sync(['create','remove']);
      ```
