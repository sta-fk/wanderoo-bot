<?php

namespace App\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseApiController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly SerializerInterface $serializer,
    ) {
    }

    protected function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }
}
