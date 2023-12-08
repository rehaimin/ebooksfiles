<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Validate the request data.
        $this->validate($request, [
            'file' => 'required_without:url|file|mimes:pdf',
            'url' => 'required_without:file|nullable|url',
            'name' => 'nullable|string',
        ]);

        set_time_limit(6000);
        ini_set('memory_limit', '4096M');

        if ($request->has('file')) {
            $file = $request->file('file');
            $name = $request->input('name') ?? $file->getClientOriginalName();
            $randomFileName = Str::random(40) . '.pdf';
            $path = $file->storeAs('files', $randomFileName); // Store the file
            $size = $file->getSize();
        } elseif ($request->has('url')) {
            $context = stream_context_create(['http' => ['header' => 'User-Agent: PHP']]);

            $file = file_get_contents($request->url, false, $context);
            $randomFileName = Str::random(40) . '.pdf';
            $name = $request->input('name') ?? basename(urldecode($request->url));
            $path = 'files/' . $randomFileName;

            file_put_contents(storage_path('app/' . $path), $file);
            $size = Storage::size($path);
        }

        if ($file) {
            // Create a new file model.
            $fileModel = new File();
            $fileModel->name = $name;
            $fileModel->path = $path;
            $fileModel->token = Str::random(60); // Generate a random token
            $fileModel->size = number_format(round($size / 1024 / 1024, 2), 2, ',', '.');
            $fileModel->save();

            unset($file);

            // return $fileModel;
            return redirect()->route('files.index');
        } else {
            echo 'Aucun fichier n\'a été soumis.';
            die();
        }
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
    public function update($token)
    {
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
}
