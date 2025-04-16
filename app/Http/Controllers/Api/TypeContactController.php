<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class TypeContactController extends Controller
{
    protected string $tableName = 'type_contact';
    protected string $tableLabel = 'type_contact';
    protected string $modelName = 'TypeContact';

    protected function model()
    {
        $modelClass = "\\App\\Models\\api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyNameFilter($query, $request);
        
        if ($request->filled('input_type')) {
            $query->where('input_type', 'like', '%' . $request->input_type . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        
        $query = FilterHelper::applyDateFilters($query, $request);

        // $query = FilterHelper::applyOrderFilter($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'name', 'input_type', 'mask', 'active', 'created_at', 'updated_at'
        ]);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'name' => $item->name,
                'input_type' => $item->input_type,
                'mask' => $item->mask,
                'active' => $item->active,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];
        
            // 🔐 Só exibe para a matriz (id_credential = 1)
            if (session('id_credential') == 1) {
                $response['id_credential'] = $item->id_credential;
            }
        
            return $response;
        });

        LogHelper::createLog('viewed', $this->tableName, 0, null, $request->all());

        return response()->json([
            $this->tableLabel => $dados,
            'applied_filters' => $request->all(),
            'available_filters' => [
                'id' => 'array ou string separada por vírgula',
                'name' => 'string',
                'input_type' => 'nome do tipo de campo: text, number, email, etc',
                'active' => '0 ou 1',
                'created_at_start' => 'data (Y-m-d)',
                'created_at_end' => 'data (Y-m-d)',
                'updated_at_start' => 'data (Y-m-d)',
                'updated_at_end' => 'data (Y-m-d)',
            ],
            'options' => FilterHelper::getOptions($request),
        ]);
    }

    public function show($id)
    {
        $record = FilterHelper::findOrFail($this->model(), $id);

        LogHelper::createLog('show', $this->tableName, $record->id);

        return response()->json(array_merge([
            'id' => $record->id,
            'name' => $record->name,
            'input_type' => $record->input_type,
            'mask' => $record->mask,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 
        session('id_credential') == 1 ? [
            'id_credential' => $record->id_credential
            ] : [])
        );
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name',
            'input_type' => 'required|string|max:50',
            'mask' => 'nullable|string|max:255',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'name' => $request->name,
            'input_type' => $request->input_type,
            'mask' => $request->mask,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'input_type' => $record->input_type,
            'mask' => $record->mask,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name,' . $id,
            'input_type' => 'required|string|max:50',
            'mask' => 'nullable|string|max:255',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'name' => $request->name,
            'input_type' => $request->input_type,
            'mask' => $request->mask,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'input_type' => $record->input_type,
            'mask' => $record->mask,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ]);
    }

    public function destroy($id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);
        $old = $record->toArray();
        $record->delete();

        LogHelper::createLog('deleted', $this->tableName, $record->id, $old, null);

        return response()->json(['message' => 'Registro excluído com sucesso.']);
    }
}
