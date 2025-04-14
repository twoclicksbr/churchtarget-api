<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\{FilterHelper, LogHelper};
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    protected string $tableName = 'media_library';
    protected string $tableLabel = 'media_files';
    protected string $modelName = 'MediaLibrary';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyNameFilter($query, $request);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'name', 'type', 'size', 'active', 'created_at', 'updated_at'
        ]);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'name' => $item->name,
                'url' => $item->url,
                'type' => $item->type,
                'size' => $item->size,
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
                'name' => 'parte do nome',
                'type' => 'image, pdf, etc',
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

        return response()->json([
            'id' => $record->id,
            'name' => $record->name,
            'url' => $record->url,
            'type' => $record->type,
            'size' => $record->size,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
            'id_credential' => session('id_credential') == 1 ? $record->id_credential : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        // Gera nome único e verifica se já existe
        do {
            $generatedName = Str::uuid() . '.' . $extension;
            $exists = $this->model()->where('name', $generatedName)->exists();
        } while ($exists);

        $path = $file->storeAs('media', $generatedName, 'public');

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'name' => $generatedName,
            'url' => asset('storage/' . $path),
            'path' => $path,
            'type' => $extension,
            'size' => $file->getSize(),
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'message' => 'Arquivo enviado com sucesso.',
            'id' => $record->id,
            'url' => $record->url,
        ], 201);
    }

    public function destroy($id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        $old = $record->toArray();

        Storage::disk('public')->delete($record->path);
        $record->delete();

        LogHelper::createLog('deleted', $this->tableName, $record->id, $old, null);

        return response()->json(['message' => 'Arquivo excluído com sucesso.']);
    }
}
