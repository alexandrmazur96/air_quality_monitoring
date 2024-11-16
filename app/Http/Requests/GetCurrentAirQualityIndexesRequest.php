<?php

namespace Mazur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetCurrentAirQualityIndexesRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [];
    }
}
