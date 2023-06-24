<?php

namespace App\Service;

use App\Exception\BadParamsException;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DtoConverter
{
    private SymfonySerializer $serializer;

    private ValidatorInterface $validator;

    public function __construct()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        $propertyNormalizer = new PropertyNormalizer();
        $arrayDenormalized = new ArrayDenormalizer();
        $objectNormalizer = new ObjectNormalizer(null, null, $propertyAccessor, new ReflectionExtractor());

        $this->serializer = new SymfonySerializer([$arrayDenormalized, $objectNormalizer, $propertyNormalizer], [new JsonEncoder()]);

        $this->validator = Validation::createValidatorBuilder()
            ->setDoctrineAnnotationReader(new AnnotationReader())
            ->enableAnnotationMapping()
            ->getValidator();
    }

    public function convertResponseToDto(array|object $data, string $toDtoClass, bool $isCollection = true, array $context = []): object|array
    {
        $arrayEntity = $this->serializer->normalize($data, null, $context);

        $toDtoClass = $isCollection ? $toDtoClass . '[]' : $toDtoClass;

        return $this->serializer->denormalize($arrayEntity, $toDtoClass);
    }

    public function convertRequestToDto(string $dtoClass, Request $request): object
    {
        $requestBody = json_decode($request->getContent(), true);
        $requestData = array_merge(
            $request->query->all(),
            $request->request->all(),
            is_array($requestBody) ? $requestBody : []
        );

        try {
            $reflectionClass = new \ReflectionClass($dtoClass);
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage());
        }

        $errors = [];
        foreach ($reflectionClass->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $requestData)) {
                $requestData[$property->getName()] = null;
            }
            $viol = $this->validator->validatePropertyValue($dtoClass, $property->getName(), $requestData[$property->getName()]);
            for ($i = 0; $i < count($viol); $i++) {
                $errors[$property->getName()] = $viol[$i]->getMessage();
            }
        }

        if (count($errors) > 0) {
            throw new BadParamsException($errors);
        }

        $response = $this->serializer->denormalize($requestData, $dtoClass, null,
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
        );

        return $response;
    }
}