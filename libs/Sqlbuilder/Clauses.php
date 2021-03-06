<?php

declare(strict_types=1);

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
 * SQL builder clauses.
 *
 * @copyright   copyright (c) 2016-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Clauses
{
    /**
     * String to use for joining multiple clauses.
     *
     * @var     string
     */
    protected string $joiner;

    /**
     * Prefix string for joined clauses.
     *
     * @var     string
     */
    protected string $prefix;

    /**
     * Postfix string for joined clauses.
     *
     * @var     string
     */
    protected string $postfix;

    /**
     * Clauses.
     *
     * @var     array
     */
    protected array $clauses;

    /**
     * Parameters.
     *
     * @var     array
     */
    protected array $parameters = array();

    /**
     * Constructor.
     *
     * @param   string              $joiner                     String to use for joining multiple clauses.
     * @param   string              $prefix                     Prefix string for joined clauses.
     * @param   string              $postfix                    Postfix string for joined clauses.
     */
    public function __construct(string $joiner, string $prefix, string $postfix)
    {
        $this->joiner = $joiner;
        $this->prefix = $prefix;
        $this->postfix = $postfix;

        $this->clauses = [
            true => [],
            false => []
        ];
    }

    /**
     * Resolve clauses.
     *
     * @param   array                       $parameters         Parameters for resolving snippet.
     * @return  string                                          Resolved clauses.
     */
    public function resolveClauses(array &$parameters): string
    {
        $parameters = array_merge($parameters, $this->parameters);

        $filter = function($clause) use ($parameters) {
            $is_exist = true;
            
            if (preg_match_all('/@(?P<type>.):(?P<name>.+?)@/', $clause, $match) > 0) {
                foreach ($match['name'] as $name) {
                    if (!($is_exist = isset($parameters[$name]))) {
                        // all fields must be available
                        break;
                    }
                }
            }

            return $is_exist;
        };

        $clauses = [
            true => array_filter($this->clauses[true], $filter),
            false => array_filter($this->clauses[false], $filter)
        ];

        if (count($clauses[true]) > 0) {
            $snippet = $this->prefix . implode(
                $this->joiner,
                array_merge(
                    $clauses[false],
                    [
                        ' ( ' . implode(' OR ', $clauses[true]) . ' ) '
                    ]
                )
            ) . $this->postfix;
        } elseif (count($clauses[false]) > 0) {
            $snippet = $this->prefix . implode($this->joiner, $clauses[false]) . $this->postfix;
        } else {
            $snippet = '';
        }

        return $snippet;
    }

    /**
     * Add a clause to the list of clauses.
     *
     * @param   string              $sql                        SQL of clause to add.
     * @param   array               $parameters                 Parameters for clause.
     * @param   bool                $is_inclusive               Clause mode.
     */
    public function addClause(string $sql, array $parameters, bool $is_inclusive)
    {
        $this->clauses[$is_inclusive][] = $sql;

        $this->parameters = array_merge($this->parameters, $parameters);
    }
}
