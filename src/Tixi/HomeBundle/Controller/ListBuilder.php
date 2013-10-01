<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martin jonasse
 * Date: 01.10.13
 * Time: 07:29
 * initial file
 */

namespace Tixi\HomeBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;

class ListBuilder
{ /*
   * class for displaying a view as a html table on the screen,
   * the user can select rows and or filter these
   * inputs: the view and constraints, all other inputs are stored in the session
   * feature: the number of rows displayed is limited to 30
   */
    // inputs
    protected $view;
    protected $constraints;
    // output
    protected $list;

    public function setView($value) {
        $this->view = $value;
        $this->list = array();
    }

    public function setConstraints($value) {
        $this->constraints = $value;
        $this->list = array();
    }

    public function makeList()
    { /*
       * make a list (array) with data (headers, values) from the customer database
       * the array can have keys (not mandatory) or not
       */
        $this->list = array();
//        $this->list[] = array(
//            "col1" => "col1",
//            "col2" => "col2",
//            "col3" => "col3"
//        );
//        $this->list[] = array(
//            "col1" => "abc",
//            "col2" => "def",
//            "col3" => "0"
//        );
        $this->list[] = array( "col1", "col2", "col3");
        $this->list[] = array( "def", "xyz", "1");
    }

    public function getList() {
        return $this->list; // test version
    }
}