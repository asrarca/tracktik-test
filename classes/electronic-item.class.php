<?php
/**
* @author <asrar.ca@gmail.com>
* @since 2019-10-27
* @version 1.0
*/

namespace Tracktik;

abstract class ElectronicItem {

    /**
    * @var float
    */
    protected $price;

    /**
    * @var string
    */
    private $type;

    /**
    * @var array List of extras applied to item
    */
    private $extras = [];

    /**
    * @var bool
    */
    private $wired = 1;

    private $is_extra = false;

    const ELECTRONIC_ITEM_TELEVISION = 'television';
    const ELECTRONIC_ITEM_CONSOLE = 'console';
    const ELECTRONIC_ITEM_MICROWAVE = 'microwave';
    const ELECTRONIC_ITEM_CONTROLLER = 'controller';

    private static $types = array(
        self::ELECTRONIC_ITEM_CONSOLE,
        self::ELECTRONIC_ITEM_MICROWAVE,
        self::ELECTRONIC_ITEM_TELEVISION,
        self::ELECTRONIC_ITEM_CONTROLLER,
    );

    /**
    * Optionally set any properties upon instantiation.
    */
    public function __construct($options = [])
    {
        foreach($options as $property => $property_value) {
            if (property_exists($this, $property)) {
                $this->$property = $property_value;
            }
        }
    }

    /**
    * Determine if an item can have extras or not
    * @return bool
    */
    public function canHaveExtras()
    {
        return $this->getMaxExtras() !== 0;
    }

    /**
    * Get the maximum number of extras an item can have.
    * Must be implemented in child class.
    *
    * @return mixed
    */
    abstract protected function getMaxExtras();

    /**
    * Converts and camelCase variable to an under_scored variable
    * or vice versa.
    *
    * @param $string the string to inflect
    * @param $style how to infect it (underscore (default) | camel | class)
    *
    * @return string
    */
    private static function inflect($string, $style = 'underscore')
    {
        if ($style == 'underscore') {
            $string = preg_split('/(?=[A-Z])/', $string);
            $string = implode('_', array_map('strtolower', $string));
        }
        else {
            $string = implode('', array_map('ucfirst', explode('_', $string)));
            if ($style === 'camel') {
                $string = lcfirst($string);
            }
        }
        return $string;
    }

    /**
    * Universal setter and getter allows us to avoid adding
    * setters and getters for each property.
    *
    * camelCase methods will translate to under_score properties.
    *
    * Example Usage:
    * setPrice(3); // will set $this->price to 3
    *
    * An error will be thrown when attempting to set or get a
    * property that doesn't exist.
    *
    * Note: Custom setters and getters can be created to override this method.
    */
    public function __call($name, $arguments = [])
    {
        if (preg_match("/(s|g)et[A-Z](.)/", $name)) {
            // method requested is a setter or getter
            $property = preg_split('/(?=[A-Z])/', $name);
            $set_or_get = array_shift($property);
            $property = implode('_', array_map('strtolower', $property));
            if (property_exists($this, $property)) {
                if ($set_or_get === 'set') {
                    $this->$property = empty($arguments) ? null : $arguments[0];
                    return true;
                }
                else {
                    return $this->$property;
                }
            }
        }
        throw new \Exception('Property '. $name .' does not exist on '. get_class($this));
    }


    /**
    * Returns an instance of a specific type of ElectronicItem
    * object without needing to know the exact class name.
    * @param string
    * @param array options to pass upon class instantiaton
    */
    public static function factory($type_full, $options = [])
    {
        $type_parts = explode('_', $type_full);

        // determine the parent "type" of item, if we are creating a sub-class
        $type = array_shift($type_parts);

        // load and return the class
        $class_file = CLASS_BASE .'/electronic-item-types/'. $type_full .'.class.php';
        $class_name = "Tracktik\\". self::inflect($type_full, 'class');
        if (in_array(strtolower($type), self::$types) && file_exists($class_file)) {
            require_once $class_file;

            $class = new $class_name($options);
            $class->setType($type);
            return $class;
        }
    }


    /**
    * Add an ElectronicItem extra to an ElectronicItem
    * @param ElectronicItem
    */
    public function addExtra($extra)
    {
        if ($this->canHaveExtras()) {
            if (is_null($this->getMaxExtras()) || count($this->extras) < $this->getMaxExtras()) {
                if (is_subclass_of($extra, '\Tracktik\ElectronicItem')) {
                    $extra->setIsExtra(true);
                    $this->extras[] = $extra;
                }
            }
            else {
                throw new \Exception($this->type .' can have a maximum of '. $this->getMaxExtras() .' extras');
            }
        }
        else {
            throw new \Exception($this->type .' cannot have any extras!');
        }
        return true;
    }


    /**
    * Convenience method to add multiple extras at a time
    * @param array
    */
    public function addExtras(array $extras)
    {
        foreach ($extras as $item) {
            $this->addExtra($item);
        }
        return true;
    }


    /**
    * @return array
    */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
    * Return the total price of an item, including extras
    *
    * @return float
    */
    public function getTotalPrice()
    {
        $total_price = $this->getPrice();
        foreach($this->getExtras() as $_extra) {
            $total_price += $_extra->getPrice();
        }
        return $total_price;
    }

    public function getName()
    {
        return ucfirst($this->type);
    }

    public function printLine($options = [])
    {
        $width = isset($options['width']) && is_numeric($options['width']) ? $options['width'] : 50;
        $indent = $this->is_extra ? ' + ' : '';
        $price = (isset($options['grouped']) && $options['grouped']) ? $this->getTotalPrice() : $this->getPrice();
        return str_pad($indent . $this->getName(), $width - 10, ' ', STR_PAD_RIGHT) . str_pad(number_format($price, 2), 10, ' ', STR_PAD_LEFT);
    }
}
