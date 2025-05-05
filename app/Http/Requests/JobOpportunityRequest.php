<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOpportunityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'job_type' => 'required|string|in:full_time,part_time,contract,freelance,internship',
            'work_location_type' => 'required|string|in:remote,hybrid,on_site',
            'experience_level' => 'required|string|in:entry,junior,mid,senior,lead,manager',
            'status' => 'required|string|in:active,closed',
            'deadline' => 'nullable|date|after:today',
        ];
    }
} 