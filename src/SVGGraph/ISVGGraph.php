<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph;

use SVG\Nodes\Structures\SVGDocumentFragment;

interface ISVGGraph
{
    public function create(SVGDocumentFragment $svgDocument);
}
