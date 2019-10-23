<?php

namespace App\Handlers;
use JWT;

class Response {
  private function responseJson($success, $statusCode, $message, $result) {
    $jsonData = [
      'status' => [
        'success' => $success,
        'code' => $statusCode,
        'message' => $message
      ],
      'result' => $result
    ];
    return response()->json($jsonData, $statusCode);
  }
  public function success($data) {
    return $this->responseJson(true, 200, 'OK', $data);
  }
  public function notValidInput($data) {
    return $this->responseJson(false, 400, 'Invalid Input', $data);
  }
  public function forbidden($message) {
      return $this->responseJson(false, 401, $message, $message);
  }
  public function error($message) {
      return $this->responseJson(false, 400, $message, null);
  }
  public function notFound() {
    return $this->responseJson(false, 404, 'Route Not Found', 'Route Not Found');
  }
  public function sendToken($user) {
    $payload = [
      'iss' => 'Balita Sehat',
      'iat' => time(),
      'sub' => $user
    ];
    $token = JWT::encode($payload, env('JWT_SECRET'));
    return $this->success(['token' => $token]);
  }
}
