<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:40
 */

namespace Tixi\ApiBundle\Tile;


use Symfony\Bridge\Twig\Form\TwigRenderer;
use Zend\Stdlib\SplQueue;

class TileRenderer {

    protected $engine;

    public function render(AbstractTile $tile) {
        return $this->render($tile, new \SplStack(), array(), 0);
    }

    protected  function renderTile(AbstractTile $tile, \SplStack $renderedChildren, array $parameters, $depth) {
        if($tile === null) {
            return $renderedChildren;
        }
        $child=$tile->getNextChildToVisit();
        if(null !== $child) {
            $parameters[$depth]=$tile->getViewParameters();
            $depth++;
            $this->renderTile($child, $renderedChildren, $parameters, $depth);
        }
        $renderedChildrenForLevel = array();
        for($i=0; $i<$tile->getAmountOfChildren();$i++) {
            $renderedChildrenForLevel[] = $renderedChildren->pop();
        }
        $renderedChildren->push($this->resolve($tile, $renderedChildrenForLevel, $parameters, $depth));
        $depth--;
        $this->renderTile($tile->getParent(), $renderedChildren, $parameters, $depth);

    }

    protected function resolve(AbstractTile $tile, $parameters, $renderedChildren,$depth) {
        return array($tile->getName()=>$this->engine->render($tile->getTemplateName(), $this->flattenParameters($parameters, $renderedChildren, $depth)));
    }

    protected function flattenParameters($parameters, $renderedChildren, $depth) {
        $viewParameters = array();
        for($i=0;$i<$depth;$i++)  {
            array_merge($viewParameters, $parameters[$i]);
        }
        foreach($renderedChildren as $key=>$value) {
            $viewParameters['children'][$key]=$value;
        }
        array_merge($viewParameters, $renderedChildren);
        return $viewParameters;
    }

    public function setTemplateEngine(TwigRenderer $engine) {
        $this->engine = $engine;
    }






} 