<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FilterHelper
{
    // Retorna a query base do model considerando a credencial logada.
    // - Se id_credential = 1 (matriz), retorna todos os registros.
    // - Caso contrário, filtra apenas os registros da credencial logada.
    public static function baseQuery($modelInstance)
    {
        $idCredential = session('id_credential');

        return $idCredential == 1
            ? $modelInstance->newQuery()
            : $modelInstance->where('id_credential', $idCredential);
    }

    
    // Aplica filtro no campo "id".
    // - Suporta múltiplos valores via string separada por vírgula ou array.
    // - Utiliza whereIn para buscar pelos IDs informados.
    public static function applyIdFilter($query, $request)
    {
        if ($request->filled('id')) {
            $ids = is_string($request->id) ? explode(',', $request->id) : $request->id;
            $query->whereIn('id', (array) $ids);
        }
        return $query;
    }

    


    // Aplica filtro no campo "id_person".
    // - Aceita múltiplos valores como string separada por vírgula ou array.
    // - Utiliza whereIn para filtrar pelos IDs informados.
    public static function applyIdPersonFilter($query, $request)
    {
        if ($request->filled('id_person')) {
            $id_persons = is_string($request->id_person) ? explode(',', $request->id_person) : $request->id_person;
            $query->whereIn('id_person', (array) $id_persons);
        }
        return $query;
    }

    // Aplica filtro no campo "id_type_gender".
    // - Aceita múltiplos valores como string separada por vírgula ou array.
    // - Utiliza whereIn para filtrar pelos IDs informados.
    public static function applyIdTypeGenderFilter($query, $request)
    {
        if ($request->filled('id_type_gender')) {
            $id_type_genders = is_string($request->id_type_gender) ? explode(',', $request->id_type_gender) : $request->id_type_gender;
            $query->whereIn('id_type_gender', (array) $id_type_genders);
        }
        return $query;
    }


    // Aplica filtro no campo "id_type_group".
    // - Aceita múltiplos valores como string separada por vírgula ou array.
    // - Utiliza whereIn para filtrar pelos IDs informados.
    public static function applyIdTypeGroupFilter($query, $request)
    {
        if ($request->filled('id_type_group')) {
            $id_type_groups = is_string($request->id_type_group) ? explode(',', $request->id_type_group) : $request->id_type_group;
            $query->whereIn('id_type_group', (array) $id_type_groups);
        }
        return $query;
    }

    
    // Aplica filtro no campo "id_parent".
    // - Permite múltiplos valores separados por vírgula ou array.
    // - Utiliza whereIn para buscar todos os registros com os IDs informados.
    public static function applyIdParentFilter($query, $request)
    {
        if ($request->filled('id_parent')) {
            $id_parents = is_string($request->id_parent) ? explode(',', $request->id_parent) : $request->id_parent;
            $query->whereIn('id_parent', (array) $id_parents);
        }
        return $query;
    }

    
    // Aplica filtro no campo "name" com busca parcial (LIKE).
    // - Se enviado na requisição, procura nomes que contenham o valor informado.
    public static function applyNameFilter($query, $request)
    {
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        return $query;
    }


    // Aplica filtro no campo "route" com busca parcial (LIKE).
    // - Se enviado na requisição, procura ocorrências que contenham o valor informado.
    public static function applyRouteFilter($query, $request)
    {
        if ($request->filled('route')) {
            $query->where('route', 'like', '%' . $request->route . '%');
        }
        return $query;
    }

    
    // Aplica filtro no campo "active".
    // - Se enviado na requisição, filtra pelo valor informado (0 ou 1).
    // - Se não enviado, assume o valor padrão 1 (registros ativos).
    public static function applyActiveFilter($query, $request)
    {
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }
        return $query;
    }
    

    // Aplica filtros de data para os campos informados (ex: created_at, updated_at).
    // - Para cada campo, verifica se existe uma chave com sufixo `_start` ou `_end` na requisição.
    // - `_start`: aplica filtro >= (início do dia se for apenas data)
    // - `_end`: aplica filtro <= (fim do dia se for apenas data)
    // Exemplo: created_at_start=2024-01-01 → aplica created_at >= '2024-01-01 00:00:00'
    public static function applyDateFilters($query, $request, $fields = ['created_at', 'updated_at'])
    {
        foreach ($fields as $field) {
            foreach (['_start', '_end'] as $suffix) {
                $key = $field . $suffix;
                if ($request->filled($key)) {
                    $value = $request->$key;
                    $value .= strlen($value) === 10
                        ? ($suffix === '_start' ? ' 00:00:00' : ' 23:59:59')
                        : '';
                    $query->where($field, $suffix === '_start' ? '>=' : '<=', $value);
                }
            }
        }
        return $query;
    }

    
    // Aplica ordenação na consulta com base nos parâmetros da requisição.
    // - sort_by: campo permitido para ordenar (padrão: 'id')
    // - sort_order: direção da ordenação ('asc' ou 'desc', padrão: 'desc')
    // Só aplica se o campo estiver dentro da lista permitida ($allowed).
    public static function applyOrderFilter($query, $request, $allowed = ['id', 'name', 'active', 'created_at', 'updated_at'])
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }


    // Retorna o valor de itens por página para paginação.
    // - Usa o valor enviado em "per_page" ou aplica o padrão definido (default = 10).
        public static function getPerPage($request, $default = 10)
    {
        return $request->get('per_page', $default);
    }

    
    // Retorna as opções de ordenação e paginação utilizadas na listagem.
    // - Inclui sort_by, sort_order e per_page com valores da requisição ou padrão.
    public static function getOptions($request)
    {
        return [
            'sort_by' => $request->get('sort_by', 'id'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 10),
        ];
    }
    
    
    // Busca um registro ativo pelo ID, considerando a credencial logada.
    // - Matriz (id_credential = 1): busca sem restrição de credencial.
    // - Filiais: restringe pela id_credential da sessão.
    // - Se não encontrar ou se não estiver ativo, retorna erro 404.
    public static function findOrFail($modelInstance, $id)
    {
        $idCredential = session('id_credential');

        $query = $modelInstance->where('id', $id);

        if ($idCredential != 1) {
            $query->where('id_credential', $idCredential);
        }

        $record = $query->first();

        if (!$record) {
            abort(response()->json(['error' => 'Registro não encontrado.'], 404));
        }

        return $record;
    }

    
    
    // Busca um registro pelo ID com validação de edição pela credencial.
    // - Matriz (id_credential = 1): pode editar qualquer registro.
    // - Filiais: só podem editar registros da própria credencial.
    // - Se não encontrar ou não tiver permissão, retorna erro 404.
    public static function findEditableOrFail($modelInstance, $id)
    {
        $idCredential = session('id_credential');

        $record = $modelInstance->find($id);

        if (!$record || ($idCredential != 1 && $record->id_credential != $idCredential)) {
            abort(response()->json(['error' => 'Registro não encontrado.'], 404));
        }

        return $record;
    }


    // Valida os dados recebidos com base nas regras informadas.
    // - Em caso de erro, lança exceção com retorno JSON (HTTP 422) contendo os erros de validação.
    public static function validateOrFail($data, $rules, $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json([
                'errors' => $validator->errors(),
            ], 422));
        }
    }


}
