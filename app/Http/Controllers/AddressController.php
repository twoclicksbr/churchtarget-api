<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class AddressController extends Controller
{
    protected string $tableName = 'address';
    protected string $tableLabel = 'address';
    protected string $modelName = 'Address';

    protected function model()
    {
        $modelClass = "\\App\\Models\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyIdParentFilter($query, $request);

        if ($request->filled('cep')) {
            $query->where('cep', 'like', '%' . $request->cep . '%');
        }
        
        if ($request->filled('logradouro')) {
            $query->where('logradouro', 'like', '%' . $request->logradouro . '%');
        }
        
        if ($request->filled('bairro')) {
            $query->where('bairro', 'like', '%' . $request->bairro . '%');
        }
        
        if ($request->filled('localidade')) {
            $query->where('localidade', 'like', '%' . $request->localidade . '%');
        }
        
        if ($request->filled('uf')) {
            $query->where('uf', $request->uf);
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        
        $query = FilterHelper::applyDateFilters($query, $request);
        
        // $query = FilterHelper::applyOrderFilter($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'route', 'id_parent', 'id_type_address',
            'cep', 'logradouro', 'numero', 'bairro', 'localidade', 'uf',
            'active', 'created_at', 'updated_at'
        ]);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'route' => $item->route,
                'id_parent' => $item->id_parent,
                'id_type_address' => $item->id_type_address,
                'cep' => $item->cep,
                'logradouro' => $item->logradouro,
                'numero' => $item->numero,
                'complemento' => $item->complemento,
                'bairro' => $item->bairro,
                'localidade' => $item->localidade,
                'uf' => $item->uf,
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
                'route' => 'nome da origem (ex: person, church)',
                'id_parent' => 'ID do registro vinculado',
                'id_type_address' => 'ID do tipo de endereço (type_address)',
                'cep' => 'filtro parcial (ex: 12345)',
                'logradouro' => 'filtro parcial (ex: Rua João)',
                'bairro' => 'filtro parcial (ex: Centro)',
                'localidade' => 'filtro parcial (ex: São Paulo)',
                'uf' => 'UF exata (ex: SP)',
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
            'id_type_address' => $record->id_type_address,
            'cep' => $record->cep,
            'logradouro' => $record->logradouro,
            'numero' => $record->numero,
            'complemento' => $record->complemento,
            'bairro' => $record->bairro,
            'localidade' => $record->localidade,
            'uf' => $record->uf,
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
            'id_type_address' => 'required|exists:type_address,id',
            'cep' => 'required|string|max:20',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => 'required|string|max:2',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'route' => $request->route,
            'id_parent' => $request->id_parent,
            'id_type_address' => $request->id_type_address,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'localidade' => $request->localidade,
            'uf' => $request->uf,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'id_type_address' => $record->id_type_address,
            'cep' => $record->cep,
            'logradouro' => $record->logradouro,
            'numero' => $record->numero,
            'complemento' => $record->complemento,
            'bairro' => $record->bairro,
            'localidade' => $record->localidade,
            'uf' => $record->uf,
            'active' =>$record->active,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'route' => 'required|string|max:255',
            'id_parent' => 'required|integer',
            'id_type_address' => 'required|exists:type_address,id',
            'cep' => 'required|string|max:20',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => 'required|string|max:2',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();
        $record->update($request->only([
            'route', 'id_parent', 'id_type_address',
            'cep', 'logradouro', 'numero', 'complemento',
            'bairro', 'localidade', 'uf', 'active',
        ]));

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'route' => $record->route,
            'id_parent' => $record->id_parent,
            'id_type_address' => $record->id_type_address,
            'cep' => $record->cep,
            'logradouro' => $record->logradouro,
            'numero' => $record->numero,
            'complemento' => $record->complemento,
            'bairro' => $record->bairro,
            'localidade' => $record->localidade,
            'uf' => $record->uf,
            'active' =>$record->active,
        ], 201);
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
