<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class PersonController extends Controller
{
    protected string $tableName = 'person';
    protected string $tableLabel = 'person';
    protected string $modelName = 'Person';

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
        $query = FilterHelper::applyIdTypeGenderFilter($query, $request);
        $query = FilterHelper::applyIdTypeGroupFilter($query, $request);
        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request);
        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'name' => $item->name,
                'birthdate' => $item->birthdate?->format('Y-m-d'),
                'id_type_gender' => $item->id_type_gender,
                'id_type_group' => $item->id_type_group,
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
                'id_type_gender' => 'informa o id da tabela type_gender',
                'id_type_group' => 'informa o id da tabela type_group',
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
            'birthdate' => $record->birthdate?->format('Y-m-d'),
            'id_type_gender' => $record->id_type_gender,
            'id_type_group' => $record->id_type_group,
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
            'name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'id_type_gender' => 'required|exists:type_gender,id',
            'id_type_group' => 'required|exists:type_group,id',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'id_type_gender' => $request->id_type_gender,
            'id_type_group' => $request->id_type_group,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'birthdate' => $record->birthdate?->format('Y-m-d'),
            'id_type_gender' => $record->id_type_gender,
            'id_type_group' => $record->id_type_group,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'id_type_gender' => 'required|exists:type_gender,id',
            'id_type_group' => 'required|exists:type_group,id',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'id_type_gender' => $request->id_type_gender,
            'id_type_group' => $request->id_type_group,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'birthdate' => $record->birthdate?->format('Y-m-d'),
            'id_type_gender' => $record->id_type_gender,
            'id_type_group' => $record->id_type_group,
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
