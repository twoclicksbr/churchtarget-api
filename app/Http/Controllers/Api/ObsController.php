<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class ObsController extends Controller
{
    protected string $tableName = 'obs';
    protected string $tableLabel = 'obs';
    protected string $modelName = 'Obs';

    protected function model()
    {
        $modelClass = "\\App\\Models\\api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyIdPersonFilter($query, $request);
        $query = FilterHelper::applyRouteFilter($query, $request);
        $query = FilterHelper::applyIdParentFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        // $query = FilterHelper::applyOrderFilter($query, $request);

        // ✅ Aqui você define os campos permitidos para ordenação:
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_person', 'route', 'id_parent', 'created_at', 'updated_at'
        ]);

        $query->with(['person']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_person' => $item->id_person,
                'name_person' => $item->person?->name,
                'route' => $item->route,
                'id_parent' => $item->id_parent,
                'content' => $item->content,
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
                'id_person' => 'array ou string separada por vírgula',
                'route' => 'string',
                'id_parent' => 'array ou string separada por vírgula',
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
        $idCredential = session('id_credential');

        $query = $this->model()->where('id', $id);

        // if ($idCredential != 1) {
        //     $query->where('id_credential', $idCredential);
        // }

        if ($idCredential != 1) {
            $query->where(function ($q) use ($idCredential) {
                $q->where('id_credential', $idCredential)
                  ->orWhere('id_credential', 1);
            });
        }

        $record = $query->first();

        if (!$record) {
            abort(response()->json(['error' => 'Registro não encontrado.'], 404));
        }

        LogHelper::createLog('show', $this->tableName, $record->id);

        return response()->json(array_merge([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'content' => $record->content,
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
            'route' => 'required|string',
            'id_parent' => 'required|integer',
            'content' => 'required|string',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_person' => session('auth_id_person'),
            'route' => $request->route,
            'id_parent' => $request->id_parent,
            'content' => $request->content,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'content' => $record->content,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => 'Função temporariamente desativada.'
        ], 403); // ou 200 se quiser retornar sucesso, mas com a mensagem

        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_person' => 'required|integer',
            'route' => 'required|string',
            'id_parent' => 'required|integer',
            'content' => 'required|string',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_person' => $request->id_person,
            'route' => $request->route,
            'id_parent' => $request->id_parent,
            'content' => $request->content,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'content' => $record->content,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'message' => 'Função temporariamente desativada.'
        ], 403); // ou 200 se quiser retornar sucesso, mas com a mensagem

        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        $old = $record->toArray();
        $record->delete();

        LogHelper::createLog('deleted', $this->tableName, $record->id, $old, null);

        return response()->json(['message' => 'Registro excluído com sucesso.']);
    }
}
