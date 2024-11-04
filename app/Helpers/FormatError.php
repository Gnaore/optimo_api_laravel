<?php

use SimpleSoftwareIO\QrCode\Facades\QrCode;

function validationFormatErrors($validator){

  $errors = $validator->getMessageBag()->toArray();
  $formattedErrors = [];

  foreach ($errors as $field => $messages) {
    foreach ($messages as $message) {
      $formattedErrors[$field] = $message;
      break;
    }
  }

  return $formattedErrors;
}

function generateUid($prefix = "INV"){

  $firstFourCharacters = substr($prefix, 0, 4);
  $uid = strtolower($firstFourCharacters);
  $value = uniqid($uid);
  return $value;
}

function image_url($url){

  $path = explode('uploads', $url);

  if(!isset($path) || (isset($path) && !isset($path[1]))){
    return "";
  }

  $base_url = env("APP_URL");
  if($base_url === "http://localhost:8000"){
    $base_url = $base_url.'/uploads';
  }
  else{
    $base_url = $base_url.'/public/uploads';
  }

  return $base_url.$path[1];
}

function qrcode($address){

  $qrCodeImage = Qrcode::encoding("UTF-8")
    ->color(8, 114, 145)
    ->backgroundColor(10, 255, 10)
    ->size(150)
    ->generate($address);

  return $qrCodeImage;
}