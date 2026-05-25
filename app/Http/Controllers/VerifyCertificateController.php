<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class VerifyCertificateController extends Controller
{
    public function show($uuid)
    {
        $certificate = Certificate::where('id', $uuid)
            ->where('status', 'completed')
            ->first();

        if (!$certificate) {
            abort(404, 'Certificate not found or not yet valid.');
        }

        return view('verify.certificate', compact('certificate'));
    }
}
