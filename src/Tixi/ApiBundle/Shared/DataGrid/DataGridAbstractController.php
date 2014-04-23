<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 21:42
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tixi\CoreDomain\Shared\FastGenericEntityAccessorRepository;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * Class DataGridAbstractController
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
abstract class DataGridAbstractController extends ContainerAware{

    protected $router;
    protected $fgeaRepository;
    protected $routeProperties;
    private $isEmbedded;

    /**
     * @param ContainerInterface $container
     * @param bool $embeddedState
     * @param array $routeProperties
     */
    public function __construct(ContainerInterface $container, $embeddedState=false, array $routeProperties=array()) {
        $this->setContainer($container);
        $this->router = $container->get('router');
        $this->fgeaRepository = $container->get('tixi_coredomain.fgea_repository');
        $this->isEmbedded = $embeddedState;
        $this->routeProperties = $routeProperties;
    }

    /**
     * @return string
     */
    public function createDataGridJsConf() {
        $embedded = $this->isInEmbeddedState() ? 'true' : 'false';
        $emptyDataText = $this->container->get('translator')->trans('datagrid.empty.defaulttext');
        return 'conf = {
                    "'.$this->getGridIdentifier().'": {
                        "dblClickCallback": function (rowId) {
                            var _url = "'.$this->getDblClickPath().'";
                                _url = _url.replace("__replaceId__", rowId);
                                window.location = _url;
                        },
                        "isEmbedded": '.$embedded.',
                        "trans": {
                            "emptyDefaultText": "'.$emptyDataText.'"
                        }
                    }
                }';
    }

    /**
     * @param GenericEntityFilter $filter
     * @return int
     */
    public function getTotalAmountOfRowsByFgeaFilter(GenericEntityFilter $filter) {
        return $this->fgeaRepository->findTotalAmountByFilter($filter);
    }

    /**
     * @return bool
     */
    public function isInEmbeddedState() {
        return $this->isEmbedded;
    }

    /**
     * @return mixed
     */
    public abstract function getGridIdentifier();

    /**
     * @return mixed
     */
    public abstract function createCustomControlTile();

    /**
     * @return mixed
     */
    public abstract function getDblClickPath();

    /**
     * @return mixed
     */
    public abstract function getReferenceDTO();

    /**
     * @param GenericEntityFilter $filter
     * @return mixed
     */
    public abstract function constructDtosFromFgeaFilter(GenericEntityFilter $filter);

    /**
     * @return mixed
     */
    public abstract function getDataSrcUrl();

    /**
     * @param GenericEntityFilter $filter
     * @return array
     */
    protected function getEntitiesByFgeaFilter(GenericEntityFilter $filter) {
        return $this->fgeaRepository->findByFilter($filter);
    }

    /**
     * @param $route
     * @param array $parameters
     * @return string
     */
    protected function generateUrl($route, $parameters = array()) {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }
} 