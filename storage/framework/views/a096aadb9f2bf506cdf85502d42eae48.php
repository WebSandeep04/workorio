

<?php $__env->startSection('title', $form->name . ' - Form'); ?>

<?php
    $fields = is_array($form->fields) ? $form->fields : (json_decode($form->fields, true) ?: []);

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

        if (str_contains($name, 'email')) { return 'email'; }
        if (str_contains($name, 'phone') || str_contains($name, 'mobile')) { return 'tel'; }
        if (in_array($type, ['int','bigint','integer','smallint','mediumint','tinyint','decimal','double','float'], true)) { return 'number'; }
        if (in_array($type, ['timestamp','datetime'], true)) { return 'datetime-local'; }
        if ($type === 'date') { return 'date'; }
        if (str_contains($type, 'text')) { return 'textarea'; }
        return 'text';
    };

    $shortcodeSnippet = '<x-form-builder id="' . $form->id . '" />';
    $iframeSnippet = '<iframe src="' . route('formbuilder.view', ['form' => $form->id, 'embed' => 1]) . '" style="border:0;width:100%;min-height:560px;" title="' . e($form->name) . '" loading="lazy"></iframe>';
    
    // Public Embed Logic
    $currentTenantId = session('tenant_id', 1);
    $publicUrl = route('public.form.view', ['tenant' => $currentTenantId, 'form' => $form->id]);
    $publicSubmitUrl = route('public.form.submit', ['tenant' => $currentTenantId, 'form' => $form->id]);
    $publicIframeSnippet = '<iframe src="' . $publicUrl . '" style="border:0;width:100%;min-height:560px;" title="' . e($form->name) . '" loading="lazy"></iframe>';

    $htmlLines = [];
    $htmlLines[] = '<!-- FormBuilder HTML Embed Code -->';
    $htmlLines[] = '<form method="POST" action="' . $publicSubmitUrl . '" class="row g-3">';
    $htmlLines[] = '    <input type="hidden" name="_fb_embed" value="1">';
    foreach ($fields as $field) {
        $name = $field['name'] ?? null;
        if (!$name) { continue; }
        $label = $normalizeLabel($field);
        $type = $resolveType($field);
        $requiredAttr = !empty($field['required']) ? ' required' : '';
        $labelText = $label . (!empty($field['required']) ? ' *' : '');
        $safeLabel = htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $htmlLines[] = '    <div class="col-12" style="margin-bottom: 1rem;">';
        $htmlLines[] = '        <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">' . $safeLabel . '</label>';
        if ($type === 'textarea') {
            $htmlLines[] = '        <textarea class="form-control" name="' . $safeName . '" rows="3" placeholder="Enter ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"' . $requiredAttr . '></textarea>';
        } else {
            $htmlLines[] = '        <input class="form-control" type="' . $type . '" name="' . $safeName . '" placeholder="Enter ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"' . $requiredAttr . ' />';
        }
        $htmlLines[] = '    </div>';
    }
    $htmlLines[] = '    <div class="col-12 text-end" style="margin-top: 1rem; text-align: right;">';
    $htmlLines[] = '        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem; background: #434afa; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Submit</button>';
    $htmlLines[] = '    </div>';
    $htmlLines[] = '</form>';
    $htmlSnippet = implode("\n", $htmlLines);
?>

<?php $__env->startPush('styles'); ?>
<style>
    body {
        background: #f8fafc;
        min-height: 100vh;
    }
    .fb-view-wrapper {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }
    .container-narrow { max-width: 1040px; }
    
    /* Left Card: Embed Option */
    .embed-card {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #ffffff;
        color: #334155;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .embed-header {
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        padding: .85rem 1.25rem;
        font-weight: 600;
        color: #1e293b;
    }
    .embed-header .badge {
        background: #e2e8f0;
        color: #475569;
    }
    .embed-card .card-body {
        padding: 1.5rem;
    }
    .snippet-block + .snippet-block { margin-top: 1.25rem; }
    .snippet-block label { 
        color: #475569; 
        font-weight: 600; 
        text-transform: uppercase; 
        font-size: .75rem; 
        letter-spacing: .05em; 
    }
    .snippet { position: relative; }
    .code-area {
        font-family: 'SFMono-Regular', Consolas, Menlo, Monaco, ui-monospace;
        font-size: .9rem;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        min-height: 100px;
        padding: .75rem;
        resize: vertical;
    }
    .copy-snippet {
        position: absolute;
        bottom: 0.5rem;
        right: 0.5rem;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #434afa;
        font-weight: 500;
        font-size: .75rem;
        padding: .25rem .6rem;
        line-height: 1.2;
        transition: all .2s ease;
        top: auto; /* override previous style */
    }
    .copy-snippet:hover { 
        background: #434afa; 
        color: #fff; 
        border-color: #434afa;
    }

    /* Right Card: Preview Form (Matches iframe ui now) */
    .card-form {
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        background: #fff;
        box-shadow: none;
    }
    .form-header {
        background: #434afa;
        color: #fff;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 0.75rem 1rem;
    }
    .form-header .badge {
        background: rgba(255,255,255,0.2);
        font-size: .75rem;
    }
    .form-card-body {
        padding: 1.5rem;
        background: #fff;
    }
    .form-label {
        font-weight: 500;
        color: #334155;
        font-size: 0.9rem;
    }
    .form-label span { font-weight: 700; color:#dc2626; }
    .form-control {
        border-radius: 3px;
        border: 1px solid #cbd5e1;
        padding: .5rem .7rem;
        font-size: 0.9rem;
        transition: border-color .2s ease;
    }
    .form-control:focus {
        border-color: #434afa;
        box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
    }
    .submit-btn {
        background: #434afa;
        border: none;
        padding: .5rem 1.5rem;
        border-radius: 3px;
        font-weight: 500;
        box-shadow: none;
    }
    .submit-btn:hover { background: #3238c9; }
    .footer-note { color:#94a3b8; font-size:.85rem; }
    @media (max-width: 991.98px) {
        .form-card-body { padding: 1.25rem; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="fb-view-wrapper">
    <div class="container container-narrow">
        <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-5">
                    <div class="card embed-card h-100">
                        <div class="embed-header d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-box-arrow-up-right me-2"></i>Embed Options</div>
                            <span class="badge rounded-pill"><?php echo e(count($fields)); ?> fields</span>
                        </div>
                        <div class="card-body">
                            <p class="mb-4 opacity-90">Copy the code below to embed this form on any website.</p>

                            <div class="snippet-block">
                                <label class="mb-2">Iframe Embed Code</label>
                                <div class="snippet">
                                    <textarea class="form-control code-area" id="snippet-public-<?php echo e($form->id); ?>" rows="3" readonly><?php echo e($publicIframeSnippet); ?></textarea>
                                    <button type="button" class="btn copy-snippet" data-target="snippet-public-<?php echo e($form->id); ?>">Copy</button>
                                </div>
                                <div class="form-text text-light">Place this code on your website to display the form.</div>
                            </div>

                            <div class="snippet-block mt-4">
                                <label class="mb-2">HTML Source Code</label>
                                <div class="snippet">
                                    <textarea class="form-control code-area" id="snippet-html-<?php echo e($form->id); ?>" rows="6" readonly><?php echo e($htmlSnippet); ?></textarea>
                                    <button type="button" class="btn copy-snippet" data-target="snippet-html-<?php echo e($form->id); ?>">Copy</button>
                                </div>
                                <div class="form-text text-light">Use this raw HTML if you want to merge it with your website theme.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card card-form h-100">
                        <div class="form-header d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><?php echo e($form->name); ?></div>
                                <div class="small opacity-75">Live form preview</div>
                            </div>
                            <span class="badge rounded-pill">Preview</span>
                        </div>
                        <div class="form-card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger" role="alert"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="row g-3" method="POST" action="<?php echo e(route('formbuilder.submit', $form->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $name = $f['name'] ?? '';
                                $type = strtolower($f['type'] ?? ($f['data_type'] ?? 'text'));
                                // Make label more readable: handle underscores, camelCase, and capitalize properly
                                $label = $f['label'] ?? $name;
                                $label = str_replace(['_', '-'], ' ', $label);
                                // Convert camelCase to Title Case
                                $label = preg_replace('/([a-z])([A-Z])/', '$1 $2', $label);
                                $label = ucwords(strtolower($label));
                                $isRequired = (bool)($f['required'] ?? false);
                                $inputType = 'text';
                                if (str_contains($name, 'email')) $inputType = 'email';
                                elseif (str_contains($name, 'phone') || str_contains($name, 'mobile')) $inputType = 'tel';
                                elseif (in_array($type, ['int','bigint','integer','smallint','mediumint','tinyint','decimal','double','float'])) $inputType = 'number';
                                elseif ($type === 'timestamp' || $type === 'datetime') $inputType = 'datetime-local';
                                elseif ($type === 'date') $inputType = 'date';
                                elseif (str_contains($type, 'text')) $inputType = 'textarea';
                            ?>
                            <div class="col-12">
                                <label class="form-label"><?php echo e($label); ?> <?php if($isRequired): ?><span class="text-danger">*</span><?php endif; ?></label>
                                <?php if($inputType === 'textarea'): ?>
                                    <textarea class="form-control" rows="3" name="<?php echo e($name); ?>" placeholder="Enter <?php echo e($label); ?>" <?php if($isRequired): ?> required <?php endif; ?>></textarea>
                                <?php else: ?>
                                    <input class="form-control" type="<?php echo e($inputType); ?>" name="<?php echo e($name); ?>" placeholder="Enter <?php echo e($label); ?>" <?php if($isRequired): ?> required <?php endif; ?> />
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(empty($fields)): ?>
                            <div class="col-12 text-muted">No fields to show.</div>
                        <?php endif; ?>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                        </div>
                    </div>
                </div>
        </div>
        <div class="mt-3 text-center footer-note">Powered by Workorio</div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-snippet').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const el = document.getElementById(targetId);
                if (!el) { return; }
                el.select();
                el.setSelectionRange(0, 99999);
                try {
                    const text = el.value;
                    navigator.clipboard.writeText(text).then(() => {
                        this.innerText = 'Copied!';
                        this.classList.add('btn-success');
                        setTimeout(() => {
                            this.innerText = 'Copy';
                            this.classList.remove('btn-success');
                        }, 1600);
                    }).catch(() => {
                        document.execCommand('copy');
                    });
                } catch (err) {
                    document.execCommand('copy');
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/formbuilder/show.blade.php ENDPATH**/ ?>