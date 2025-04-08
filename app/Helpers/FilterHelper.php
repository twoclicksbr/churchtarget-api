<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FilterHelper
{
    public static function baseQuery($modelInstance)
    {
        $idCredential = session('id_credential');

        return $idCredential == 1
            ? $modelInstance->newQuery()
            : $modelInstance->where('id_credential', $idCredential);
    }

    public static function applyIdFilter($query, $request)
    {
        if ($request->filled('id')) {
            $ids = is_string($request->id) ? explode(',', $request->id) : $request->id;
            $query->whereIn('id', (array) $ids);
        }
        return $query;
    }

    public static function applyNameFilter($query, $request)
    {
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        return $query;
    }

    public static function applyActiveFilter($query, $request)
    {
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        } else {
            $query->where('active', 1);
        }
        return $query;
    }

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

    public static function applyOrderFilter($query, $request, $allowed = ['id', 'name', 'active', 'created_at', 'updated_at'])
    {
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    public static function getPerPage($request, $default = 10)
    {
        return $request->get('per_page', $default);
    }

    public static function getOptions($request)
    {
        return [
            'sort_by' => $request->get('sort_by', 'id'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 10),
        ];
    }
    
    public static function findOrFail($modelInstance, $id)
    {
        $idCredential = session('id_credential');

        $query = $modelInstance->where('id', $id);

        if ($idCredential != 1) {
            $query->where('id_credential', $idCredential);
        }

        $record = $query->where('active', 1)->first();

        if (!$record) {
            abort(response()->json(['error' => 'Registro não encontrado.'], 404));
        }

        return $record;
    }

    public static function findEditableOrFail($modelInstance, $id)
    {
        $idCredential = session('id_credential');

        $record = $modelInstance->find($id);

        if (!$record || ($idCredential != 1 && $record->id_credential != $idCredential)) {
            abort(response()->json(['error' => 'Registro não encontrado.'], 404));
        }

        return $record;
    }

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
