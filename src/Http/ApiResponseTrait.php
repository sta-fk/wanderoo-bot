<?php

namespace App\Http;

use App\DTO\Response\SerializableResponseDTOInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait ApiResponseTrait
{
    abstract protected function getSerializer(): SerializerInterface;

    /** @param array<string, string> $headers */
    protected function successDTO(
        SerializableResponseDTOInterface $dto,
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        $context = [];
        if (!empty($dto->getSerializationGroups())) {
            $context['groups'] = $dto->getSerializationGroups();
        }

        /** @var Serializer $serializer */
        $serializer = $this->getSerializer();
        $data = $serializer->normalize($dto, null, $context);
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Normalized data must be an array');
        }

        return $this->success($data, $status, $headers);
    }

    /**
     * @param array<mixed> $data
     * @param array<string, string> $headers
     */
    protected function success(array $data = [], int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'data' => $data,], $status, $headers);
    }

    /** @param array<string, string> $headers */
    protected function error(string $message, int $status = 400, array $headers = []): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $message,], $status, $headers);
    }

    /** @param array<string, mixed> $errors */
    protected function validationError(array $errors, int $status = 422): JsonResponse
    {
        return new JsonResponse(['status' => 'fail', 'errors' => $errors,], $status);
    }
}
