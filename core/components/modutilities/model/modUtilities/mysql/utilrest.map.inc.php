<?php
$xpdo_meta_map['Utilrest']= array (
  'package' => 'modUtilities',
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
    'category' => 1,
  ),
  'fieldMeta' => 
  array (
    'permission' => 
    array (
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
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
      'default' => 0,
    ),
    'category' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => true,
      'default' => 1,
      'index' => 'index',
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
    'FK_modutil_utilrest_modutil_utilrestcategory' => 
    array (
      'alias' => 'FK_modutil_utilrest_modutil_utilrestcategory',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'category' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
