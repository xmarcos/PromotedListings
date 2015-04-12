<?php

namespace PromotedListings\Entity;

use xmarcos\Dot\Container as DotContainer;

abstract class Entity
{
    protected $entity_data;

    protected function setEntityData($data)
    {
        $this->entity_data = $data instanceof DotContainer
            ? $data
            : DotContainer::create($data);
    }

    protected function getEntityData()
    {
        return $this->entity_data instanceof DotContainer
            ? $this->entity_data
            : DotContainer::create();
    }

    public function get($key, $value = null)
    {
        return $this->entity_data->get($key, $default);
    }

    public function has($key)
    {
        return $this->entity_data->has($key);
    }

    abstract public function toArray();

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
