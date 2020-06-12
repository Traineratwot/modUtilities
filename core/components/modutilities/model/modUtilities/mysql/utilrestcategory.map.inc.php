<?php
$xpdo_meta_map['Utilrestcategory']= array (
  'package' => 'modUtilities',
  'version' => '1.1',
  'table' => 'utilrestcategory',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'name' => NULL,
    'permission' => NULL,
    'param' => NULL,
    'allowMethod' => NULL,
    'BASIC_auth' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => true,
    ),
    'permission' => 
    array (
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
    'param' => 
    array (
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
    'allowMethod' => 
    array (
      'dbtype' => 'set',
      'precision' => '\'GET\',\'POST\',\'PUT\',\'DELETE\',\'PATH\',\'CONNECT\',\'HEAD\',\'OPTIONS\',\'TRACE\'',
      'phptype' => 'string',
      'null' => true,
    ),
    'BASIC_auth' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
);
