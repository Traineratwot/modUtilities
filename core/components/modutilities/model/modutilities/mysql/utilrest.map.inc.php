<?php
$xpdo_meta_map['Utilrest']= array (
  'package' => 'modutilities',
  'version' => '1.1',
  'table' => 'utilrest',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'permission' => NULL,
    'url' => NULL,
    'snippet' => NULL,
    'param' => NULL,
    'allowMethod' => NULL,
    'BASIC_auth' => 0,
    'category' => NULL,
  ),
  'fieldMeta' => 
  array (
    'permission' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => true,
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
      'index' => 'unique',
    ),
    'snippet' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => true,
    ),
    'param' => 
    array (
      'dbtype' => 'longtext',
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
      'default' => 0,
    ),
    'category' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'url' => 
    array (
      'alias' => 'url',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'url' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
