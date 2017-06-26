<?php
/**
 * Copyright (c) 2014 - 2016. ООО "БАРС - 46"
 */

namespace Citrus\NSTree;

class ExampleTable extends DataManager {

    public static function getTableName()
    {
        return 'example';
    }

    public static function getMap()
    {
        return array(
            'ID'            => array(
                'data_type'    => 'integer',
                'primary'      => true,
                'autocomplete' => true,
            ),
            'PARENT_ID'     => array(
                'data_type' => 'integer',
            ),
            'LEFT_MARGIN' => array(
                'data_type' => 'integer',
            ),
            'RIGHT_MARGIN' => array(
                'data_type' => 'integer',
            ),
            'DEPTH_LEVEL' => array(
                'data_type' => 'integer',
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
            ),
            'GLOBAL_ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
            ),
            'SORT' => array(
                'data_type' => 'integer',
            ),
            'NAME'          => array(
                'data_type'  => 'string',
                'required'   => true,
            ),
        );
    }
}