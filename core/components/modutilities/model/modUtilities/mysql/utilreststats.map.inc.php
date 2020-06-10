<?php
$xpdo_meta_map['Utilreststats']= array (
  'package' => 'modUtilities',
  'version' => '1.1',
  'table' => 'utilreststats',
  'extends' => 'xPDOObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'id' => NULL,
    'stats' => NULL,
    'log' => NULL,
  ),
  'fieldMeta' => 
  array (
    'id' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => true,
      'index' => 'index',
    ),
    'stats' => 
    array (
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
    'log' => 
    array (
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'FK__modutilitiesrest' => 
    array (
      'alias' => 'FK__modutilitiesrest',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
