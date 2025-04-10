<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\{FilterHelper, LogHelper};
use App\Models\Contact;

class PersonUserController extends Controller
{
    protected string $tableName = 'person_user';
    protected string $tableLabel = 'person_user';
    protected string $modelName = 'PersonUser';

    protected function model()
    {
        $modelClass = "\\App\\Models\\{$this->modelName}";
        return new $modelClass();
    }

    public function index(Request $request)
    {
        $query = FilterHelper::baseQuery($this->model());
        
        $query = FilterHelper::applyIdFilter($query, $request);
        $query = FilterHelper::applyIdPersonFilter($query, $request);
        $query = FilterHelper::applyEmailFilter($query, $request);
        $query = FilterHelper::applyActiveFilter($query, $request);

        $query = FilterHelper::applyDateFilters($query, $request);
        $query = FilterHelper::applyOrderFilter($query, $request);
        $perPage = FilterHelper::getPerPage($request);

        $dados = $query->paginate($perPage)->through(function ($item) {
            $response = [
                'id' => $item->id,
                'id_person' => $item->id_person,
                'email' => $item->email,
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
                'id_person' => 'array ou string separada por vírgula',
                'email' => 'string',
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
            'id_person' => $record->id_person,
            'email' => $record->email,
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
            'id_person' => 'required|exists:person,id',
            'email' => 'required|email|unique:' . $this->tableName . ',email',
            'password' => 'required|min:6',
        ]);

        $record = $this->model()->create([
            'id_credential' => session('id_credential'),
            'id_person' => $request->id_person,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'active' => 1,
        ]);

        LogHelper::createLog('created', $this->tableName, $record->id, null, $record->toArray());

        $contact = Contact::create([
            'id_credential' => session('id_credential'),
            'route' => 'person',
            'id_parent' => $record->id_person,
            'id_type_contact' => 4, // E-mail
            'value' => $record->email,
            'active' => 1,
        ]);
        
        LogHelper::createLog('created', 'contact', $contact->id, null, $contact->toArray());
        

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'email' => $record->email,
            'active' => $record->active,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = FilterHelper::findEditableOrFail($this->model(), $id);

        FilterHelper::validateOrFail($request->all(), [
            'id_person' => 'required|exists:person,id',
            'email' => 'required|email|unique:' . $this->tableName . ',email,' . $id,
            'password' => 'nullable|min:6',
            'active' => 'required|in:0,1',
        ]);

        $old = $record->toArray();

        $data = [
            'id_person' => $request->id_person,
            'email' => $request->email,
            'active' => $request->active,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $record->update($data);

        LogHelper::createLog('updated', $this->tableName, $record->id, $old, $record->toArray());

        return response()->json([
            'id' => $record->id,
            'id_person' => $record->id_person,
            'email' => $record->email,
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
