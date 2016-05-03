<?php

namespace Kregel\Contractor\Http\Controllers;

use Illuminate\Http\Request;
use Kregel\Contractor\Models\Contract;
use Kregel\Contractor\Models\Paths;
use Kregel\Dispatch\Models\Ticket;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class ContractsController extends Controller
{

    public function create($related_model)
    {
        $model = $this->findRelatedModel($related_model);

        return view('contractor::new.contract', ['related_model' => $model]);
    }

    private function findRelatedModel($slug)
    {
        $slug = '%' . str_replace('-', '%', $slug) . '%';
        $model_class = config('kregel.contractor.related_model');
        return $model_class::where(config('kregel.contractor.related_model_name_key'), 'like', $slug)->first();
    }

    public function home()
    {
        return view('contractor::home');
    }

    public function showAContractor($related_model)
    {
        $model = $this->findRelatedModel($related_model);

        return view('contractor::list-all', ['contracts' => $model->contracts, 'related_model' => $model]);
    }

    public function handlePost($related_model, Request $request)
    {
        $this->validate($request, [
            'path' => 'mimes:pdf|required',
            'name' => 'required'
        ]);
        $model = $this->findRelatedModel($related_model);
        if ($request->hasFile('path')) {
            $file = $request->file('path');
            $ext = strtolower($file->getClientOriginalExtension());
            $uuid = uuid(openssl_random_pseudo_bytes(16));
            $name = $uuid . '.' . $ext;
            $file->move(storage_path(config('kregel.contractor.storage_path')), $name);

            $file_path = config('kregel.contractor.storage_path') . $name;

            $contract = Contract::create([
                'name' => $request->input('name'),
                'who_its_through' => $request->input('who_its_through'),
                'description' => $request->input('description'),
                'notification_date' => $request->input('notification_date'),
                'started_at' => $request->input('started_at'),
                'ended_at' => $request->input('ended_at'),
                'contractor_id' => $model->id
            ]);
            $path = Paths::create([
                'contract_id' => $contract->id,
                'path' => $file_path,
                'uuid' => $uuid,
            ]);
            $contract->contractors()->attach([$model->id]);
            return redirect(route('contractor::list', str_slug($contract->contractors()->first()->name)));
        }

        return response()->json([
            'message' => 'No file was found',
            'code' => 422
        ], 422);
    }

    public function handlePut($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $contract = Contract::find($id);
        $save = false;
        $info = [];
        if ($contract !== null) {
            foreach ($contract->getFillable() as $fillable) {
                if (!empty($data = $request->get($fillable))) {
                    if ($contract->$fillable !== $data) {
                        $contract->$fillable = $data;
                        $save = true;
                        $info[] = $data;
                    }
                }
            }
        }
        if ($save === true) {
            $contract->save();
        }

        return redirect(route('contractor::edit', $id));
    }

    public function edit($ticket)
    {
        if (!is_numeric($ticket)) {
            throw new InvalidArgumentException('Your ticket must be a number.');
        }
        $ticket = Contract::where('id', $ticket)->first();

        return view('contractor::edit.contract', compact('ticket'));
    }

    public function delete($id, Request $request)
    {
        $contract = Contract::find($id);
        if(empty($contract)){
            return response()->json([
                'message' => 'No document found',
                'code' => 404
            ]);
        }
        foreach($contract->paths as $path){
//            if(file_exists(storage_path($path->path))){
//                unlink(storage_path($path->path));
//            }
            $path->delete();
        }
        $contract->delete();
        if($request->ajax()) {
            return response()->json([
                'message' => 'Document and paths deleted!',
                'code' => 202
            ]);
        }
        return response(route('contractor::'));
    }
}