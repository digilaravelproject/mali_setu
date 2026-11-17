<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminTableExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminTableExportController extends Controller
{
    public function excel(Request $request): BinaryFileResponse
    {
        $data = $this->validatedTable($request);

        return Excel::download(
            new AdminTableExport($data['headings'], $data['rows']),
            $data['filename'].'.xlsx'
        );
    }

    public function pdf(Request $request): Response
    {
        $data = $this->validatedTable($request);

        return Pdf::loadView('admin.exports.table', $data)
            ->setPaper('a4', 'landscape')
            ->download($data['filename'].'.pdf');
    }

    private function validatedTable(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'headings' => ['required', 'array', 'min:1', 'max:50'],
            'headings.*' => ['nullable', 'string', 'max:500'],
            'rows' => ['required', 'array', 'max:10000'],
            'rows.*' => ['array', 'max:50'],
            'rows.*.*' => ['nullable', 'string', 'max:5000'],
        ]);

        $columnCount = count($validated['headings']);
        $validated['rows'] = collect($validated['rows'])
            ->map(fn ($row) => array_slice(array_pad(array_values($row), $columnCount, ''), 0, $columnCount))
            ->values()->all();
        $validated['filename'] = str($validated['title'])->slug().'_'.now()->format('Y-m-d_His');
        $validated['generatedAt'] = now();

        return $validated;
    }
}
