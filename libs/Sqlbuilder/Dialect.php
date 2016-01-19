<?php

/*
 * This file is part of the 'octris/sqlbuilder' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Sqlbuilder;

/**
 * SQL Dialects base class.
 *
 * @copyright   copyright (c) 2016 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class Dialect extends \Octris\Sqlbuilder
{
    /**
     * Return string for limiting result.
     * 
     * @param   int                     $limit              Limit result.
     * @param   int                     $offset             Optional offset to start result at.
     * @return  string                                      SQL snippet for limiting result.
     */
    abstract public function getLimitString($limit, $offset = 0);
}
