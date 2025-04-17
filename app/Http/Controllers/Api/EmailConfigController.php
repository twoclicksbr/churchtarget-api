<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class EmailConfigController extends Controller
{
    protected string $tableName = 'email_config';
    protected string $tableLabel = 'email_configs';
    protected string $modelName = 'EmailConfig';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);

        if ($request->filled('id_ministry')) {
            $query->where('id_ministry', $request->id_ministry);
        }

        if ($request->filled('id_type_email_config')) {
            $query->where('id_type_email_config', $request->id_type_email_config);
        }

        if ($request->filled('events')) {
            $query->where('events', 'like', '%' . $request->events . '%');
        }

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);

        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_ministry', 'id_type_email_config', 'banner_url', 'events', 'client_name', 'active', 'created_at', 'updated_at'
        ]);

        $query->with(['type']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_ministry' => $item->id_ministry,
                'id_type_email_config' => $item->id_type_email_config,
                'banner_url' => $item->banner_url,
                'events' => $item->events,
                'client_name' => $item->client_name,
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
                'id_ministry' => 'ID do ministério',
                'id_type_email_config' => 'ID do tipo',
                'events' => 'parte do texto do evento',
                'client_name' => 'parte do nome do cliente',
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
            'id_ministry' => $record->id_ministry,
            'id_type_email_config' => $record->id_type_email_config,
            'banner_url' => $record->banner_url,
            'events' => $record->events,
            'client_name' => $record->client_name,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ], session('id_credential') == 1 ? [
            'id_credential' => $record->id_credential
        ] : []));
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_ministry' => 'required|integer|exists:ministry,id',
            'id_type_email_config' => 'required|integer',
            'banner_url' => 'nullable|string',
            'events' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
        ]);

        $bannerUrl = $request->banner_url;

        // Caminho local relativo à pasta public/
        $relativePath = str_replace(asset(''), '', $bannerUrl);
        $absolutePath = public_path($relativePath);

        // Verifica se o arquivo realmente existe
        if ($bannerUrl && !file_exists($absolutePath)) {
            return response()->json(['error' => 'O arquivo do banner não foi encontrado.'], 400);
        }

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_ministry' => $request->id_ministry,
            'id_type_email_config' => $request->id_type_email_config,
            'banner_url' => $request->banner_url,
            'events' => $request->events,
            'client_name' => $request->client_name,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_ministry' => $record->id_ministry,
            'id_type_email_config' => $record->id_type_email_config,
            'banner_url' => $record->banner_url,
            'events' => $record->events,
            'client_name' => $record->client_name,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_ministry' => 'required|integer|exists:ministry,id',
            'id_type_email_config' => 'required|integer',
            'banner_url' => 'nullable|string',
            'events' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_ministry' => $request->id_ministry,
            'id_type_email_config' => $request->id_type_email_config,
            'banner_url' => $request->banner_url,
            'events' => $request->events,
            'client_name' => $request->client_name,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_ministry' => $record->id_ministry,
            'id_type_email_config' => $record->id_type_email_config,
            'banner_url' => $record->banner_url,
            'events' => $record->events,
            'client_name' => $record->client_name,
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
