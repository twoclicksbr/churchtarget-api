<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class MinistryCycleController extends Controller
{
    protected string $tableName = 'ministry_cycle';
    protected string $tableLabel = 'ministry_cycles';
    protected string $modelName = 'MinistryCycle';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        $query = FilterHelper::applyIdFilter($query, $request);
        
        // Filtro por título (like)
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        return $query;

        // Filtro por data de início
        if ($request->filled('starts_at')) {
            $query->whereDate('starts_at', $request->starts_at);
        }

        // Filtro por data de fim
        if ($request->filled('ends_at')) {
            $query->whereDate('ends_at', $request->ends_at);
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'title', 'starts_at', 'ends_at', 'active', 'created_at', 'updated_at'
        ]);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'title' => $item->title,
                'starts_at' => $item->starts_at?->format('Y-m-d'),
                'ends_at' => $item->ends_at?->format('Y-m-d'),
                'active' => $item->active,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];

            if (session('id_credential') == 1) {
                $response['id_credential'] = $item->id_credential;
                $response['id_ministry'] = $item->id_ministry;
            }

            return $response;
        });

        LogHelper::createLog('viewed', $this->tableName, 0, null, $request->all());

        return response()->json([
            $this->tableLabel => $dados,
            'applied_filters' => $request->all(),
            'available_filters' => [
                'id' => 'array ou string separada por vírgula',
                'title' => 'string',
                'starts_at' => 'data (Y-m-d)',
                'ends_at' => 'data (Y-m-d)',
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
            'title' => $record->title,
            'starts_at' => $record->starts_at?->format('Y-m-d'),
            'ends_at' => $record->ends_at?->format('Y-m-d'),
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'title' => 'required|string|unique:' . $this->tableName . ',title',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'id_ministry' => 'required|exists:ministry,id',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_ministry' => $request->id_ministry,
            'title' => $request->title,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'title' => $record->title,
            'starts_at' => $record->starts_at?->format('Y-m-d'),
            'ends_at' => $record->ends_at?->format('Y-m-d'),
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'title' => 'required|string|unique:' . $this->tableName . ',title,' . $id,
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'title' => $request->title,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'title' => $record->title,
            'starts_at' => $record->starts_at?->format('Y-m-d'),
            'ends_at' => $record->ends_at?->format('Y-m-d'),
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
