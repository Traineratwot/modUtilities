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
    'in' => NULL,
    'rest_id' => 0,
    'input' => NULL,
    'output' => NULL,
    'user' => NULL,
  ),
  'fieldMeta' => 
  array (
    'in' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'rest_id' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'input' => 
    array (
      'dbtype' => 'json',
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
      'dbtype' => 'json',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'in' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'FK__modutilitiesrest' => 
    array (
      'alias' => 'FK__modutilitiesrest',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'rest_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
