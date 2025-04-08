<?php

// MODELO BASE - NÃO UTILIZAR DIRETAMENTE
// Use este arquivo como referência para criar novas controllers

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class NomeController extends Controller
{
    protected string $tableName = 'nome_da_tabela';
    protected string $tableLabel = 'nome_label_resposta';
    protected string $modelName = 'NomeModel';

    protected function model()
    {
        $modelClass = "\\App\\Models\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyNameFilter($query, $request);
        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request);
        $perPage = FilterHelper::getPerPage($request);

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
            'options' => FilterHelper::getOptions($request),
        ]);
    }

    public function show($id)
    {
        $record = FilterHelper::findOrFail($this->model(), $id);

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
        FilterHelper::validateOrFail($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name',
        ], [
            'name.required' => 'O campo name é obrigatório.',
            'name.unique' => 'Este nome já está em uso.',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
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
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'name' => 'required|unique:' . $this->tableName . ',name,' . $id,
            'active' => 'required|in:0,1',
        ], [
            'name.required' => 'O campo name é obrigatório.',
            'name.unique' => 'Este nome já está em uso.',
            'active.required' => 'O campo active é obrigatório.',
            'active.in' => 'O campo active deve ser 0 ou 1.',
        ]);

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
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        $old = $record->toArray();
        $record->delete();

        LogHelper::createLog('deleted', $this->tableName, $record->id, $old, null);

        return response()->json(['message' => 'Registro excluído com sucesso.']);
    }
}
