# Bump plugin
[![Build Status](https://travis-ci.org/spartaksun/composer-bump-plugin.svg?branch=master)](https://travis-ci.org/spartaksun/composer-bump-plugin)

## Install
```bash
composer require-dev spartaksun/composer-bump-plugin
```

## Usage
After plugin package installed you will be able to call `composer bump` command.
Without any additional arguments it will increment a patch part of version mentioned in composer.json.
Command will also create a backup copy of `composer.json` in `composer.json-backup` file.

#### Increment patch part of a version

 ```bash
composer bump patch
```
For example: 1.0.3 => 1.0.4



#### Increment minor part of a version and reset patch

 ```bash
composer bump minor
```
For example: 1.0.3 => 1.1.0



#### Increment major part of a version and reset patch and minor

 ```bash
composer bump major
```
For example: 1.17.34 => 2.0.0


#### Change default indentation in composer.json
```bash
composer bump -i 4
```
This command will change indentation in composer.json to 4 spaces/tabs


Nex command will increment minor, set indentation to 2 and disable creating of backup file.
```bash
composer bump minor -i 2 no-backup
```

## Callbacks
You may optionally specify callbacks scripts in your `composer.json`:
```json
{
  "scripts": {
     "pre-bump": "./my_script.sh",
     "post-bump": "bin/console post:bump"
   }
}
```
`pre-bump` script will be called before incrementing a version with argument `--old-version`.

`post-bump` script will be called after version is incremented with arguments `--old-version` and `--new-version` added to your script. 
