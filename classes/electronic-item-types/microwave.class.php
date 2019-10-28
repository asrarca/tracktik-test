<?php
/**
* @author <asrar.ca@gmail.com>
* @since 2019-10-27
* @version 1.0
*/
namespace Tracktik;

class Microwave extends ElectronicItem {
    protected $price = 89.99;

    protected function getMaxExtras()
    {
        return 0;
    }
}
