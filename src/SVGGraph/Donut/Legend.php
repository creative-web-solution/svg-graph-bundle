<?php

namespace Cws\Bundle\SVGGraphBundle\Donut;

use Cws\Bundle\SVGGraphBundle\IGraphLegend;
use Cws\Bundle\SVGGraphBundle\Tools\Text;

/**
 * Class Legend
 * @package Cws\Bundle\SVGGraphBundle\Donut
 */
class Legend implements IGraphLegend
{
    private $data;
    private $graphStyle;
    private $graph;
    private $drawingData;

    /**
     * DonutLegend constructor.
     *
     * @param $data
     * @param $graph
     * @param $graphStyle
     */
    public function __construct($data, $graph, $graphStyle)
    {
        $this->data = $data;
        $this->graph = $graph;
        $this->graphStyle = $graphStyle;
        $this->drawingData  = $graph->getDrawingData();
    }

    /**
     * @param $arcList
     *
     * @return string
     */
    private function createAllLabels($arcList)
    {
        $html = [];

        foreach ($arcList as $arc) {
            $html[] = $this->createLabel($arc);
        }

        return implode('', $html);
    }

    /**
     * @param $arc
     *
     * @return string
     */
    private function createLabel($arc)
    {
        $drawindData = $arc->getDrawingData();

        $cssClass = $this->graphStyle->legend->labelCssClass;

        if ($drawindData->centerArcPoint->y < 75) {
            $cssClass .= ' alt';
        }

        $position = $drawindData->legendLinePoints->point1;

        $text = new Text($drawindData->data->label, $position, $cssClass, $drawindData->data->color);

        return $text->create();
    }

    /**
     * @return string
     */
    public function create()
    {
        $arcList = $this->drawingData->arcList;
        $html = $this->createAllLabels($arcList);

        if (isset($this->graphStyle->mainLegend)) {
            $mainLegend = new Text(
                $this->graphStyle->mainLegend->label,
                $this->drawingData->center,
                $this->graphStyle->mainLegend->cssClass
            );

            $html .= $mainLegend->create();
        }

        return $html;
    }
}
