<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class MinistryPersonRegisteredController extends Controller
{
    protected string $tableName = 'ministry_person_registered';
    protected string $tableLabel = 'ministry_persons_registered';
    protected string $modelName = 'MinistryPersonRegistered';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        $query = FilterHelper::applyIdFilter($query, $request);

        if ($request->filled('id_ministry_cycle')) {
            $id_ministry_cycles = is_string($request->id_ministry_cycle) ? explode(',', $request->id_ministry_cycle) : $request->id_ministry_cycle;
            $query->whereIn('id_ministry_cycle', (array) $id_ministry_cycles);
        }
        return $query;

        $query = FilterHelper::applyIdPersonFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request);
        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'id_ministry_cycle' => $item->id_ministry_cycle,
                'id_person' => $item->id_person,
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
                'id_ministry_cycle' => 'int',
                'id_person' => 'int',
                'created_at_start' => 'data (Y-m-d)',
                'created_at_end' => 'data (Y-m-d)',
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
            'id_ministry_cycle' => $record->id_ministry_cycle,
            'id_person' => $record->id_person,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_ministry_cycle' => 'required|exists:ministry_cycle,id',
            'id_person' => 'required|exists:person,id',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_ministry_cycle' => $request->id_ministry_cycle,
            'id_person' => $request->id_person,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_ministry_cycle' => $record->id_ministry_cycle,
            'id_person' => $record->id_person,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_ministry_cycle' => 'required|exists:ministry_cycle,id',
            'id_person' => 'required|exists:person,id',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_ministry_cycle' => $request->id_ministry_cycle,
            'id_person' => $request->id_person,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_ministry_cycle' => $record->id_ministry_cycle,
            'id_person' => $record->id_person,
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
