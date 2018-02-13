<?php

namespace SkyHub\Api\EntityInterface\Catalog\Product;

use SkyHub\Api\DataTransformer\Catalog\Product\Attribute\Create;
use SkyHub\Api\EntityInterface\EntityAbstract;
use SkyHub\Api\Handler\Response\HandlerDefault,
    SkyHub\Api\Handler\Response\HandlerException;

class Attribute extends EntityAbstract
{

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->setData('code', (string) $code);
        return $this;
    }


    /**
     * @return string
     */
    public function getCode()
    {
        return (string) $this->getData('code');
    }


    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->setData('label', $label);
        return $this;
    }


    /**
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->getData('label');
    }


    /**
     * @param string|integer $value
     *
     * @return $this
     */
    public function addOption($value)
    {
        $options   = (array) $this->getOptions();
        $options[] = $value;

        $this->setData('options', $options);

        return $this;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return (array) $this->getData('options');
    }


    /**
     * @return array|bool|mixed|string
     */
    public function export()
    {
        return (array) [
            'attribute' => (array) $this->getData()
        ];
    }


    /**
     * @return HandlerDefault|HandlerException
     */
    public function create()
    {
        $this->validate();
    
        /** @var \SkyHub\Api\Handler\Request\Catalog\Product\AttributeHandler $handler */
        $handler = $this->requestHandler();
        
        /** @var HandlerDefault|HandlerException $response */
        $response = $handler->create($this->getCode(), $this->getLabel(), $this->getOptions());

        return $response;
    }


    /**
     * @return HandlerDefault|HandlerException
     */
    public function update()
    {
        $this->validate();

        /** @var \SkyHub\Api\Handler\Request\Catalog\Product\AttributeHandler $handler */
        $handler = $this->requestHandler();
        
        /** @var HandlerDefault|HandlerException $response */
        $response = $handler->update($this->getCode(), $this->getLabel(), $this->getOptions());

        return $response;
    }
}