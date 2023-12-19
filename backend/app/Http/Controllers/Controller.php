<?php

namespace App\Http\Controllers;

use App\Kafka\KafkaProducer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param mixed $response
     * 
     * @return JsonResponse
     */
    private function responseToJSON($response): JsonResponse
    {
        try {
            if (empty($response)) {
                return response()->json(null, 200);
            }

            if ($response instanceof JsonResource) {
                return response()->json($response);
            }

            if ($response instanceof Response) {
                $code = $response->getStatusCode();
                $content = empty($response->getContent()) ? null : $response->getContent();

                return response()->json($content, $code);
            }

            return response()->json(null, 200);
        } catch (\Exception $error) {
            return $this->responseToError($error->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * @param string $message
     * @param int $code
     * 
     * @return JsonResponse
     */
    private function responseToError(string $message, int $code): JsonResponse
    {
        return response()->json(
            ['error' => $message],
            $code
        );
    }

    /**
     * @param callable $serviceFunction
     * 
     * @return JsonResponse
     */
    protected function handleServiceCall(callable $serviceFunction): JsonResponse
    {
        try {
            $response = $serviceFunction();
            return $this->responseToJSON($response);
        } catch (HttpException $exception) {
            return $this->responseToError(
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        } catch (\Exception $exception) {
            return $this->responseToError(
                $exception->getMessage(),
                $exception->getCode()
            );
        } catch (\Throwable $exception) {
            return $this->responseToError(
                $exception->getMessage(),
                Response::HTTP_BAD_GATEWAY
            );
        }
    }
}
