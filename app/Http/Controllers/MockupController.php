<?php

namespace App\Http\Controllers;

use Spatie\Browsershot\Browsershot;

class MockupController extends Controller
{
    public function viewToImage()
    {
        try {
            $options = [
                'type' => 'png',
                'args' => [],
                'viewport' => [
                    'width' => 1200,
                    'height' => 800,
                ],
            ];

            $screenshot = Browsershot::url(route('mockup'))
                ->base64Screenshot();

            return response()->json(['message' => 'Conversion to JPG successful', 'image_data' => $screenshot]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            dd($errorMessage);
        }
    }
}
