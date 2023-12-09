<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Sopamo\LaravelFilepond\Filepond;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileSystem;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->search;
        $files = File::where('name', 'LIKE', '%' . $searchTerm . '%')->orderBy('created_at', 'desc')
            ->paginate(10); // Récupérer tous les fichiers téléversés depuis la base de données
        return view('files.index', compact('files')); // Passer les fichiers à la vue index
    }

    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required_without:url',
            'url' => 'required_without:file|nullable|url',
            'name' => 'nullable|string',
        ]);

        set_time_limit(6000);
        ini_set('memory_limit', '4096M');

        if ($request->file) {
            $filepond = app(Filepond::class);
            $path = $filepond->getPathFromServerId($request->file);
            $originalName = Str::afterLast($path, '/');
            $fullpath = storage_path('app/') . $path;
            $size = Storage::size($path);
            $randomFileName = Str::random(40) . '.pdf';
            $finalLocation = storage_path('app/files/' . $randomFileName);
            $fileModelPath = str_replace($finalLocation, 'app/', '');
            FileSystem::move($fullpath, $finalLocation);
            $directoryPath = dirname($fullpath);
            FileSystem::deleteDirectory($directoryPath);
            $name = $request->input('name') ?? $originalName;

            $fileModel = new File();
            $fileModel->name = $name;
            $fileModel->path = 'files/' . $randomFileName;
            $fileModel->token = Str::random(60); // Generate a random token
            $fileModel->size = number_format(round($size / 1024 / 1024, 2), 2, ',', '.');
            $fileModel->save();
            return redirect()->route('files.index');
        }
        if ($request->has('url')) {
            $context = stream_context_create(['http' => ['header' => 'User-Agent: PHP']]);

            $file = file_get_contents($request->url, false, $context);
            $randomFileName = Str::random(40) . '.pdf';
            $name = $request->input('name') ?? basename(urldecode($request->url));
            $path = 'files/' . $randomFileName;

            file_put_contents(storage_path('app/' . $path), $file);
            $size = Storage::size($path);
            $fileModel = new File();
            $fileModel->name = $name;
            $fileModel->path = $path;
            $fileModel->token = Str::random(60); // Generate a random token
            $fileModel->size = number_format(round($size / 1024 / 1024, 2), 2, ',', '.');
            $fileModel->save();
            if (isset($file)) {
                unset($file);
            }
            return redirect()->route('files.index');
        }

        echo 'Aucun fichier n\'a été soumis.';
        die();
    }


    public function edit($token)
    {
        $file = File::where('token', $token)->FirstOrFail();
        if ($file) {
            return view('files.edit', ['file' => $file]);
        } else {
            echo 'Fichier introuvable !';
            die();
        }
    }
    public function update(Request $request, $token)
    {
        $this->validate($request, [
            'file' => 'nullable',
            'url' => 'nullable|url',
            'name' => 'nullable|string',
        ]);

        set_time_limit(6000);
        ini_set('memory_limit', '4096M');

        if ($request->file && Str::length($request->file) == 256) {
            $filepond = app(Filepond::class);
            $path = $filepond->getPathFromServerId($request->file);
            $originalName = Str::afterLast($path, '/');
            $fullpath = storage_path('app/') . $path;
            $size = Storage::size($path);
            $randomFileName = Str::random(40) . '.pdf';
            $finalLocation = storage_path('app/files/' . $randomFileName);
            FileSystem::move($fullpath, $finalLocation);
            $directoryPath = dirname($fullpath);
            FileSystem::deleteDirectory($directoryPath);
            $name = $request->input('name') ?? $originalName;

            $fileModel = File::where('token', $token)->firstOrFail();
            $fileModel->name = $name;
            $fileModel->path = 'files/' . $randomFileName;
            $fileModel->size = number_format(round($size / 1024 / 1024, 2), 2, ',', '.');
            $fileModel->save();
            return redirect()->route('files.index');
        }
        if ($request->has('url') && $request->url != '') {
            $context = stream_context_create(['http' => ['header' => 'User-Agent: PHP']]);

            $file = file_get_contents($request->url, false, $context);
            $randomFileName = Str::random(40) . '.pdf';
            $name = $request->input('name') ?? basename(urldecode($request->url));
            $path = 'files/' . $randomFileName;

            file_put_contents(storage_path('app/' . $path), $file);
            $size = Storage::size($path);
            $fileModel = File::where('token', $token)->firstOrFail();
            $fileModel->name = $name;
            $fileModel->path = $path;
            $fileModel->size = number_format(round($size / 1024 / 1024, 2), 2, ',', '.');
            $fileModel->save();
            if (isset($file)) {
                unset($file);
            }
            return redirect()->route('files.index');
        }

        if ($request->name) {
            $fileModel = File::where('token', $token)->firstOrFail();
            $fileModel->name = $request->name;
            $fileModel->save();
            return redirect()->route('files.index');
        }

        echo 'Aucun fichier n\'a été soumis.';
        die();
    }

    public function destroy($token)
    {
        $file = File::where('token', $token)->firstOrFail();

        if ($file) {

            $file->delete();
            Storage::delete($file->path);

            //return response()->json(['success' => 'File deleted successfully'], 200);
            return redirect()->route('files.index');
        } else {
            //return response()->json(['error' => 'File not found or unauthorized access'], 404);
            echo "File not found !";
        }
    }

    public function download($token)
    {
        set_time_limit(6000);
        ini_set('memory_limit', '4096M');
        $file = File::where('token', $token)->firstOrFail();

        if ($file) {
            $filePath = storage_path('app/' . $file->path);
            $fileName = $file->name; // Utilisez le nom stocké dans la base de données
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            if (strtolower($fileExtension) !== 'pdf') {
                $fileName .= '.pdf';
            }

            $fileSize = filesize($filePath);
            $rangeHeader = request()->header('Range');

            if ($rangeHeader) {
                $parts = explode('=', $rangeHeader);
                $start = isset($parts[1]) ? (int) $parts[1] : 0;
                $end = isset($parts[2]) ? (int) $parts[2] : null;

                $headers = [
                    'Content-Type' => 'application/pdf',
                    'Content-Length' => $fileSize,
                    'Content-Range' => sprintf('bytes %d-%d/%d', $start, $end ? $end - 1 : $fileSize, $fileSize),
                    'Accept-Ranges' => 'bytes',
                ];

                return Response::stream(function () use ($filePath, $start, $end) {
                    $fileHandle = fopen($filePath, 'r');

                    fseek($fileHandle, $start);

                    while (!feof($fileHandle)) {
                        if ($end !== null && ftell($fileHandle) >= $end) {
                            break;
                        }

                        echo fread($fileHandle, 1024);
                    }

                    fclose($fileHandle);
                }, $fileName, $headers);
            } else {
                return response()->download($filePath, $fileName);
            }
        } else {
            echo "File not found !";
        }
    }
}
