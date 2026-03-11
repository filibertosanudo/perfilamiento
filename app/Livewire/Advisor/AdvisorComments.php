<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use App\Models\AdvisorComment;
use App\Models\User;
use Illuminate\Support\Collection;

class AdvisorComments extends Component
{
    // Usuario al que pertenecen los comentarios
    public int $userId;
    public ?int $testResponseId = null;

    // Formulario de nuevo comentario
    public string $body = '';
    public string $type = 'note';
    public bool $flagFollowUp = false;

    // Edición
    public ?int $editingId = null;
    public string $editBody = '';
    public string $editType = 'note';
    public bool $editFlagFollowUp = false;

    // Confirmación de borrado
    public ?int $deletingId = null;

    // Filtros de la lista
    public string $filterType = '';    // '', 'follow_up', 'alert', 'note'

    // Modal visibility (manejado desde blade con Alpine)
    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'body' => 'required|string|min:3|max:2000',
            'type' => 'required|in:note,follow_up,alert',
            'flagFollowUp' => 'boolean',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editBody'        => 'required|string|min:3|max:2000',
            'editType'        => 'required|in:note,follow_up,alert',
            'editFlagFollowUp' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'body.required'     => 'El comentario no puede estar vacío.',
            'body.min'          => 'El comentario debe tener al menos 3 caracteres.',
            'body.max'          => 'El comentario no puede superar los 2000 caracteres.',
            'editBody.required' => 'El comentario no puede estar vacío.',
            'editBody.min'      => 'El comentario debe tener al menos 3 caracteres.',
            'editBody.max'      => 'El comentario no puede superar los 2000 caracteres.',
        ];
    }

    /**
     * Guardar nuevo comentario
     */
    public function save(): void
    {
        $this->validate($this->rules());

        AdvisorComment::create([
            'user_id'          => $this->userId,
            'advisor_id'       => auth()->id(),
            'test_response_id' => $this->testResponseId,
            'body'             => trim($this->body),
            'type'             => $this->type,
            'is_private'       => true,
            'flag_follow_up'   => $this->flagFollowUp,
        ]);

        $this->reset(['body', 'type', 'flagFollowUp', 'showForm']);
        $this->dispatch('comment-saved');
    }

    /**
     * Cargar datos en panel de edición
     */
    public function startEdit(int $commentId): void
    {
        $comment = AdvisorComment::where('id', $commentId)
            ->where('advisor_id', auth()->id())
            ->firstOrFail();

        $this->editingId       = $commentId;
        $this->editBody        = $comment->body;
        $this->editType        = $comment->type;
        $this->editFlagFollowUp = $comment->flag_follow_up;
    }

    /**
     * Guardar edición
     */
    public function saveEdit(): void
    {
        $this->validate($this->editRules());

        AdvisorComment::where('id', $this->editingId)
            ->where('advisor_id', auth()->id())
            ->update([
                'body'           => trim($this->editBody),
                'type'           => $this->editType,
                'flag_follow_up' => $this->editFlagFollowUp,
            ]);

        $this->reset(['editingId', 'editBody', 'editType', 'editFlagFollowUp']);
    }

    /**
     * Cancelar edición
     */
    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'editBody', 'editType', 'editFlagFollowUp']);
    }

    /**
     * Confirmar borrado
     */
    public function confirmDelete(int $commentId): void
    {
        $this->deletingId = $commentId;
    }

    /**
     * Borrar comentario
     */
    public function deleteComment(): void
    {
        AdvisorComment::where('id', $this->deletingId)
            ->where('advisor_id', auth()->id())
            ->delete();

        $this->deletingId = null;
    }

    /**
     * Cancelar borrado
     */
    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    /**
     * Toggle seguimiento rápido desde la lista
     */
    public function toggleFollowUp(int $commentId): void
    {
        $comment = AdvisorComment::where('id', $commentId)
            ->where('advisor_id', auth()->id())
            ->firstOrFail();

        $comment->update(['flag_follow_up' => !$comment->flag_follow_up]);
    }

    public function render()
    {
        $query = AdvisorComment::where('user_id', $this->userId)
            ->with(['advisor', 'testResponse.assignment.test'])
            ->when($this->testResponseId, fn($q) => $q->where('test_response_id', $this->testResponseId))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->orderBy('created_at', 'desc');

        $comments = $query->get();

        $followUpCount = AdvisorComment::where('user_id', $this->userId)
            ->where('flag_follow_up', true)
            ->count();

        return view('livewire.advisor.advisor-comments', [
            'comments'      => $comments,
            'followUpCount' => $followUpCount,
        ]);
    }
}
