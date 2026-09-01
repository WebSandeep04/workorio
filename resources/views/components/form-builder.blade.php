@php
    $defaultRoute = route('formbuilder.submit', $form->id);
    // If the component is passed an 'action' attribute, use that. 
    // Otherwise check for $action variable, else default.
    $submitUrl = $attributes->get('action') ?? ($action ?? $defaultRoute);
    $fields = $fields ?? [];

    $normalizeLabel = function (array $field) {
        $source = $field['label'] ?? $field['name'] ?? '';
        $label = str_replace(['_', '-'], ' ', $source);
        $label = preg_replace('/([a-z])([A-Z])/', '$1 $2', $label ?? '');
        $label = ucwords(strtolower(trim($label ?? '')));
        // Remove leading "Sender" word if present
        $label = preg_replace('/^\s*sender\s+/i', '', $label);
        return trim($label);
    };

    $resolveType = function (array $field) {
        $name = strtolower($field['name'] ?? '');
        $type = strtolower($field['type'] ?? ($field['data_type'] ?? 'text'));

        if (str_contains($name, 'email')) {
            return 'email';
        }
        if (str_contains($name, 'phone') || str_contains($name, 'mobile')) {
            return 'tel';
        }
        if (in_array($type, ['int','bigint','integer','smallint','mediumint','tinyint','decimal','double','float'], true)) {
            return 'number';
        }
        if ($type === 'timestamp' || $type === 'datetime') {
            return 'datetime-local';
        }
        if ($type === 'date') {
            return 'date';
        }
        if (str_contains($type, 'text')) {
            return 'textarea';
        }

        return 'text';
    };
@endphp


<form id="fb-embedded-form" class="row g-3" method="POST" action="{{ $submitUrl }}">
    @csrf
    @if(!empty($embed))
        <input type="hidden" name="_fb_embed" value="1">
    @endif

    @foreach($fields as $field)
        @php
            $name = $field['name'] ?? null;
            if (!$name) {
                continue;
            }
            $label = $normalizeLabel($field);
            $inputType = $resolveType($field);
            $isRequired = (bool)($field['required'] ?? false);
        @endphp

        <div class="col-12">
            <label class="form-label fw-semibold">{{ $label }} @if($isRequired)<span class="text-danger">*</span>@endif</label>

            @if($inputType === 'textarea')
                <textarea class="form-control" rows="3" name="{{ $name }}" placeholder="Enter {{ $label }}" @if($isRequired) required @endif></textarea>
            @else
                <input class="form-control" type="{{ $inputType }}" name="{{ $name }}" placeholder="Enter {{ $label }}" @if($isRequired) required @endif>
            @endif
        </div>
    @endforeach

    @if(empty($fields))
        <div class="col-12 text-muted">No fields available.</div>
    @endif

    <div class="col-12 text-center">
        <button id="fb-submit-btn" type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('fb-embedded-form');
    const submitBtn = document.getElementById('fb-submit-btn');

    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // UI Loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Submitting...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if(!response.ok && response.status !== 422) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    form.reset();
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                } else {
                    alert(data.message || 'There was an error submitting the form. Please check your inputs.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>


