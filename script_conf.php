<?php
/**
 * Main script configuration file
 *
 * This file holds some basic configuration values. Defaults should work right
 * off the box, but if you have eny specific nedds change them here rather than
 * in main script.
 *
 *         !!! REMEMBER TO END EACH LINE WITH SEMICOLON (;) !!!
 */

/*
 * Database configuration script name.
 *
 * Sample file is present in the same directory as main index.php script, but
 * it should be copied outside of web root dir. It may pose risk of name
 * collision with another package so there is a way to change it.
 */
$dbConfFileName = 'db_conf.php';

/*
 * The number of subdirectories between web root dir and main (index.php)
 * script's dir.
 *
 * Database configuration file should be placed outside of (above) web root dir.
 * Default is that main (index.php) script file is located right in web root
 * dir (hence default '0' value). If it hapens that main script is located in
 * subdir (or deeper) of web root dir change this variable accordingly.
 */
$indexFileDepthRelativeToWebRoot = 0;

/*
 * Configuration script path building prefix.
 *
 * DO NOT TOUCH unles you know what you are doing.
 */
$dbConfFilePathPrefix = '../';
