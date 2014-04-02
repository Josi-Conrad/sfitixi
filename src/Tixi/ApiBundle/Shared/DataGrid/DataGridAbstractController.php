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

abstract class DataGridAbstractController extends ContainerAware{

    protected $router;
    protected $fgeaRepository;
    protected $routeProperties;
    private $isEmbedded;

    public function __construct(ContainerInterface $container, $embeddedState=false, array $routeProperties) {
        $this->setContainer($container);
        $this->router = $container->get('router');
        $this->fgeaRepository = $container->get('tixi_coredomain.fgea_repository');
        $this->isEmbedded = $embeddedState;
        $this->routeProperties = $routeProperties;
    }

    public function createDataGridJsConf() {
        $embedded = $this->isInEmbeddedState() ? 'true' : 'false';
        return 'conf = {
                    "'.$this->getGridIdentifier().'": {
                        "dblClickCallback": function (rowId) {
                            var _url = "'.$this->getDblClickPath().'";
                                _url = _url.replace("__replaceId__", rowId);
                                window.location = _url;
                        },
                        "isEmbedded": '.$embedded.'
                    }
                }';
    }

    public function getTotalAmountOfRowsByFgeaFilter(GenericEntityFilter $filter) {
        return $this->fgeaRepository->findTotalAmountByFilter($filter);
    }

    public function isInEmbeddedState() {
        return $this->isEmbedded;
    }

    public abstract function getGridIdentifier();

    public abstract function getGridDisplayTitel();

    public abstract function createCustomControlTile();

    public abstract function getDblClickPath();

    public abstract function getReferenceDTO();

    public abstract function constructDtosFromFgeaFilter(GenericEntityFilter $filter);

    public abstract function getDataSrcUrl();


    protected function getEntitiesByFgeaFilter(GenericEntityFilter $filter) {
        return $this->fgeaRepository->findByFilter($filter);
    }

    protected function generateUrl($route, $parameters = array()) {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }
} 