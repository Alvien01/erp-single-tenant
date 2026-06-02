<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentManager extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';
    public $category = '';
    public $status = '';

    // Upload & Form fields
    public $document_id;
    public $name;
    public $doc_category = 'General';
    public $file; // Holds uploaded file
    public $mock_file_name; // Holds typed mock file name if no actual file uploaded

    // Signature field
    public $signature_data; // Base64 signature path

    public $isOpen = false;
    public $isSignOpen = false;
    public $isVersionsOpen = false;
    public $selectedDoc;

    protected $queryString = ['search', 'category', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($id = null)
    {
        $this->isOpen = true;
        if ($id) {
            $doc = Document::findOrFail($id);
            $this->document_id = $doc->id;
            $this->name = $doc->name;
            $this->doc_category = $doc->category;
        } else {
            $this->resetFields();
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->document_id = null;
        $this->name = '';
        $this->doc_category = 'General';
        $this->file = null;
        $this->mock_file_name = '';
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'doc_category' => 'required|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'mock_file_name' => 'nullable|string|max:255',
        ]);

        $filePath = null;
        if ($this->file) {
            // Livewire file upload storage
            $filePath = $this->file->store('documents', 'public');
        } elseif ($this->mock_file_name) {
            $filePath = 'mock_documents/' . $this->mock_file_name;
        } else {
            $filePath = 'documents/default_template.pdf';
        }

        if ($this->document_id) {
            // New Version creation
            $doc = Document::findOrFail($this->document_id);

            // Record old version in document_versions table before updating
            DocumentVersion::create([
                'document_id' => $doc->id,
                'version' => $doc->version,
                'file_path' => $doc->file_path,
                'created_by' => $doc->created_by,
            ]);

            $newVersion = $doc->version + 1;
            $doc->update([
                'name' => $this->name,
                'category' => $this->doc_category,
                'file_path' => $filePath,
                'version' => $newVersion,
                'status' => 'draft', // Reset to draft for re-evaluation/re-signing
            ]);

            $action = "Uploaded version {$newVersion} for document: {$this->name}";
        } else {
            // New Document creation
            $doc = Document::create([
                'name' => $this->name,
                'category' => $this->doc_category,
                'file_path' => $filePath,
                'version' => 1,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            $action = "Created new document: {$this->name}";
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'DMS',
            'action' => $this->document_id ? 'update_version' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Document processed successfully!');
        $this->closeModal();
    }

    public function openSignModal($id)
    {
        $this->isSignOpen = true;
        $this->selectedDoc = Document::findOrFail($id);
        $this->signature_data = '';
    }

    public function closeSignModal()
    {
        $this->isSignOpen = false;
        $this->selectedDoc = null;
        $this->signature_data = '';
    }

    public function saveSignature()
    {
        $this->validate([
            'signature_data' => 'required|string',
        ]);

        $this->selectedDoc->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_data' => $this->signature_data,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'DMS',
            'action' => 'sign',
            'description' => "Signed contract/document: {$this->selectedDoc->name}",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Document digitally signed and locked successfully!');
        $this->closeSignModal();
    }

    public function markPendingSignature($id)
    {
        $doc = Document::findOrFail($id);
        $doc->update(['status' => 'pending_signature']);
        session()->flash('success', 'Document status changed to Pending Signature!');
    }

    public function showVersions($id)
    {
        $this->isVersionsOpen = true;
        $this->selectedDoc = Document::with('versions.creator')->findOrFail($id);
    }

    public function closeVersionsModal()
    {
        $this->isVersionsOpen = false;
        $this->selectedDoc = null;
    }

    public function restoreVersion($versionId)
    {
        $ver = DocumentVersion::findOrFail($versionId);
        $doc = Document::findOrFail($ver->document_id);

        // Store current version in versions log
        DocumentVersion::create([
            'document_id' => $doc->id,
            'version' => $doc->version,
            'file_path' => $doc->file_path,
            'created_by' => $doc->created_by,
        ]);

        $newVersion = $doc->version + 1;
        $doc->update([
            'version' => $newVersion,
            'file_path' => $ver->file_path,
            'status' => 'draft',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'DMS',
            'action' => 'restore_version',
            'description' => "Restored document {$doc->name} to previous version state",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Document version restored successfully!');
        $this->closeVersionsModal();
    }

    public function delete($id)
    {
        $doc = Document::findOrFail($id);
        $doc->delete();
        session()->flash('success', 'Document deleted successfully!');
    }

    public function render()
    {
        $query = Document::with('creator')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Stats calculation
        $totalDocs = Document::count();
        $signedDocs = Document::where('status', 'signed')->count();
        $pendingDocs = Document::where('status', 'pending_signature')->count();

        return view('livewire.document-manager', [
            'documents' => $query->paginate(10),
            'stats' => [
                'total' => $totalDocs,
                'signed' => $signedDocs,
                'pending' => $pendingDocs,
            ]
        ])->layout('layouts.app');
    }
}
