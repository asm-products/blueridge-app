<?php

namespace Hydrators;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Hydrator\HydratorInterface;
use Doctrine\ODM\MongoDB\UnitOfWork;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ODM. DO NOT EDIT THIS FILE.
 */
class BlueridgeDocumentsUserHydrator implements HydratorInterface
{
    private $dm;
    private $unitOfWork;
    private $class;

    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $class)
    {
        $this->dm = $dm;
        $this->unitOfWork = $uow;
        $this->class = $class;
    }

    public function hydrate($document, $data, array $hints = array())
    {
        $hydratedData = array();

        /** @Field(type="id") */
        if (isset($data['_id'])) {
            $value = $data['_id'];
            $return = $value instanceof \MongoId ? (string) $value : $value;
            $this->class->reflFields['id']->setValue($document, $return);
            $hydratedData['id'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['name'])) {
            $value = $data['name'];
            $return = (string) $value;
            $this->class->reflFields['name']->setValue($document, $return);
            $hydratedData['name'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['firstName'])) {
            $value = $data['firstName'];
            $return = (string) $value;
            $this->class->reflFields['firstName']->setValue($document, $return);
            $hydratedData['firstName'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['lastName'])) {
            $value = $data['lastName'];
            $return = (string) $value;
            $this->class->reflFields['lastName']->setValue($document, $return);
            $hydratedData['lastName'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['email'])) {
            $value = $data['email'];
            $return = (string) $value;
            $this->class->reflFields['email']->setValue($document, $return);
            $hydratedData['email'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['url'])) {
            $value = $data['url'];
            $return = (string) $value;
            $this->class->reflFields['url']->setValue($document, $return);
            $hydratedData['url'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['avatar'])) {
            $value = $data['avatar'];
            $return = (string) $value;
            $this->class->reflFields['avatar']->setValue($document, $return);
            $hydratedData['avatar'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['key'])) {
            $value = $data['key'];
            $return = (string) $value;
            $this->class->reflFields['key']->setValue($document, $return);
            $hydratedData['key'] = $return;
        }

        /** @Field(type="hash") */
        if (isset($data['roles'])) {
            $value = $data['roles'];
            $return = $value;
            $this->class->reflFields['roles']->setValue($document, $return);
            $hydratedData['roles'] = $return;
        }

        /** @Field(type="hash") */
        if (isset($data['profile'])) {
            $value = $data['profile'];
            $return = $value;
            $this->class->reflFields['profile']->setValue($document, $return);
            $hydratedData['profile'] = $return;
        }

        /** @Field(type="hash") */
        if (isset($data['projects'])) {
            $value = $data['projects'];
            $return = $value;
            $this->class->reflFields['projects']->setValue($document, $return);
            $hydratedData['projects'] = $return;
        }

        /** @Field(type="hash") */
        if (isset($data['subscription'])) {
            $value = $data['subscription'];
            $return = $value;
            $this->class->reflFields['subscription']->setValue($document, $return);
            $hydratedData['subscription'] = $return;
        }

        /** @Field(type="hash") */
        if (isset($data['providers'])) {
            $value = $data['providers'];
            $return = $value;
            $this->class->reflFields['providers']->setValue($document, $return);
            $hydratedData['providers'] = $return;
        }
        return $hydratedData;
    }
}