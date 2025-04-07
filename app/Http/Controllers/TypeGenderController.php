<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;

class TypeGenderController extends Controller
{
    protected string $tableName = 'type_gender';
    protected string $tableLabel = 'type_genders';
    protected string $modelName = 'TypeGender';

    protected function model()
    {
        $modelClass = '\\App\\Models\\' . $this->modelName;
        return new $modelClass;
    }

    public function index(Request $request)
    {
        $idCredential = session('id_credential');

        if ($idCredential == 1) {
            $query = $this->model()->newQuery();
        } else {
            $query = $this->model()->where('id_credential', $idCredential);
        }

        if ($request->filled('id')) {
            $ids = $request->id;
            if (is_string($ids)) {
                $ids = explode(',', $ids);
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

        if ($request->filled('created_at_start')) {
            $start = $request->created_at_start;
            $start .= strlen($start) === 10 ? ' 00:00:00' : '';
            $query->where('created_at', '>=', $start);
        }

        if ($request->filled('created_at_end')) {
            $end = $request->created_at_end;
            $end .= strlen($end) === 10 ? ' 23:59:59' : '';
            $query->where('created_at', '<=', $end);
        }

        if ($request->filled('updated_at_start')) {
            $start = $request->updated_at_start;
            $start .= strlen($start) === 10 ? ' 00:00:00' : '';
            $query->where('updated_at', '>=', $start);
        }

        if ($request->filled('updated_at_end')) {
            $end = $request->updated_at_end;
            $end .= strlen($end) === 10 ? ' 23:59:59' : '';
            $query->where('updated_at', '<=', $end);
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

        LogHelper::createLog('viewed', $this->tableName, 0, null, $request->all());

        return response()->json([
            $this->tableLabel => $dados,
            'applied_filters' => $request->all(),
            'available_filters' => [
                'id' => 'array ou string separada por vírgula',
                'name' => 'string',
                'active' => '0 ou 1',
                'created_at_start' => 'data (Y-m-d)',
                'created_at_end' => 'data (Y-m-d)',
                'updated_at_start' => 'data (Y-m-d)',
                'updated_at_end' => 'data (Y-m-d)',
            ],
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

        $query = $this->model()->where('id', $id);
        if ($idCredential != 1) {
            $query->where('id_credential', $idCredential);
        }

        $record = $query->where('active', 1)->first();

        if (!$record) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        LogHelper::createLog('show', $this->tableName, $record->id);

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function store(Request $request)
    {
        $idCredential = session('id_credential');

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name',
        ], [
            'name.required' => 'O campo name é obrigatório.',
            'name.unique' => 'Este nome já está em uso.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json([
                'errors' => $validator->errors(),
            ], 422));
        }

        $record = $this->model()->create([
            'id_credential' => $idCredential,
            'name' => $request->name,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $idCredential = session('id_credential');

        $record = $this->model()->find($id);

        if (!$record || ($idCredential != 1 && $record->id_credential != $idCredential)) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name,' . $id,
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

        $old = $record->toArray();

        $record->update([
            'name' => $request->name,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function destroy($id)
    {
        $idCredential = session('id_credential');

        $record = $this->model()->find($id);

        if (!$record || ($idCredential != 1 && $record->id_credential != $idCredential)) {
            return response()->json(['error' => 'Registro não encontrado.'], 404);
        }

        $old = $record->toArray();

        $record->delete();

        LogHelper::createLog('deleted', $this->tableName, $record->id, $old, null);

        return response()->json(['message' => 'Registro excluído com sucesso.']);
    }
}
