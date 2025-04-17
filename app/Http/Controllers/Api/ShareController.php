<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};

class ShareController extends Controller
{
    protected string $tableName = 'share';
    protected string $tableLabel = 'shares';
    protected string $modelName = 'Share';

    protected function model()
    {
        $modelClass = "\\App\\Models\\Api\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());

        $query = FilterHelper::applyIdFilter($query, $request);

        if ($request->filled('id_type_share')) {
            $query->whereIn('id_type_share', (array) explode(',', $request->id_type_share));
        }

        if ($request->filled('id_type_gender')) {
            $query->whereIn('id_type_gender', (array) explode(',', $request->id_type_gender));
        }

        if ($request->filled('id_type_participation')) {
            $query->whereIn('id_type_participation', (array) explode(',', $request->id_type_participation));
        }

        if ($request->filled('id_person_leader')) {
            $query->whereIn('id_person_leader', (array) explode(',', $request->id_person_leader));
        }

        if ($request->filled('link')) {
            $query->where('link', 'like', '%' . $request->link . '%');
        }

        $query = FilterHelper::applyActiveFilter($query, $request);
        $query = FilterHelper::applyDateFilters($query, $request);

        $query = FilterHelper::applyOrderFilter($query, $request, [
            'id', 'id_type_share', 'id_type_gender', 'id_type_participation', 'id_person_leader', 'link', 'active', 'created_at', 'updated_at'
        ]);

        $query->with(['typeShare', 'typeGender', 'typeParticipation', 'leader']);

        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_type_share' => $item->id_type_share,
                'name_type_share' => $item->typeShare?->name,
                'id_type_gender' => $item->id_type_gender,
                'name_type_gender' => $item->typeGender?->name,
                'id_type_participation' => $item->id_type_participation,
                'name_type_participation' => $item->typeParticipation?->name,
                'id_person_leader' => $item->id_person_leader,
                'name_person_leader' => $item->leader?->name,
                'link' => $item->link,
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
                'id_type_share' => 'ID do tipo de conteúdo',
                'id_type_gender' => 'ID do gênero',
                'id_type_participation' => 'ID do tipo de participação',
                'id_person_leader' => 'ID da pessoa líder',
                'link' => 'filtro parcial por link',
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
            'id_type_share' => $record->id_type_share,
            'id_type_gender' => $record->id_type_gender,
            'id_type_participation' => $record->id_type_participation,
            'id_person_leader' => $record->id_person_leader,
            'link' => $record->link,
            'active' => $record->active,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
        ],
        session('id_credential') == 1 ? ['id_credential' => $record->id_credential] : []));
    }

    public function store(Request $request)
    {
        FilterHelper::validateOrFail($request->all(), [
            'id_type_share' => 'required|exists:type_share,id',
            'id_type_gender' => 'required|exists:type_gender,id',
            'id_type_participation' => 'required|exists:type_participation,id',
            'id_person_leader' => 'required|exists:person,id',
            'link' => 'required|string|max:255',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_type_share' => $request->id_type_share,
            'id_type_gender' => $request->id_type_gender,
            'id_type_participation' => $request->id_type_participation,
            'id_person_leader' => $request->id_person_leader,
            'link' => $request->link,
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_type_share' => $record->id_type_share,
            'id_type_gender' => $record->id_type_gender,
            'id_type_participation' => $record->id_type_participation,
            'id_person_leader' => $record->id_person_leader,
            'link' => $record->link,
            'active' => $record->active,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_type_share' => 'required|exists:type_share,id',
            'id_type_gender' => 'required|exists:type_gender,id',
            'id_type_participation' => 'required|exists:type_participation,id',
            'id_person_leader' => 'required|exists:person,id',
            'link' => 'required|string|max:255',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $record->update([
            'id_type_share' => $request->id_type_share,
            'id_type_gender' => $request->id_type_gender,
            'id_type_participation' => $request->id_type_participation,
            'id_person_leader' => $request->id_person_leader,
            'link' => $request->link,
            'active' => $request->active,
        ]);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_type_share' => $record->id_type_share,
            'id_type_gender' => $record->id_type_gender,
            'id_type_participation' => $record->id_type_participation,
            'id_person_leader' => $record->id_person_leader,
            'link' => $record->link,
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
