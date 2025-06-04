<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Attribute\Ignore;

interface SerializableResponseDTOInterface
{
    /**
     * @return array<string>
     */
    #[Ignore]
    public function getSerializationGroups(): array;
}
