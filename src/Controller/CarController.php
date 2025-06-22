<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use DateTimeZone;
use Exception;
// use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarController extends AbstractController
{
    #[Route('/cars', name: 'car_list', methods: ['GET'])]
    public function index(CarRepository $carRepository): JsonResponse
    {
        $cars = $carRepository->findAll();

        return $this->json([
            'success' => true,
            'message' => 'todos os carros diponíveis',
            'carros' => $cars,
        ], 200);
    }

    #[Route('/cars/{idCar}', name: 'car_show', methods: ['GET'])]
    public function show($idCar, CarRepository $carRepository): JsonResponse
    {
        try {

            if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
                return $this->json([
                    'success' => false,
                    'message' => 'O parâmetro idCar precisa ser um número inteiro válido!',
                ], 400);
            }

            $idCar = (int) $idCar;
            $car = $carRepository->find($idCar);

            if (!$car) {
                return $this->json([
                    'success' => false,
                    'message' => 'Carro NÃO encontrado!',
                ], 404);
            }

            return $this->json([
                'success' => true,
                'message' => 'Carro encontrado!',
                'carros' => $car,
            ], 200);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro ao encontrar Carro!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    #[Route('/car', name: 'car_create', methods: ['POST'])]
    public function create(Request $request, CarRepository $carRepository): JsonResponse
    {

        try {

            if ($request->headers->get('Content-Type') == 'application/json') {
                // $data = json_decode($request->getContent(), true);
                $data = $request->toArray();
            } else {
                $data = $request->request->all();
            }

            $requiredFields = ['brand', 'model', 'manufacture_year', 'model_year', 'km'];
            $camposNulos = [];

            foreach ($requiredFields as $campo) {
                if (empty($data[$campo])) {
                    $camposNulos[] = $campo;
                }
            }

            if (!empty($camposNulos)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Os seguintes campos devem ser preenchidos',
                    'campos' => $camposNulos
                ], 400);
            }

            if (
                !is_int($data['manufacture_year'])
                ||
                !is_int($data['model_year'])
                ||
                !is_float($data['km'])
            ) {
                return $this->json([
                    'success' => false,
                    'message' => 'Os campos relacionados ao ano de fabricação/modelo/km devem ser números válidos ',
                ], 400);
            }

            $car = new Car();

            $car->setBrand($data['brand']);
            $car->setModel($data['model']);
            $car->setManufactureYear($data['manufacture_year']);
            $car->setModelYear($data['model_year']);
            $car->setKm($data['km']);
            $car->setCreatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));
            $car->setUpdatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));

            $carRepository->add($car, true);

            return $this->json([
                'success' => true,
                'message' => 'Carro criado com Sucesso!',
                'car' => $car
            ], 201);

        } catch (Exception $e) {

            return $this->json([
                'success' => false,
                'message' => 'Erro ao criar Carro!',
                'error' => $e->getMessage()
            ], 500);
        }

    }


    #[Route('/cars/{idCar}', name: 'car_update', methods: ['PUT', 'PATCH'])]
    public function update(
        $idCar,
        Request $request,
        ManagerRegistry $doctrine,
        CarRepository $carRepository
    ): JsonResponse {

        try {

            if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
                return $this->json([
                    'success' => false,
                    'message' => 'O parâmetro idCar precisa ser um número inteiro válido!',
                ], 400);
            }

            $idCar = (int) $idCar;
            $car = $carRepository->find($idCar);

            if (!$car) {
                return $this->json([
                    'success' => false,
                    'message' => 'Carro não encontrado!',
                ], 404);
            }

            // método HTTP
            $method = $request->getMethod(); // PUT/PATCH 

            if ($request->headers->get('Content-Type') == 'application/json') {
                // $data = json_decode($request->getContent(), true);
                $data = $request->toArray();
            } else {
                $data = $request->request->all();
            }

            if ($method === 'PUT') {

                $requiredFields = ['brand', 'model', 'manufacture_year', 'model_year', 'km'];
                $missingFields = [];

                foreach ($requiredFields as $field) {
                    if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                        $missingFields[] = $field;
                    }
                }

                if (!empty($missingFields)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Os seguintes campos devem ser preenchidos',
                        'missing_fields' => $missingFields,
                    ], 400);
                }
            }

            // Mapeamento de campos => setters
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

            $car->setUpdatedAt(new \DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')));
            $doctrine->getManager()->flush();

            return $this->json([
                'success' => true,
                'message' => 'Informações do Carro atualizadas com sucesso!',
                'car' => $car
            ], 200);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro ao atualizar Carro!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/cars/{idCar}', name: 'car_delete', methods: ['DELETE'])]
    public function delete($idCar, CarRepository $carRepository): JsonResponse
    {
        try {

            if (!filter_var($idCar, FILTER_VALIDATE_INT)) {
                return $this->json([
                    'success' => false,
                    'message' => 'O parâmetro idCar precisa ser um número inteiro válido!',
                ], 400);
            }

            $idCar = (int) $idCar;
            $car = $carRepository->find($idCar);

            if (!$car) {
                return $this->json([
                    'success' => false,
                    'message' => 'Carro NÃO encontrado!',
                ], 404);
            }

            $carRepository->remove($car, true);

            return $this->json([
                'success' => true,
                'message' => 'Carro deletado!',
            ], 200);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro ao deletar Carro!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
