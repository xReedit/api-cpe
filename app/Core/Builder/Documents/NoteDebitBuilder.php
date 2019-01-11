<?php

namespace App\Core\Builder\Documents;

use App\Models\Document;

class NoteDebitBuilder extends DocumentBuilder
{
    public function save($inputs)
    {
        $this->saveDocument($inputs['document']);

        $this->document->note()->create($inputs['document_base']);
    }

    public function getDocument()
    {
        return Document::with(['details', 'note'])->find($this->document->id);
    }
}