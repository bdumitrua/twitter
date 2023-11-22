<?php

namespace App\Http\Controllers;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function responseToJSON($response): JsonResponse
    {
        try {
            if (isset($response['error'])) {
                return response()->json(['error' => $response['error']], $response['code']);
            }

            if (isset($response['message'])) {
                return response()->json(['message' => $response['message']], $response['code']);
            }

            if ($response) {
                return response()->json($response, 200);
            }

            return response()->json(["message" => "success"], 200);
        } catch (\Throwable $error) {
            return response()->json(['error' => $error['error']], 500);
        }
    }

    protected function handleServiceCall(callable $serviceFunction): JsonResponse
    {
        try {
            $response = $serviceFunction();
            return $this->responseToJSON($response);
        } catch (HttpException $exception) {
            return $this->responseToJSON([
                'error' => $exception->getMessage(),
                'code' => $exception->getStatusCode()
            ]);
        } catch (\Throwable $exception) {
            return $this->responseToJSON([
                'error' => $exception->getMessage(),
                'code' => Response::HTTP_BAD_GATEWAY
            ]);
        }
    }

    public function handleKafka()
    {
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
            ],
        ]);

        $context = $connectionFactory->createContext();
        $topic = $context->createTopic('user_created');
        $message = $context->createMessage(json_encode([
            "name" => "username",
            "email" => "user email",
        ]));

        $context->createProducer()->send($topic, $message);
    }
}
