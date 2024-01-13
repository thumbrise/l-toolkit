<?php

namespace Thumbrise\Toolkit\Opresult\Generate\Internal;

use Closure;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Thumbrise\Toolkit\Opresult\OperationResult;

class FindOpresultsWithError
{
    /**
     * @param string $filepath
     *
     * @return OperationResult[]
     */
    public function do(string $filepath): array
    {
        $ast = $this->parseFile($filepath);

        $returnsOpresultError = $this->filterNodes($ast, $this->isReturnAndIsOpresultError(...));
        //TODO Нужна логика рекурсивного поиска возвратов ошибок opresult
        $returnsUnknown = $this->filterNodes($ast, $this->isReturnAndIsUnknown(...));
        dd($returnsUnknown);
        /** @var \PhpParser\Node\Stmt\Return_ $returnStmt */
        foreach ($returnsUnknown as $returnStmt) {
            $expr = $returnStmt->expr;
//            if ((! $expr instanceof Node\Expr\MethodCall
//                 && ! $expr instanceof Node\Expr\StaticCall
//                )
//                && ! $expr->var instanceof Node\Expr\Variable
//            ) {
//                continue;
//            }
            if (! $expr instanceof Node\Expr\MethodCall) {
                continue;
            }
            if (! $expr instanceof Node\Expr\MethodCall) {
                continue;
            }
            $variableName = $returnStmt->expr->var;
            $variableName->
            $allWithThisName = [];
            dd($returnStmt);
        }
        return $this->parseOpresultsFromReturns($returnsOpresultError);
    }

    private function filterNodes(array $nodes, Closure $filter): array
    {
        $traverser = new NodeTraverser();
        $visitor = new FindingVisitor($filter);
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);

        return $visitor->getFoundNodes();
    }

    private function isOpresultError(Node\Stmt\Return_ $node): bool
    {
        $opresultParts = explode('\\', OperationResult::class);

        return $node->expr instanceof Node\Expr\StaticCall
               && $node->expr->class instanceof Node\Name\FullyQualified
               && $node->expr->class->getParts() == $opresultParts
               && $node->expr->name instanceof Node\Identifier
               && $node->expr->name->name === 'error';
    }

    private function isReturnAndIsOpresultError(Node $node): bool
    {
        return $node instanceof Node\Stmt\Return_ && $this->isOpresultError($node);
    }

    private function isReturnAndIsUnknown(Node $node): bool
    {
        return $node instanceof Node\Stmt\Return_ && ! $this->isOpresultError($node);
    }

    private function parseFile(string $filepath)
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver(null, [
            'preserveOriginalNames' => false,
            'replaceNodes' => true,
        ]));

        $code = file_get_contents($filepath);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($code);

        return $nodeTraverser->traverse($ast);
    }

    /**
     * @param \PhpParser\Node\Stmt\Return_[] $nodes
     *
     * @return \Thumbrise\Toolkit\Opresult\OperationResult[]
     */
    private function parseOpresultsFromReturns(array $nodes): array
    {
        $opresults = [];
        foreach ($nodes as $node) {
            if (! $node->expr instanceof Node\Expr\StaticCall) {
                throw new InvalidArgumentException('Type error');
            }
            $args = $node->expr->getArgs();
            $message = @$args[0]?->value?->value;
            $code = @$args[1]?->value?->name?->name ?? @$args[1]?->value?->value;
            $opresults[] = OperationResult::error($message, $code);
        }

        return $opresults;
    }

}