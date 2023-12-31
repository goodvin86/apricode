<?php

namespace DoctrineExtensions\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * @author      Marius Krämer <marius@marius-kraemer.de>
 */
class UtcTimestamp extends FunctionNode
{
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        return 'UTC_TIMESTAMP()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
