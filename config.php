<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbname    = 'u704129739_cQ6l7';
$CFG->dbuser    = 'u704129739_PAJjh';
$CFG->dbpass    = 'PP9y2SME7u';
$CFG->prefix    = 'zjx5_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '0',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot   = 'https://aulavirtual.dfpol.es/desarrollo';
$CFG->dataroot  = '/home/u704129739/domains/dfpol.es/public_html/aulavirtual/.htuvcmxvorwixd.data/';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
//$CFG->cachejs = false;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
