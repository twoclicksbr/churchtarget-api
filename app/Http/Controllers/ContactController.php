<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class ContactController extends Controller
{
    protected string $tableName = 'contact';
    protected string $tableLabel = 'contact';
    protected string $modelName = 'Contact';

    protected function model()
    {
        $modelClass = "\\App\\Models\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyRouteFilter($query, $request);
        $query = FilterHelper::applyIdParentFilter($query, $request);

        if ($request->filled('id_type_contact')) {
            $ids = is_string($request->id_type_contact) ? explode(',', $request->id_type_contact) : $request->id_type_contact;
            $query->whereIn('id_type_contact', (array) $ids);
        }

        if ($request->filled('value')) {
            $query->where('value', 'like', '%' . $request->value . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);

        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'route', 'id_parent', 'id_type_contact', 'value', 'active', 'created_at', 'updated_at'
        ]);

        $query->with(['typeContact']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'route' => $item->route,
                'id_parent' => $item->id_parent,
                'id_type_contact' => $item->id_type_contact,
                'name_type_contact' => $item->typeContact?->name,
                'value' => $item->value,
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
                'route' => 'string (ex: person)',
                'id_parent' => 'ID do registro vinculado',
                'id_type_contact' => 'ID do tipo de contato',
                'value' => 'filtro parcial',
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
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'id_type_contact' => $record->id_type_contact,
            'value' => $record->value,
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
            'route' => 'required|string|max:255',
            'id_parent' => 'required|integer',
            'id_type_contact' => 'required|exists:type_contact,id',
            'value' => 'required|string|max:255',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'route' => $request->route,
            'id_parent' => $request->id_parent,
            'id_type_contact' => $request->id_type_contact,
            'value' => $request->value,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'id_type_contact' => $record->id_type_contact,
            'value' => $record->value,
            'active' => $record->active,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'route' => 'required|string|max:255',
            'id_parent' => 'required|integer',
            'id_type_contact' => 'required|exists:type_contact,id',
            'value' => 'required|string|max:255',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'route' => $request->route,
            'id_parent' => $request->id_parent,
            'id_type_contact' => $request->id_type_contact,
            'value' => $request->value,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'id_type_contact' => $record->id_type_contact,
            'value' => $record->value,
            'active' => $record->active,
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
