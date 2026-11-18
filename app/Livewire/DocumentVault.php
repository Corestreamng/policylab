<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Support\Facades\URL as FacadeUrl;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DocumentVault extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'state')]
    public string $selectedRegion = '';

    #[Url(as: 'type')]
    public string $selectedType = '';

    #[Url(as: 'year')]
    public string $selectedYear = '';

    public string $viewMode = 'grid'; // 'grid' or 'list'

    public ?string $previewDocUuid = null;
    public ?string $previewStreamUrl = null;
    public ?string $previewTitle = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openPreview(string $uuid): void
    {
        $doc = Document::where('uuid', $uuid)->firstOrFail();
        $this->previewDocUuid = $uuid;
        $this->previewTitle = $doc->title;
        $this->previewStreamUrl = FacadeUrl::temporarySignedRoute(
            'documents.stream',
            now()->addMinutes(30),
            ['document' => $doc->uuid]
        );

        $this->dispatch('document-preview-requested', [
            'url' => $this->previewStreamUrl,
            'title' => $this->previewTitle,
        ]);
    }

    public function closePreview(): void
    {
        $this->previewDocUuid = null;
        $this->previewStreamUrl = null;
        $this->previewTitle = null;
    }

    public function getSignedDownloadUrl(Document $doc): string
    {
        return FacadeUrl::temporarySignedRoute(
            'documents.download',
            now()->addMinutes(30),
            ['document' => $doc->uuid]
        );
    }

    public function render()
    {
        $query = Document::with(['region', 'supportingOrg'])
            ->where('is_public', true);

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('file_name', 'like', $term)
                  ->orWhereHas('region', fn($r) => $r->where('name', 'like', $term));
            });
        }

        if (!empty($this->selectedRegion)) {
            $query->where('region_id', $this->selectedRegion);
        }

        if (!empty($this->selectedType)) {
            $query->where('document_type', $this->selectedType);
        }

        if (!empty($this->selectedYear)) {
            $query->where('year', (int)$this->selectedYear);
        }

        $documents = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->paginate(12);

        $regions = Region::orderBy('name')->get();
        $years = Document::whereNotNull('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('livewire.document-vault', [
            'documents' => $documents,
            'regions' => $regions,
            'years' => $years,
            'totalDocs' => Document::where('is_public', true)->count(),
        ]);
    }
}
