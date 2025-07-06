<?php

namespace App\Services;

use App\DTO\CarDTO;
use App\Entity\Car;
use App\Repository\CarRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarService
{
    public function __construct(private CarRepository $carRepository)
    {
    }

    public function showCar($idCar): Car
    {
        if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('O parâmetro idCar precisa ser um número inteiro válido!');
        }

        $idCar = (int) $idCar;
        $car = $this->carRepository->find($idCar);

        if (!$car) {
            throw new NotFoundHttpException('Carro NÃO encontrado!');
        }

        return $car;
    }

    public function createCarFromArray(array $data): Car
    {
        $dto = new CarDTO(
            brand: $data['brand'] ?? '',
            model: $data['model'] ?? '',
            manufacture_year: $data['manufacture_year'] ?? 0,
            model_year: $data['model_year'] ?? 0,
            km: $data['km'] ?? 0
        );

        $errors = $dto->validate();

        if (!empty($errors)) {
            throw new InvalidArgumentException(json_encode($errors));
        }

        return $this->createCar($dto);
    }

    private function createCar(CarDTO $dto): Car
    {
        $car = new Car();
        $car->setBrand($dto->brand);
        $car->setModel($dto->model);
        $car->setManufactureYear($dto->manufacture_year);
        $car->setModelYear($dto->model_year);
        $car->setKm($dto->km);
        $car->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));
        $car->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));

        $this->carRepository->add($car, true);

        return $car;
    }

    public function deleteCar($idCar)
    {
        if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('O parâmetro idCar precisa ser um número inteiro válido!');
        }

        $idCar = (int) $idCar;
        $car = $this->carRepository->find($idCar);

        if (!$car) {
            throw new NotFoundHttpException('Carro NÃO encontrado!');
        }

        $this->carRepository->remove($car, true);
    }

    public function updateCar($idCar, array $data, string $method): Car
    {
        if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('O parâmetro idCar precisa ser um número inteiro válido!');
        }

        $idCar = (int) $idCar;
        $car = $this->carRepository->find($idCar);

        if (!$car) {
            throw new NotFoundHttpException('Carro não encontrado!');
        }

        $dto = new CarDTO(
            brand: $data['brand'] ?? '',
            model: $data['model'] ?? '',
            manufacture_year: $data['manufacture_year'] ?? 0,
            model_year: $data['model_year'] ?? 0,
            km: $data['km'] ?? 0
        );

        if ($method === 'PUT') {
            $errors = $dto->validateAll();
            if (!empty($errors)) {
                throw new InvalidArgumentException(json_encode($errors));
            }
        } else {
            $errors = $dto->validatePartial($data);
            if (!empty($errors)) {
                throw new InvalidArgumentException(json_encode($errors));
            }
        }

        $fieldMap = [
            'brand' => fn($value) => $car->setBrand($value),
            'model' => fn($value) => $car->setModel($value),
            'manufacture_year' => fn($value) => $car->setManufactureYear((int) $value),
            'model_year' => fn($value) => $car->setModelYear((int) $value),
            'km' => fn($value) => $car->setKm((float) $value),
        ];

        foreach ($fieldMap as $field => $setter) {
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $setter($data[$field]);
            }
        }

        $car->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));
        $this->carRepository->add($car, true);

        return $car;
    }

}

?>