<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};
use Illuminate\Support\Facades\DB;

class PersonRestrictionController extends Controller
{
    protected string $tableName = 'person_restriction';
    protected string $tableLabel = 'restrições';
    protected string $modelName = 'PersonRestriction';

    protected function model()
    {
        $modelClass = "\\App\\Models\\api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);

        // $query = FilterHelper::applyOrderFilter($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_person', 'id_type_user', 'created_at', 'updated_at'
        ]);

        $query->with(['person', 'typeUser']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_person' => $item->id_person,
                'name_person' => $item->person?->name,
                'id_type_user' => $item->id_type_user,
                'name_type_user' => $item->typeUser?->name,
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
                'id_person' => 'integer',
                'id_type_user' => 'integer',
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

        $response = [
            'id' => $record->id,
            'id_person' => $record->id_person,
            'id_type_user' => $record->id_type_user,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ];

        if (session('id_credential') == 1) {
            $response['id_credential'] = $record->id_credential;
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_person' => 'required|exists:person,id',
            'id_type_user' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('type_user')
                        ->where('id', $value)
                        ->whereIn('id_credential', [1, session('id_credential')])
                        ->exists();
        
                    if (! $exists) {
                        $fail('O campo tipo de usuário é inválido.');
                    }
                },
            ],
        ]);        

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_person' => $request->id_person,
            'id_type_user' => $request->id_type_user,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'id_type_user' => $record->id_type_user,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_person' => 'required|exists:person,id',
            'id_type_user' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('type_user')
                        ->where('id', $value)
                        ->whereIn('id_credential', [1, session('id_credential')])
                        ->exists();
    
                    if (! $exists) {
                        $fail('O campo tipo de usuário é inválido.');
                    }
                },
            ],
        ]);

        $old = $record->toArray();

        $record->update([
            'id_person' => $request->id_person,
            'id_type_user' => $request->id_type_user,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'id_type_user' => $record->id_type_user,
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
