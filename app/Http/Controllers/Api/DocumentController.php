<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class DocumentController extends Controller
{
    protected string $tableName = 'document';
    protected string $tableLabel = 'documents';
    protected string $modelName = 'Document';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyIdPersonFilter($query, $request);

        if ($request->filled('id_type_document')) {
            $query->where('id_type_document', $request->id_type_document);
        }

        if ($request->filled('value')) {
            $query->where('value', 'like', '%' . $request->value . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);

        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_person', 'id_type_document', 'value', 'active', 'created_at', 'updated_at'
        ]);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_person' => $item->id_person,
                'id_type_document' => $item->id_type_document,
                'value' => $item->value,
                'active' => $item->active,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];

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
                'id_person' => 'ID da pessoa',
                'id_type_document' => 'ID do tipo de documento',
                'value' => 'valor do documento',
                'active' => '0 ou 1',
                'created_at_start' => 'Y-m-d',
                'created_at_end' => 'Y-m-d',
                'updated_at_start' => 'Y-m-d',
                'updated_at_end' => 'Y-m-d',
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
            'id_person' => $record->id_person,
            'id_type_document' => $record->id_type_document,
            'value' => $record->value,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ],
        session('id_credential') == 1 ? [
            'id_credential' => $record->id_credential
        ] : []));
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_person' => 'required|integer',
            'id_type_document' => 'required|integer',
            'value' => 'required|string',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_person' => $request->id_person,
            'id_type_document' => $request->id_type_document,
            'value' => $request->value,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'id_type_document' => $record->id_type_document,
            'value' => $record->value,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_type_document' => 'required|integer',
            'value' => 'required|string',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_type_document' => $request->id_type_document,
            'value' => $request->value,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'id_type_document' => $record->id_type_document,
            'value' => $record->value,
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
