<?php
$xpdo_meta_map['Utilreststats']= array (
  'package' => 'modutilities',
  'version' => '1.1',
  'table' => 'utilreststats',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'rest_id' => 0,
    'input' => NULL,
    'output' => NULL,
    'user' => NULL,
    'time' => NULL,
    'datetime' => NULL,
  ),
  'fieldMeta' => 
  array (
    'rest_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'input' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => true,
    ),
    'output' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => true,
    ),
    'user' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => true,
    ),
    'time' => 
    array (
      'dbtype' => 'float',
      'precision' => '12,6',
      'attributes' => 'unsigned',
      'phptype' => 'float',
      'null' => true,
    ),
    'datetime' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
  ),
);
