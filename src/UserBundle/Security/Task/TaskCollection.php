<?php

namespace Rabble\UserBundle\Security\Task;

use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

class TaskCollection extends ArrayCollection
{
    public function __construct(array $elements = [])
    {
        $array = [];
        foreach ($elements as $element) {
            Assert::isInstanceOf($element, TaskBundle::class);
            $array[$element->getName()] = $element;
        }
        parent::__construct($array);
    }

    /**
     * @param TaskBundle $element
     *
     * @return bool|true
     */
    public function add($element)
    {
        Assert::isInstanceOf($element, TaskBundle::class);
        parent::set($element->getName(), $element);

        return true;
    }

    /**
     * @param int|string $key
     * @param TaskBundle $value
     */
    public function set($key, $value)
    {
        Assert::isInstanceOf($value, TaskBundle::class);
        Assert::eq($key, $value->getName(), 'The key has to be equal to %2$s.');
        parent::set($key, $value);
    }
}
