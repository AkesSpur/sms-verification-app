<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait ImageUploadTrait
{
    protected function convertToWebp($imagePath, $ext)
    {
        $newPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
        if ($newPath === $imagePath) {
            $newPath = $imagePath . '.webp';
        }

        $imageType = exif_imagetype($imagePath);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_WEBP:
                return $imagePath;
            default:
                return $imagePath; // Return original if not convertible
        }

        imagewebp($image, $newPath, 80); // Adjust quality as needed
        imagedestroy($image);
        File::delete($imagePath); // Delete original image

        return $newPath;
    }

    public function uploadImage(Request $request, $inputName, $path)
    {
        if ($request->hasFile($inputName)) {
            $image = $request->{$inputName};
            $ext = strtolower($image->getClientOriginalExtension());
            $imageName = 'media_' . uniqid() . '.' . $ext;
            $fullPath = public_path($path . '/' . $imageName);

            $image->move(public_path($path), $imageName);

            // Convert if not already webp
            if (!in_array($ext, ['webp'])) {
                $fullPath = $this->convertToWebp($fullPath, $ext);
                $imageName = basename($fullPath);
            }

            return $path . '/' . $imageName;
        }
    }

    public function uploadMultiImage(Request $request, $inputName, $path)
    {
        $imagePaths = [];

        if ($request->hasFile($inputName)) {
            $images = $request->{$inputName};

            foreach ($images as $image) {
                $ext = strtolower($image->getClientOriginalExtension());
                $imageName = 'media_' . uniqid() . '.' . $ext;
                $fullPath = public_path($path . '/' . $imageName);

                $image->move(public_path($path), $imageName);

                if (!in_array($ext, ['webp'])) {
                    $fullPath = $this->convertToWebp($fullPath, $ext);
                    $imageName = basename($fullPath);
                }

                $imagePaths[] = $path . '/' . $imageName;
            }

            return $imagePaths;
        }
    }

    public function updateImage(Request $request, $inputName, $path, $oldPath = null)
    {
        if ($request->hasFile($inputName)) {
            if ($oldPath && File::exists(public_path($oldPath))) {
                File::delete(public_path($oldPath));
            }

            return $this->uploadImage($request, $inputName, $path);
        }

        return $oldPath;
    }

    public function deleteImage(string $path)
    {
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
