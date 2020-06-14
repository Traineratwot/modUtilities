<?php
$xpdo_meta_map['Utilreststats']= array (
  'package' => 'modUtilities',
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
    'datetime' => 'CURRENT_TIMESTAMP',
  ),
  'fieldMeta' => 
  array (
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
      'default' => 'CURRENT_TIMESTAMP',
      'extra' => 'default_generated',
    ),
  ),
  'indexes' => 
  array (
    'FK_modutil_utilreststats_modutil_utilrest' => 
    array (
      'alias' => 'FK_modutil_utilreststats_modutil_utilrest',
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
