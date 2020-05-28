<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponseBuilder
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    public function __construct(
        SerializerInterface $serializer,
        EndpointResponseNamer $endpointResponseNamer
    ) {
        $this->serializer = $serializer;
        $this->endpointResponseNamer = $endpointResponseNamer;
    }

    public function buildResponseForGetOneRequest(
        string $object,
        array $values,
        int $status,
        Request $request
    ): Response {
        $contentTypes = $request->getAcceptableContentTypes();
        if (in_array('application/vnd.api+json', $contentTypes)) {
            return $this->buildJsonApiResponse($values, $status, $request->query->get('include'), true);
        } else {
            return $this->buildJsonResponse(
                [ $this->endpointResponseNamer->getPluralName($object) => $values],
                $status
            );
        }
    }

    public function buildResponseForGetAllRequest(
        string $object,
        array $values,
        int $status,
        Request $request
    ): Response {
        $contentTypes = $request->getAcceptableContentTypes();
        if (in_array('application/vnd.api+json', $contentTypes)) {
            return $this->buildJsonApiResponse($values, $status, $request->query->get('include'), false);
        } else {
            return $this->buildJsonResponse(
                [ $this->endpointResponseNamer->getPluralName($object) => $values],
                $status
            );
        }
    }

    public function buildResponseForPostRequest(
        string $object,
        array $values,
        int $status,
        Request $request
    ): Response {
        $contentTypes = $request->getAcceptableContentTypes();
        if (in_array('application/vnd.api+json', $contentTypes)) {
            return $this->buildJsonApiResponse($values, $status, $request->query->get('include'), false);
        } else {
            return $this->buildJsonResponse(
                [ $this->endpointResponseNamer->getPluralName($object) => $values],
                $status
            );
        }
    }

    public function buildResponseForPutRequest(
        string $object,
        object $value,
        int $status,
        Request $request
    ): Response {
        $contentTypes = $request->getAcceptableContentTypes();
        if (in_array('application/vnd.api+json', $contentTypes)) {
            return $this->buildJsonApiResponse($value, $status, $request->query->get('include'), true);
        } else {
            return $this->buildJsonResponse(
                [ $this->endpointResponseNamer->getSingularName($object) => $value],
                $status
            );
        }
    }

    /**
     * Create a response for our classic JSON api
     */
    protected function buildJsonResponse($data, int $status): Response
    {
        return new Response(
            $this->serializer->serialize(
                $data,
                'json'
            ),
            $status,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * Create a response for JSON:API api
     */
    protected function buildJsonApiResponse($data, int $status, ?string $include, bool $singleItem): Response
    {
        $json = $this->serializer->serialize($data, 'json-api', [
            'sideLoadFields' => $this->extractJsonApiSideLoadFields($include),
            'singleItem' => $singleItem
        ]);
        return new Response(
            $json,
            $status,
            ['Content-type' => 'application/vnd.api+json']
        );
    }

    public function extractJsonApiSideLoadFields(?string $include): array
    {
        if (!$include) {
            return [];
        }
        $fields = explode(',', html_entity_decode($include));
        $dotToTree = function (string $str) use (&$dotToTree) {
            if ($str) {
                $parts = explode('.', $str);
                $key = array_shift($parts);
                return [ $key => $dotToTree(implode('.', $parts))];
            }

            return [];
        };
        return array_reduce(
            array_map($dotToTree, $fields),
            function (array $carry, array $tree) {
                $key = array_key_first($tree);
                if (!array_key_exists($key, $carry)) {
                    $carry[$key] = [];
                }
                $carry[$key] = array_merge($carry[$key], $tree[$key]);

                return $carry;
            },
            []
        );
    }
}