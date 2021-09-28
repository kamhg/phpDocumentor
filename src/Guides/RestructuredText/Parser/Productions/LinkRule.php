<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\Link;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#hyperlink-targets
 */
final class LinkRule implements Rule
{
    /** @var LineDataParser */
    private $lineDataParser;

    /** @var Environment */
    private $environment;

    public function __construct(LineDataParser $lineDataParser, Environment $environment)
    {
        $this->lineDataParser = $lineDataParser;
        $this->environment = $environment;
    }

    public function applies(DocumentParser $documentParser): bool
    {
        $link = $this->lineDataParser->parseLink($documentParser->getDocumentIterator()->current());

        return $link !== null;
    }

    public function apply(DocumentIterator $documentIterator): ?Node
    {
        $link = $this->lineDataParser->parseLink($documentIterator->current());
        if ($link === null) {
            throw new InvalidArgumentException();
        }

        $node = null;
        if ($link->getType() === Link::TYPE_ANCHOR) {
            $node = new AnchorNode($link->getName());
        }
        $this->environment->setLink($link->getName(), $link->getUrl());

        return $node;
    }
}
