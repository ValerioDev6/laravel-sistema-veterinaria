<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    public static function upload(UploadedFile $file, string $folder): array
    {
        if (config("services.cloudinary.cloud_name")) {
            return self::uploadToCloudinary($file, $folder);
        }

        $path = $file->store("uploads/{$folder}", "public");

        return [
            "url" => asset("storage/{$path}"),
            "public_id" => $path,
        ];
    }

    public static function delete(?string $publicId): void
    {
        if (! $publicId) {
            return;
        }

        if (config("services.cloudinary.cloud_name")) {
            return;
        }

        if (Storage::disk("public")->exists($publicId)) {
            Storage::disk("public")->delete($publicId);
        }
    }

    private static function uploadToCloudinary(
        UploadedFile $file,
        string $folder
    ): array {
        $cloudName = config("services.cloudinary.cloud_name");
        $apiKey = config("services.cloudinary.api_key");
        $apiSecret = config("services.cloudinary.api_secret");

        $timestamp = time();
        $params = ["folder" => $folder, "timestamp" => $timestamp];
        ksort($params);
        $stringToSign = collect($params)
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode("&");
        $signature = hash("sha1", $stringToSign.$apiSecret);

        $response = Http::asMultipart()
            ->attach("file", file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                "folder" => $folder,
                "timestamp" => $timestamp,
                "api_key" => $apiKey,
                "signature" => $signature,
            ]);

        $data = $response->json();

        return [
            "url" => $data["secure_url"] ?? null,
            "public_id" => $data["public_id"] ?? null,
        ];
    }
}