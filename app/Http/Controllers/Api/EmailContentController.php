<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class EmailContentController extends Controller
{
    protected string $tableName = 'email_content';
    protected string $tableLabel = 'email_content';
    protected string $modelName = 'EmailContent';

    protected function model()
    {
        $modelClass = "\\App\\Models\\api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);

        if ($request->filled('id_type_email')) {
            $query->whereIn('id_type_email', (array) explode(',', $request->id_type_email));
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);
        
        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_type_email', 'subject', 'active', 'created_at', 'updated_at'
        ]);

        $query->with(['typeEmail']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_type_email' => $item->id_type_email,
                'name_type_email' => $item->typeEmail?->name,
                'subject' => $item->subject,
                'banner_url' => $item->banner_url,
                'body' => $item->body,
                'active' => $item->active,
                'created_at' => $item->created_at_formatted,
                'updated_at' => $item->updated_at_formatted,
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
                'id_type_email' => 'ID do tipo de e-mail',
                'subject' => 'filtro parcial',
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
            'id_type_email' => $record->id_type_email,
            'subject' => $record->subject,
            'banner_url' => $record->banner_url,
            'body' => $record->body,
            'active' => $record->active,
            'created_at' => $record->created_at_formatted,
            'updated_at' => $record->updated_at_formatted,
        ] + (session('id_credential') == 1 ? ['id_credential' => $record->id_credential] : []));
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_type_email' => 'required|exists:type_email,id',
            'subject' => 'required|string|max:255',
            'body' => 'required',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_type_email' => $request->id_type_email,
            'subject' => $request->subject,
            'banner_url' => $request->banner_url,
            'body' => $request->body,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_type_email' => $record->id_type_email,
            'subject' => $record->subject,
            'banner_url' => $record->banner_url,
            'body' => $record->body,
            'active' => $record->active,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_type_email' => 'required|exists:type_email,id',
            'subject' => 'required|string|max:255',
            'body' => 'required',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_type_email' => $request->id_type_email,
            'subject' => $request->subject,
            'banner_url' => $request->banner_url,
            'body' => $request->body,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_type_email' => $record->id_type_email,
            'subject' => $record->subject,
            'banner_url' => $record->banner_url,
            'body' => $record->body,
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
