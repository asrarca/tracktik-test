<?php
/**
* @author <asrar.ca@gmail.com>
* @since 2019-10-27
* @version 1.0
*/
namespace Tracktik;

class ControllerWireless extends Controller {
    protected $wired = 0;

    protected $price = 4.00;

    public function getName()
    {
        return parent::getName() .' (wireless)';
    }

}
