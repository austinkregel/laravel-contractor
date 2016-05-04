<?php

namespace Kregel\Contractor\Http\Controllers\Api;

use Kregel\Contractor\Http\Controllers\Controller;
use Kregel\Contractor\Models\Contract;
use Kregel\Contractor\Models\Paths;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function displayContract($uuid){
        $contract = Paths::where('uuid', $uuid)->first();
        if ((empty($contract) || (!auth()->user()->contracts->contains($contract))) && !auth()->user()->hasRole(['developer', 'saycomputer-admin', 'super-admin']))
            return response()->json(['message' => 'No contract found, please try another uuid', 'code' => 404], 404);
        return response()
            ->make(file_get_contents(storage_path($contract->path)))
            ->header('Content-type', 'application/pdf')
            ->header('Content-length', filesize(storage_path($contract->path)));
    }

    public function postContractCreate($id, Request $request)
    {
        $this->validate($request, [
            'path' => 'mimes:pdf'
        ]);

        if ($request->hasFile('path')) {
            $file = $request->file('path');
            $ext = strtolower($file->getClientOriginalExtension());
            $uuid = uuid(openssl_random_pseudo_bytes(16));
            $name = $uuid . '.' . $ext;
            $file->move(storage_path(config('kregel.contractor.storage_path')), $name);
            $contract = Contract::find($id);
            if($contract !== null) {
                $path = Paths::create([
                    'contract_id' => $id,
                    'uuid' => $uuid,
                    'path' => config('kregel.contractor.storage_path').$name
                ]);
            }
            return response()->json([
                'message' => 'Upload was successful',
                'code' => 202
            ]);
        }
        return response()->json([
            'message' => 'No file was found',
            'code' => 422
        ], 422);
    }
}