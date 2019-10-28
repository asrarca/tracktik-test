<?php
/**
* @author <asrar.ca@gmail.com>
* @since 2019-10-27
* @version 1.0
*/

namespace Tracktik;

class ElectronicItems {

    private $items = array();

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
    * Returns the items sorted
    *
    * @return array
    */
    public function getSortedItems($order = 'asc')
    {
        $sorted = array();
        foreach ( $this->items as $item ) {
            $sorted[($item->getPrice() * 100)] = $item;
        }
        ksort($sorted, SORT_NUMERIC);
        return ($order == 'asc') ? $sorted : array_reverse($sorted);
    }

    /**
    *
    * @param string $type
    * @return array
    */
    public function getItemsByType( $type )
    {
        if ( in_array($type, ElectronicItem::$types) ) {
            $callback = function ($item) use ($type) {
                return $item->getType() == $type;
            };
            $items = array_filter($this->items, $callback);
        }
        return false;
    }

    /**
    * @return array
    */
    public function getItems()
    {
        return $this->items;
    }

    /**
    * Get the total price of all items in collection.
    *
    * @return float
    */
    public function getTotal()
    {
        $total = 0;
        foreach($this->getItems() as $item) {
            $total += $item->getTotalPrice();
        }
        return $total;
    }


    /**
    * Returns a plain-text representation of the cart for display purposes.
    * @param $options array
    *
    * Supported options:
    *  'detailed' => whether or not to show the extra details
    *  'width' => how wide to display the table
    */
    public function printLines($options = [])
    {
        $width = isset($options['width']) && is_numeric($options['width']) ? $options['width'] : 50;

        $type_count = [];
        $rows = [];
        foreach($this->getSortedItems('desc') as $_item) {
            $rows[] = $_item->printLine($options);

            if (isset($options['detailed']) && $options['detailed'] == true) {
                foreach($_item->getExtras() as $extra) {
                    $rows[] = $extra->printLine($options);
                }
            }
        }
        $rows[] = str_repeat('-', $width);
        $rows[] = str_pad('Total', $width - 10, ' ', STR_PAD_RIGHT) . str_pad(number_format($this->getTotal(), 2), 10, ' ', STR_PAD_LEFT);
        return implode("\n", $rows);
    }
}


