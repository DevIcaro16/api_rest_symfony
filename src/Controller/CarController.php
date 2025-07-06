<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Services\CarService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function show($idCar, CarRepository $carRepository, CarService $carService): JsonResponse
    {
        try {

            $car = $carService->showCar($idCar);

            return $this->json([
                'success' => true,
                'carro' => $car,
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro interno ao buscar carro!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/car', name: 'car_create', methods: ['POST'])]
    public function create(
        Request $request,
        CarService $carService
    ): JsonResponse {

        try {

            $data = $request->headers->get('Content-Type') === 'application/json'
                ? $request->toArray()
                : $request->request->all();

            $car = $carService->createCarFromArray($data);

            return $this->json([
                'success' => true,
                'message' => 'Carro criado com Sucesso!',
                'car' => $car
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => json_decode($e->getMessage(), true)
            ], 400);

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
        CarService $carService
    ): JsonResponse {

        try {

            $method = $request->getMethod(); // PUT/PATCH 

            $data = $request->headers->get('Content-Type') === 'application/json'
                ? $request->toArray()
                : $request->request->all();

            $car = $carService->updateCar($idCar, $data, $method);

            return $this->json([
                'success' => true,
                'message' => 'Informações do Carro atualizadas com sucesso!',
                'car' => $car
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => json_decode($e->getMessage(), true)
            ], 400);

        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro interno ao atualizar carro!',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    #[Route('/cars/{idCar}', name: 'car_delete', methods: ['DELETE'])]
    public function delete($idCar, CarService $carService): JsonResponse
    {

        try {

            $carService->deleteCar($idCar);

            return $this->json([
                'success' => true,
                'message' => 'Carro deletado!',
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erro interno ao deletar carro!',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}