<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ImageController extends Controller
{
    public function transformImage(Request $request)
    {
        // Chemin vers les images
        $backgroundImagePath = public_path('images/background-image.jpg');
        $coverImagePath = $request->url; //public_path('images/book-cover.jpg');
        $sideImagePath = public_path('images/book-side.jpg');

        // Charger l'image d'arrière-plan
        $backgroundImage = imagecreatefromjpeg($backgroundImagePath);
        imagefilter($backgroundImage, IMG_FILTER_GAUSSIAN_BLUR); // Appliquer un flou

        // Charger et redimensionner l'image de couverture
        $coverImage = imagecreatefromjpeg($coverImagePath);
        $coverImage = imagescale($coverImage, 775.5, 990);

        // Insérer l'image de couverture dans l'image d'arrière-plan
        imagecopy($backgroundImage, $coverImage, 135, 45, 0, 0, imagesx($coverImage), imagesy($coverImage));

        // Charger et redimensionner l'autre image à ajouter
        $sideImage = imagecreatefromjpeg($sideImagePath);
        $sideImage = imagescale($sideImage, 35.2, 990);

        // Insérer l'autre image à la partie droite de l'image principale
        imagecopy($backgroundImage, $sideImage, 135 + 775.5, 45, 0, 0, imagesx($sideImage), imagesy($sideImage));

        //Vider le répertoire output 
        File::cleanDirectory(public_path('/images/output'));

        // Nom et chemin de l'image finale
        $finalImage = 'images/output/' . Str::random(10) . '.jpg';

        // Sauvegarder l'image transformée
        imagejpeg($backgroundImage, public_path($finalImage), 50); // Utiliser 50 comme qualité, ajustez selon vos besoins

        // Libérer la mémoire
        imagedestroy($backgroundImage);
        imagedestroy($coverImage);
        imagedestroy($sideImage);

        return response()
            ->json([
                'message' => 'Success',
                'image_path' => asset($finalImage)
            ]);
    }
}