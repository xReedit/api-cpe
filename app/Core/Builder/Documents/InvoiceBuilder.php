<?php

namespace App\Core\Builder\Documents;

use App\Models\Document;

class InvoiceBuilder extends DocumentBuilder
{
    public function save($inputs)
    {
        $this->saveDocument($inputs['document']);

        $this->document->invoice()->create($inputs['document_base']);
    }

    public function getDocument()
    {
        return Document::with(['details', 'invoice'])->find($this->document->id);
    }
}