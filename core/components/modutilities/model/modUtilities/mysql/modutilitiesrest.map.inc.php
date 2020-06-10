<?php
$xpdo_meta_map['Modutilitiesrest']= array (
  'package' => 'modUtilities',
  'version' => '1.1',
  'table' => 'modutilitiesrest',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'permision' => NULL,
    'url' => NULL,
    'snippet' => NULL,
    'param' => NULL,
  ),
  'fieldMeta' => 
  array (
    'permision' => 
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
      'null' => false,
      'index' => 'unique',
    ),
    'snippet' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'param' => 
    array (
      'dbtype' => 'json',
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
          'null' => false,
        ),
      ),
    ),
  ),
);
