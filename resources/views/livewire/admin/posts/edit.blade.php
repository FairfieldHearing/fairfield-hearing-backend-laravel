<div>

    <!-- HEADER -->
    <x-header title="{{ $post ? 'Edit Post: ' . $post->title : 'Create New Blog Post' }}" subtitle="Write and customize your article settings" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Back to Posts" link="{{ route('admin.posts') }}" class="btn-ghost" icon="o-arrow-left" no-wire-navigate />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- MAIN FORM -->
        <div class="lg:col-span-2 space-y-6">
            <x-card shadow class="bg-base-100">
                <x-form wire:submit="save">
                    @if ($errors->any())
                        <div class="alert alert-error shadow-lg mb-4">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <div>
                                    <h3 class="font-bold">Validation Errors:</h3>
                                    <ul class="list-disc list-inside text-xs mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-select label="Category" wire:model="blog_category_id" :options="$categories" option-value="id" option-label="title" required />
                        <x-select label="Author (Team Member)" wire:model="author_id" :options="$authors" option-value="id" option-label="name" placeholder="Select Author" required />
                        <x-select label="Medically Reviewed By" wire:model="reviewer_id" :options="$authors" option-value="id" option-label="name" placeholder="Select Reviewer (Optional)" />
                    </div>

                    <x-input label="Title" wire:model.live.debounce.500ms="title" required />
                    <x-input label="Slug" wire:model="slug" required />

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold">Featured Image</span></label>
                        <livewire:admin.components.media-selector wire:model="featured_image" target-field="featured_image" folder="blog_posts" />
                        @error('featured_image') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <x-textarea label="Summary" wire:model="summary" rows="3" />
                    
                    <div class="space-y-2" wire:ignore
                         x-data="{
                             value: @entangle('content'),
                             initEditor() {
                                 if (!window.tinymce || !$refs.tinymce) return;

                                 const existing = window.tinymce.get($refs.tinymce.id);
                                 if (existing) {
                                     const container = existing.getContainer();
                                     if (container && document.body.contains(container)) {
                                         return;
                                     }
                                     try { existing.remove(); } catch(e) {}
                                 }

                                 window.tinymce.init({
                                     target: $refs.tinymce,
                                     license_key: 'gpl',
                                     height: 400,
                                     menubar: false,
                                     branding: false,
                                     plugins: 'advlist autolink lists link image table quickbars custom_image_plugin key_takeaways_plugin code',
                                     toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | key_takeaways custom_image table link | code',
                                     setup: (editor) => {
                                         editor.on('keyup change undo redo NodeChange blur', () => {
                                             this.value = editor.getContent();
                                         });
                                         editor.on('init', () => {
                                             editor.setContent(this.value ?? '');
                                         });
                                         editor.on('OpenWindow', (e) => editor.topLevelWindow = e.dialog);

                                         this.$watch('value', (newValue) => {
                                             if (editor && typeof editor.getContent === 'function') {
                                                 const val = newValue || '';
                                                 if (val !== editor.getContent()) {
                                                     editor.setContent(val);
                                                 }
                                             }
                                         });
                                     }
                                 });
                             }
                         }"
                         x-init="
                             initEditor();
                             if (typeof Livewire !== 'undefined') {
                                 Livewire.hook('commit', ({ succeed }) => {
                                     succeed(() => {
                                         $nextTick(() => initEditor());
                                     });
                                 });
                             }
                         "
                    >
                        <label class="label"><span class="label-text font-semibold">Content</span></label>
                        <textarea id="blog-post-content-editor" x-ref="tinymce" class="hidden">{{ $content }}</textarea>
                        @error('content') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Headless Selector for Editor Image Insertion -->
                    <livewire:admin.components.media-selector target-field="custom_editor_insert" folder="blog_posts" headless="true" />

                    <div class="flex justify-end gap-3 mt-6">
                        <x-button label="Cancel" link="{{ route('admin.posts') }}" class="btn-ghost" no-wire-navigate />
                        <x-button label="Save Article" wire:click="save" class="btn-primary" spinner="save" />
                    </div>
                </x-form>
            </x-card>
        </div>

        <!-- SIDEBAR OPTIONS (SEO & FAQS) -->
        <div class="space-y-6">
            <!-- FAQs Section -->
            <x-card title="Post Specific FAQs" subtitle="Manage FAQs linked directly to this article" shadow class="bg-base-100">
                <div class="space-y-4">
                    <div class="space-y-2">
                        @foreach($linkedFaqs as $index => $lf)
                            <div class="flex items-start justify-between bg-base-200 p-2.5 rounded border border-base-300 text-sm">
                                <div class="flex-1 min-w-0 pr-2">
                                    <p class="font-bold text-xs truncate">{{ $lf['question'] }}</p>
                                    <p class="text-xs text-base-content/70 mt-1 truncate">{{ $lf['answer'] }}</p>
                                </div>
                                <x-button icon="o-trash" wire:click="removeFaq({{ $index }}, {{ $lf['id'] ?? null }})" class="btn-ghost btn-xs text-error shrink-0" />
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 border-t border-base-300 pt-3">
                        <x-input label="New FAQ Question" wire:model="newFaqQuestion" placeholder="E.g. Is this trial option free?" />
                        <x-textarea label="New FAQ Answer" wire:model="newFaqAnswer" placeholder="FAQ Answer..." rows="2" />
                        <x-button label="Add FAQ to Article" wire:click="addFaq" class="btn-sm btn-outline btn-secondary w-full" />
                    </div>
                </div>
            </x-card>

            <!-- SEO Metadata Section -->
            <x-card title="SEO & Structured Data" subtitle="Optimize search visibility and index configuration" shadow class="bg-base-100">
                <div class="space-y-4">
                    <x-input label="Meta Title" wire:model="meta_title" />
                    <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />
                    <x-textarea label="Meta Keywords" wire:model="meta_keywords" placeholder="Comma-separated keywords" rows="2" />
                    
                    <x-input label="Canonical URL" wire:model="canonical_url" 
                        placeholder="{{ $post ? 'Automatic: ' . $this->automaticCanonical : 'https://...' }}" 
                        hint="{{ $post ? 'Left empty, it defaults to: ' . $this->automaticCanonical : '' }}" />

                    <x-textarea label="JSON Schema (JSON-LD)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="5" />
                </div>
            </x-card>
        </div>
    </div>
</div>

<script>
    // Register custom image manager plugin on TinyMCE statically
    if (typeof window.tinymce === 'undefined') {
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof window.tinymce !== 'undefined') {
                registerTinyMceImagePlugin();
            }
        });
    } else {
        registerTinyMceImagePlugin();
    }

    function registerTinyMceImagePlugin() {
        if (!window.tinymce || !window.tinymce.PluginManager) return;

        if (!window.tinymce.PluginManager.get('custom_image_plugin')) {
            window.tinymce.PluginManager.add('custom_image_plugin', function(editor) {
                editor.ui.registry.addButton('custom_image', {
                    icon: 'image',
                    tooltip: 'Insert Image from Media Manager',
                    onAction: function () {
                        window.activeTinyMceEditor = editor;
                        window.dispatchEvent(new CustomEvent('open-media-selector-custom_editor_insert'));
                    }
                });
            });
        }

        if (!window.tinymce.PluginManager.get('key_takeaways_plugin')) {
            window.tinymce.PluginManager.add('key_takeaways_plugin', function(editor) {
                editor.ui.registry.addButton('key_takeaways', {
                    icon: 'star',
                    text: 'Key Takeaways',
                    tooltip: 'Insert Key Takeaways Box',
                    onAction: function () {
                        editor.insertContent('<div class="takeaways"><h2>Key takeaways</h2><ul><li>First takeaway point...</li><li>Second takeaway point...</li></ul></div><p>&nbsp;</p>');
                    }
                });
            });
        }
    }

    // Connect custom Media Selector events to TinyMCE
    const handleTinyMceMedia = (e) => {
        let payload = null;
        if (e && e.detail) {
            payload = Array.isArray(e.detail) ? e.detail[0] : (e.detail.detail ? e.detail.detail : e.detail);
        } else if (e) {
            payload = Array.isArray(e) ? e[0] : e;
        }

        if (payload && payload.targetField === 'custom_editor_insert') {
            let imageUrl = payload.url;
            if (!imageUrl && payload.filepath) {
                imageUrl = payload.filepath.startsWith('assets/') || payload.filepath.startsWith('/assets/')
                    ? '/' + payload.filepath.replace(/^\//, '')
                    : '/storage/' + payload.filepath;
            }

            if (imageUrl && window.activeTinyMceEditor) {
                window.activeTinyMceEditor.insertContent(`<img src="${imageUrl}" alt="" style="max-width: 100%; height: auto;" />`);
            }
        }
    };

    window.addEventListener('media-selected', handleTinyMceMedia);
    if (typeof Livewire !== 'undefined') {
        Livewire.on('media-selected', handleTinyMceMedia);
    }
</script>
