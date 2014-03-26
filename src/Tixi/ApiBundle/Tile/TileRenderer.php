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
use Zend\Stdlib\SplStack;

class TileRenderer {

    protected $engine;

    public function render(AbstractTile $tile) {
        $renderedTile = $this->renderTile(new \SplStack(), array(), 0, $tile);
        return $renderedTile->rawData;
    }

    protected  function renderTile(\SplStack $renderedChildren, array $parameters, $depth, AbstractTile $tile=null) {
        if($tile === null) {
            $toReturn = $renderedChildren->pop();
            return $toReturn;
        }
        $parameters[$depth]=$tile->getViewParameters();
        $child=$tile->getNextChildToVisit();
        if(null !== $child) {
            $depth++;
            return $this->renderTile($renderedChildren, $parameters, $depth, $child);
        }
        $renderedChildrenForLevel = array();
        for($i=0; $i<$tile->getAmountOfChildren();$i++) {
            $renderedChildrenForLevel[] = $renderedChildren->pop();
        }
        $renderedChildren->push($this->resolve($tile, $parameters, $renderedChildrenForLevel, $depth));
        $depth--;
        return $this->renderTile($renderedChildren, $parameters, $depth, $tile->getParent());

    }

    protected function resolve(AbstractTile $tile, $parameters, $renderedChildren,$depth) {
        return new ResolvedTile(
            $this->constructViewIdentifiers($tile),
            $this->engine->render($tile->getTemplateName(), $this->flattenParameters($parameters, $renderedChildren, $depth))
        );
    }

    protected function flattenParameters($parameters, $renderedChildren, $depth) {
        $viewParameters = array();
        for($i=0;$i<=$depth;$i++)  {
            foreach($parameters[$i] as $key=>$value) {
                $viewParameters[$key]=$value;
            }
        }
        foreach($renderedChildren as $resolvedChild) {
//            foreach($child as $key=>$value) {
            if(!isset($viewParameters['children'])){$viewParameters['children']=array();}
            $viewParameters['children'][]=$resolvedChild;
//            }
        }
        return $viewParameters;
    }

    protected function constructViewIdentifiers(AbstractTile $tile) {
        $identifiers = $tile->getViewIdentifiers();
        $identifiers[] = $tile->getName();
        return $identifiers;
    }

    public function setTemplateEngine($engine) {
        $this->engine = $engine;
    }






} 