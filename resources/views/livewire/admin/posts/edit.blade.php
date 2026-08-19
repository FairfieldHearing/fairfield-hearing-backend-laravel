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
                <x-form>
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

                    <!-- Key Takeaways Dynamic Section -->
                    <div class="space-y-2 mt-4" x-data="{
                        takeaways: @entangle('key_takeaways') || [],
                        showToolbar: false,
                        toolbarX: 0,
                        toolbarY: 0,
                        activeInputIdx: null,
                        addTakeaway() {
                            if (!Array.isArray(this.takeaways)) {
                                this.takeaways = [];
                            }
                            this.takeaways.push('');
                            this.$nextTick(() => {
                                const el = document.getElementById('takeaway-input-' + (this.takeaways.length - 1));
                                if (el) el.focus();
                            });
                        },
                        removeTakeaway(index) {
                            this.takeaways.splice(index, 1);
                            this.showToolbar = false;
                        },
                        handlePaste(e, index) {
                            e.preventDefault();
                            const text = e.clipboardData.getData('text/plain');
                            document.execCommand('insertText', false, text);
                            const el = document.getElementById('takeaway-input-' + index);
                            if (el) {
                                this.takeaways[index] = el.innerHTML;
                            }
                        },
                        checkSelection(index, el) {
                            const sel = window.getSelection();
                            if (sel.toString().trim().length > 0) {
                                const range = sel.getRangeAt(0);
                                const rect = range.getBoundingClientRect();
                                const containerRect = document.getElementById('takeaways-container').getBoundingClientRect();
                                this.activeInputIdx = index;
                                this.toolbarX = rect.left - containerRect.left + (rect.width / 2) - 30;
                                this.toolbarY = rect.top - containerRect.top - 40;
                                this.showToolbar = true;
                            } else {
                                this.showToolbar = false;
                            }
                        },
                        makeBold() {
                            if (this.activeInputIdx !== null) {
                                document.execCommand('bold', false, null);
                                const el = document.getElementById('takeaway-input-' + this.activeInputIdx);
                                if (el) {
                                    this.takeaways[this.activeInputIdx] = el.innerHTML;
                                }
                                this.showToolbar = false;
                            }
                        }
                    }">
                        <div class="flex items-center justify-between">
                            <label class="label"><span class="label-text font-bold text-sm">Key Takeaways</span></label>
                            <x-button type="button" label="Add Takeaway" icon="o-plus" class="btn-xs btn-outline btn-primary" @click="addTakeaway()" />
                        </div>
                        
                        <div id="takeaways-container" class="relative space-y-3 bg-base-200 p-4 rounded-xl border border-base-300">
                            <!-- Floating Premium bold popover -->
                            <div 
                                x-show="showToolbar" 
                                x-transition 
                                class="absolute z-50 bg-white text-gray-800 px-2 py-1 rounded-md shadow-lg flex items-center gap-1 border border-gray-300"
                                :style="'left: ' + toolbarX + 'px; top: ' + toolbarY + 'px;'"
                                @mousedown.prevent="makeBold()"
                            >
                                <button type="button" class="font-bold text-xs text-gray-800 px-2 py-1 hover:bg-gray-100 rounded transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75a.75.75 0 0 0-.75.75v15a.75.75 0 0 0 .75.75h5.025a3.375 3.375 0 0 0 2.248-.846 3.377 3.377 0 0 0 1.127-2.529 3.38 3.38 0 0 0-.73-2.122 3.38 3.38 0 0 0 1.055-2.427 3.378 3.378 0 0 0-2.25-3.176 3.376 3.376 0 0 0-.69-.15v-.01h-.002L6.75 3.75Zm6 6.75H9v-3.75h3.75a1.875 1.875 0 1 1 0 3.75ZM9 16.5v-4.5h4.5a2.25 2.25 0 1 1 0 4.5H9Z"/></svg>
                                    Bold
                                </button>
                            </div>

                            <template x-for="(takeaway, index) in takeaways" :key="index">
                                <div class="flex items-start gap-2 bg-base-100 p-2 rounded-lg border border-base-200">
                                    <div class="mt-2 shrink-0 w-5 h-5 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold" x-text="index + 1"></div>
                                    <div 
                                        :id="'takeaway-input-' + index"
                                        contenteditable="true"
                                        class="flex-1 min-h-[38px] max-h-[100px] overflow-y-auto bg-base-100 text-sm focus:outline-none py-1.5 px-2 border-b border-dashed border-base-300 focus:border-primary focus:border-solid transition-all"
                                        x-html="takeaway"
                                        @blur="takeaways[index] = $event.target.innerHTML"
                                        @paste="handlePaste($event, index)"
                                        @keyup="checkSelection(index, $el)"
                                        @mouseup="checkSelection(index, $el)"
                                        @keydown.enter.prevent=""
                                        placeholder="Enter key takeaway here... Highlight text to make it bold."
                                    ></div>
                                    <x-button icon="o-trash" class="btn-ghost text-error btn-xs shrink-0 mt-1" @click="removeTakeaway(index)" />
                                </div>
                            </template>

                            <div x-show="!takeaways || takeaways.length === 0" class="text-xs text-base-content/60 py-2 text-center">
                                No key takeaways added yet. Click 'Add Takeaway' to begin.
                            </div>
                        </div>
                    </div>

                    <!-- Editor Selection Toggle & Content Editor Section -->
                    <div class="space-y-4 pt-4 border-t border-base-300" x-data="{ editorMode: @entangle('editor_type') }">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm">Editor Mode</span>
                            <div class="join">
                                <button type="button" class="btn btn-xs join-item" :class="editorMode === 'html' ? 'btn-primary' : 'btn-ghost'" @click="editorMode = 'html'">TinyMCE HTML</button>
                                <button type="button" class="btn btn-xs join-item" :class="editorMode === 'markdown' ? 'btn-primary' : 'btn-ghost'" @click="editorMode = 'markdown'">Markdown</button>
                            </div>
                        </div>

                        <!-- TinyMCE HTML Editor -->
                        <div x-show="editorMode === 'html'" class="space-y-2" wire:ignore
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
                                         plugins: 'advlist autolink lists link image table quickbars custom_image_plugin code',
                                         toolbar: 'undo redo | blocks | bold italic underline removeformat | bullist numlist | custom_image table link | code',
                                         paste_preprocess: function(plugin, args) {
                                             // Remove <style> blocks injected by Word
                                             args.content = args.content.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');
                                             // Strip MsoNormal / Word-class attributes from elements (using \x22 to avoid breaking x-data double quotes)
                                             args.content = args.content.replace(/\s*class=\x22[^\x22]*Mso[^\x22]*\x22/gi, '');
                                             // Strip SourceURL and CSS-text Word pastes as a text prefix
                                             args.content = args.content.replace(/^(\s*(SourceURL:[^\n]*|@[\w\-]+\{[^}]*\}|[\w.#][^{]*\{[^}]*\}|\s*))+/si, '');
                                             // Strip MSO conditional comments
                                             args.content = args.content.replace(/<!--\[if[^\]]*\]>[\s\S]*?<!\[endif\]-->/gi, '');
                                         },
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
                                              $nextTick(() => {
                                                  // Only re-init if TinyMCE has been destroyed
                                                  const edId = $refs.tinymce ? $refs.tinymce.id : null;
                                                  const ed = edId ? window.tinymce.get(edId) : null;
                                                  const alive = ed && ed.getContainer && document.body.contains(ed.getContainer());
                                                  if (!alive) initEditor();
                                              });
                                          });
                                      });
                                  }
                              "
                        >
                            <label class="label"><span class="label-text font-semibold">Content</span></label>
                            <textarea id="blog-post-content-editor" x-ref="tinymce" class="hidden">{{ $content }}</textarea>
                            @error('content') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Markdown Text Area -->
                        <div x-show="editorMode === 'markdown'" class="space-y-2">
                            <x-textarea 
                                label="Markdown Content" 
                                wire:model="markdown_content" 
                                rows="15" 
                                placeholder="Write your post content in markdown..." 
                                hint="Standard Markdown is supported. It will be compiled to clean HTML on the frontend."
                            />
                            @error('markdown_content') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Headless Selector for Editor Image Insertion -->
                    <livewire:admin.components.media-selector target-field="custom_editor_insert" folder="blog_posts" headless="true" />

                    <div class="flex justify-end gap-3 mt-6">
                        <x-button label="Cancel" link="{{ route('admin.posts') }}" class="btn-ghost" no-wire-navigate />
                        <x-button
                            label="Save Article"
                            x-on:click.prevent="
                                if ($wire.editor_type === 'html') {
                                    const ed = window.tinymce.get('blog-post-content-editor');
                                    if (ed) { $wire.content = ed.getContent(); }
                                }
                                $wire.save();
                            "
                            class="btn-primary"
                            spinner="save"
                        />
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

    // Client-side Error Logger to laravel.log in production
    (function() {
        const logClientError = function(message, source, lineno, colno, error) {
            try {
                fetch('/api/log-client-error', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        error: message || 'Unknown JS Error',
                        url: source || window.location.href,
                        line: lineno || 0,
                        col: colno || 0,
                        stack: error && error.stack ? error.stack : ''
                    })
                });
            } catch(e) {}
        };

        window.onerror = function(message, source, lineno, colno, error) {
            logClientError(message, source, lineno, colno, error);
            return false; // let default browser handling run too
        };

        window.addEventListener('unhandledrejection', function(event) {
            const reason = event.reason;
            const message = reason instanceof Error ? reason.message : String(reason);
            const stack = reason instanceof Error ? reason.stack : '';
            logClientError('Unhandled Promise Rejection: ' + message, window.location.href, 0, 0, { stack });
        });
    })();
</script>
