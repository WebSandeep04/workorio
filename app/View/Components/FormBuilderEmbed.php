<?php

namespace App\View\Components;

use App\Models\FormBuilder;
use Illuminate\View\Component;
use Illuminate\View\View;

class FormBuilderEmbed extends Component
{
    public FormBuilder $form;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $fields;

    public bool $embed;

    public int|string $id;

    public function __construct(int|string $id, bool $embed = false)
    {
        $this->id = $id;
        $this->embed = $embed;
        $this->form = FormBuilder::findOrFail($id);
        $raw = $this->form->fields;
        $this->fields = is_array($raw) ? $raw : (json_decode($raw ?? '[]', true) ?: []);
    }

    public function render(): View
    {
        return view('components.form-builder');
    }
}


