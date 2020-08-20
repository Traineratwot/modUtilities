<?php
$xpdo_meta_map['Utilrestcategory']= array (
  'package' => 'modutilities',
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
      'index' => 'unique',
    ),
    'permission' => 
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
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
