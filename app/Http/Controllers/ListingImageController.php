<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingImagesRequest;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingImageController extends Controller
{
    // Sube imágenes de un anuncio (solo el dueño), crea miniaturas y las guarda con orden.
    public function store(StoreListingImagesRequest $request, Listing $listing)
    {
        abort_unless($request->user() && $request->user()->id === $listing->user_id, 403);

        $disk = 'public';
        $basePath = 'listings/'.$listing->id;

        $nextOrder = ($listing->images()->max('order') ?? -1) + 1;
        $created = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store($basePath, $disk);
            $thumbPath = $this->makeThumbnail($file->getRealPath(), $basePath, $disk);

            $image = new ListingImage([
                'path' => $path,
                'thumb_path' => $thumbPath,
                'order' => $nextOrder++,
            ]);
            $listing->images()->save($image);
            $created[] = $image;
        }

        if ($request->wantsJson()) {
            return response()->json(['images' => $created], 201);
        }

        return back()->with('status', 'Imágenes subidas correctamente');
    }

    // Elimina una imagen de un anuncio si pertenece al listing y al usuario dueño.
    public function destroy(Request $request, Listing $listing, ListingImage $image)
    {
        abort_unless($request->user() && $request->user()->id === $listing->user_id, 403);
        abort_unless($image->listing_id === $listing->id, 404);

        $paths = array_filter([$image->path, $image->thumb_path]);
        foreach ($paths as $p) {
            Storage::disk('public')->delete($p);
        }
        $image->delete();

        return $request->wantsJson()
            ? response()->json(['deleted' => true])
            : back()->with('status', 'Imagen eliminada');
    }

    // Actualiza el orden de una imagen dentro del anuncio.
    public function reorder(Request $request, Listing $listing, ListingImage $image)
    {
        abort_unless($request->user() && $request->user()->id === $listing->user_id, 403);
        abort_unless($image->listing_id === $listing->id, 404);

        $validated = $request->validate([
            'order' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $image->order = $validated['order'];
        $image->save();

        return $request->wantsJson()
            ? response()->json(['image' => $image])
            : back()->with('status', 'Orden actualizado');
    }

    // Genera una miniatura JPEG, la guarda en disco y devuelve la ruta o null si falla.
    private function makeThumbnail(string $sourcePath, string $basePath, string $disk): ?string
    {
        try {
            if (!extension_loaded('gd')) {
                return null;
            }

            $data = @file_get_contents($sourcePath);
            if ($data === false) {
                return null;
            }

            $img = @imagecreatefromstring($data);
            if (!$img) {
                return null;
            }

            $width = imagesx($img);
            $height = imagesy($img);

            $max = 600; // tamaño para miniatura
            $scale = min(1.0, $max / max($width, $height));
            $newW = (int) max(1, round($width * $scale));
            $newH = (int) max(1, round($height * $scale));

            $thumb = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $width, $height);

            ob_start();
            // Guardamos como JPEG para consistencia/espacio
            imagejpeg($thumb, null, 80);
            $jpeg = ob_get_clean();

            imagedestroy($thumb);
            imagedestroy($img);

            if ($jpeg === false) {
                return null;
            }

            $filename = 'thumbs/'.uniqid('', true).'.jpg';
            $thumbPath = rtrim($basePath, '/').'/'.$filename;

            Storage::disk($disk)->put($thumbPath, $jpeg, 'public');

            return $thumbPath;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
