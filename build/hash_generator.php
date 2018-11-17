#!/usr/bin/env php
<?php
/**
 * Script used to generate hashes for release packages
 *
 * Usage: php build/hash_generator.php
 *
 * @package     Joomla.Plugin
 * @subpackage  Installer.webinstaller
 *
 * @copyright   Copyright (C) 2013 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

$packageDir = __DIR__ . '/packages';

$hashes = array();

/** @var DirectoryIterator $file */
foreach (new DirectoryIterator($packageDir) as $file)
{
	if ($file->isDir() || $file->isDot())
	{
		continue;
	}

	$hashes[$file->getFilename()] = array(
		'sha256' => hash_file('sha256', $file->getPathname()),
		'sha384' => hash_file('sha384', $file->getPathname()),
		'sha512' => hash_file('sha512', $file->getPathname()),
	);
}

$jsonOptions = PHP_VERSION_ID >= 50400 ? JSON_PRETTY_PRINT : 0;

@file_put_contents($packageDir . '/checksums.json', json_encode($hashes, $jsonOptions));

echo 'Checksums file generated' . PHP_EOL . PHP_EOL;
