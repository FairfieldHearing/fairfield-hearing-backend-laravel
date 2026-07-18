<div>
    <style>
        .ql-editor {
            font-size: 17px !important;
            line-height: 1.8 !important;
            color: #333333 !important;
            font-family: 'Inter', sans-serif !important;
        }
        .ql-editor h2 {
            font-family: 'Lora', 'Playfair Display', serif !important;
            font-size: 28px !important;
            margin: 40px 0 20px 0 !important;
            color: #111111 !important;
            font-weight: 600 !important;
        }
        .ql-editor h3 {
            font-family: 'Inter', sans-serif !important;
            font-size: 20px !important;
            font-weight: 700 !important;
            margin: 30px 0 15px 0 !important;
            color: #111111 !important;
            border-left: 3px solid #a8cf45 !important;
            padding-left: 10px !important;
        }
        .ql-editor p {
            margin-bottom: 20px !important;
        }
        .ql-editor ul, .ql-editor ol {
            padding-left: 20px !important;
            margin-bottom: 25px !important;
        }
        .ql-editor li {
            margin-bottom: 10px !important;
            padding-left: 5px !important;
        }
        .ql-editor table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 15px !important;
            margin: 25px 0 !important;
            background: #ffffff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 8px !important;
        }
        .ql-editor th {
            background-color: #111111 !important;
            color: #ffffff !important;
            padding: 12px 16px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
        }
        .ql-editor td {
            padding: 12px 16px !important;
            border-bottom: 1px solid #eeeeee !important;
            color: #333333 !important;
        }
        .ql-editor tbody tr:nth-child(even) {
            background-color: #fcfdf9 !important;
        }
    </style>

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
                <x-form wire:submit="save" x-on:submit="if (window.quillEditor) { $wire.set('content', window.quillEditor.root.innerHTML); }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-select label="Category" wire:model="blog_category_id" :options="$categories" option-value="id" option-label="title" required />
                        <x-input label="Author Name" wire:model="author_name" required />
                    </div>

                    <x-input label="Title" wire:model.live.debounce.500ms="title" required />
                    <x-input label="Slug" wire:model="slug" required />

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold">Featured Image</span></label>
                        <livewire:admin.components.media-selector wire:model="featured_image" target-field="featured_image" folder="blog_posts" />
                        @error('featured_image') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <x-textarea label="Summary" wire:model="summary" rows="3" />
                    
                    <div class="space-y-2" wire:ignore wire:key="post-content-editor-wrapper">
                        <label class="label"><span class="label-text font-semibold">Content (Rich Text Editor)</span></label>
                        
                        <div 
                            x-data="{
                                initQuill(initialContent) {
                                    window.quillEditor = new Quill(this.$refs.quillCanvas, {
                                        theme: 'snow',
                                        modules: {
                                            table: true,
                                            toolbar: [
                                                [{ 'header': [1, 2, 3, false] }],
                                                ['bold', 'italic', 'underline', 'strike'],
                                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                ['link', 'image', 'video'],
                                                ['table'],
                                                ['clean']
                                            ]
                                        }
                                    });

                                    // Register image toolbar click to open media selector
                                    window.quillEditor.getModule('toolbar').addHandler('image', () => {
                                        window.dispatchEvent(new CustomEvent('open-media-selector-quill_editor_insert'));
                                    });

                                    // Set initial content from parameter
                                    window.quillEditor.root.innerHTML = initialContent || '';

                                    // Sync content back to Livewire on changes
                                    window.quillEditor.on('text-change', () => {
                                        $wire.set('content', window.quillEditor.root.innerHTML);
                                    });

                                    // Watch for external content updates from the server
                                    $watch('$wire.content', (newVal) => {
                                        if (!window.quillEditor.hasFocus() && window.quillEditor.root.innerHTML !== newVal) {
                                            window.quillEditor.root.innerHTML = newVal || '';
                                        }
                                    });
                                }
                            }"
                            x-init="initQuill(@js($content))"
                            class="bg-base-100 rounded-lg border border-base-300 overflow-hidden"
                        >
                            <div x-ref="quillCanvas" class="min-h-[400px]"></div>
                            
                            <script>
                                window.addEventListener('media-selected', function(e) {
                                    if (e.detail.targetField === 'quill_editor_insert') {
                                        if (window.quillEditor) {
                                            const range = window.quillEditor.getSelection();
                                            const index = range ? range.index : window.quillEditor.getLength();
                                            window.quillEditor.insertEmbed(index, 'image', e.detail.url);
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>

                    <!-- Selector specifically for Quill insert content (placed outside wire:ignore) -->
                    <livewire:admin.components.media-selector target-field="quill_editor_insert" folder="blog_posts" headless="true" />

                    <div class="flex justify-end gap-3 mt-6">
                        <x-button label="Cancel" link="{{ route('admin.posts') }}" class="btn-ghost" no-wire-navigate />
                        <x-button label="Save Article" type="submit" class="btn-primary" spinner="save" />
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
