<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Sweepstakes;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileDownloadController extends Controller
{
    public function download(Sweepstakes $sweepstakes, File $file): StreamedResponse
    {
        $fileDoesntBelongToTheSweepstakes = $sweepstakes->id !== $file->sweepstakes_id;
        $fileDoesntExist = !Storage::exists($file->path);

        abort_if($fileDoesntExist || $fileDoesntBelongToTheSweepstakes, 404, 'The requested file does not exist.');

        return Storage::download($file->path, $file->original_name, [
            'Content-Type' => $file->mime_type,
        ]);
    }
}
