<?php

namespace App\Http\Controllers;

use App\Models\TypeGender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;

class TypeGenderController extends Controller
{
    public function index(Request $request)
    {
        $idCredential = session('id_credential');

        if ($idCredential == 1) {
            $query = TypeGender::query();
        } else {
            $query = TypeGender::where('id_credential', $idCredential);
        }

        if ($request->filled('id')) {
            $ids = $request->id;
            if (is_string($ids)) {
                $ids = explode(',', $ids); // transforma "2,1" em [2, 1]
            }
            $query->whereIn('id', (array) $ids);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        } else {
            $query->where('active', 1);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 10);

        $dados = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'active' => $item->active,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];
        });
        
        LogHelper::createLog('viewed', 'type_gender', 0);

        return response()->json([
            'type_genders' => $dados,
            'applied_filters' => $request->all(),
            'options' => [
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show($id)
    {
        $idCredential = session('id_credential');

        $query = TypeGender::where('id', $id);
        if ($idCredential != 1) {
            $query->where('id_credential', $idCredential);
        }

        $gender = $query->where('active', 1)->first();

        if (!$gender) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        LogHelper::createLog('show', 'type_gender', $gender->id);

        return response()->json([
            'id' => $gender->id,
            'name' => $gender->name,
            'active' => $gender->active,
            'created_at' => $gender->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $gender->updated_at->format('Y-m-d H:i:s'),
        ]);
        
    }

    public function store(Request $request)
    {
        $idCredential = session('id_credential');

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:type_gender,name',
        ], [
            'name.required' => 'O campo name é obrigatório.',
            'name.unique' => 'Este nome já está em uso.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json([
                'errors' => $validator->errors(),
            ], 422));
        }

        $gender = TypeGender::create([
            'id_credential' => $idCredential,
            'name' => $request->name,
            'active' => 1,
        ]);

        LogHelper::createLog('created', 'type_gender', $gender->id, null, $gender->toArray());

        return response()->json([
            'id' => $gender->id,
            'name' => $gender->name,
            'active' => $gender->active,
            'created_at' => $gender->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $gender->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $idCredential = session('id_credential');

        $gender = TypeGender::find($id);

        if (!$gender || ($idCredential != 1 && $gender->id_credential != $idCredential)) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:type_gender,name,' . $id,
            'active' => 'required|in:0,1',
        ], [
            'name.required' => 'O campo name é obrigatório.',
            'name.unique' => 'Este nome já está em uso.',
            'active.required' => 'O campo active é obrigatório.',
            'active.in' => 'O campo active deve ser 0 ou 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $old = $gender->toArray();

        $gender->update([
            'name' => $request->name,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', 'type_gender', $gender->id, $old, $gender->toArray());

        return response()->json([
            'id' => $gender->id,
            'name' => $gender->name,
            'active' => $gender->active,
            'created_at' => $gender->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $gender->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function destroy($id)
    {
        $idCredential = session('id_credential');

        $gender = TypeGender::find($id);

        if (!$gender || ($idCredential != 1 && $gender->id_credential != $idCredential)) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        $old = $gender->toArray();

        $gender->delete();

        LogHelper::createLog('deleted', 'type_gender', $gender->id, $old, null);

        return response()->json(['message' => 'Registro excluído com sucesso.']);
    }
}
