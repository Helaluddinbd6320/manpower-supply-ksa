<?php

namespace App\Http\Controllers;

use App\Models\CandidateLead;
use App\Services\CandidateLeadProfilePdfService;

class CandidateLeadPdfController extends Controller
{
    public function show(CandidateLead $candidateLead)
    {
        $candidateLead->load(['destinationCountry', 'jobCategory', 'followUps']);

        $service = app(CandidateLeadProfilePdfService::class);
        $path = $service->generate(collect([$candidateLead]));

        return response()
            ->download($path, 'Candidate-' . str($candidateLead->name)->slug() . '.pdf')
            ->deleteFileAfterSend(true);
    }
}