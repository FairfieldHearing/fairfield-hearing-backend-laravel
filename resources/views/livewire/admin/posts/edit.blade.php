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
                        
                        <!-- Safe JSON script block to pass database content to Alpine without HTML attribute quote collisions -->
                        <script id="custom-content-source" type="application/json">{!! json_encode($content) !!}</script>

                        <div 
                            x-data="{
                                content: '',
                                selectedCell: null,
                                selectedTable: null,
                                selectedImg: null,
                                resizingImg: null,
                                startX: 0,
                                startWidth: 0,
                                initEditor() {
                                    // Load initial content from the JSON block
                                    const sourceEl = document.getElementById('custom-content-source');
                                    if (sourceEl) {
                                        this.content = JSON.parse(sourceEl.textContent) || '';
                                        this.$refs.editorCanvas.innerHTML = this.content;
                                    }

                                     // Listen for image selection
                                     const handleSelection = (e) => {
                                         // Unpack payload depending on Livewire v3 event detail format
                                         let payload = null;
                                         if (e && e.detail) {
                                             payload = Array.isArray(e.detail) ? e.detail[0] : (e.detail.detail ? e.detail.detail : e.detail);
                                         } else if (e) {
                                             payload = Array.isArray(e) ? e[0] : e;
                                         }

                                         if (payload && payload.targetField === 'custom_editor_insert') {
                                             this.$refs.editorCanvas.focus();
                                             
                                             // Restore selection if saved
                                             if (this.savedRange) {
                                                 const sel = window.getSelection();
                                                 sel.removeAllRanges();
                                                 sel.addRange(this.savedRange);
                                             }

                                             // Resolve target image URL
                                             let imageUrl = payload.url;
                                             if (!imageUrl && payload.filepath) {
                                                 imageUrl = payload.filepath.startsWith('assets/') || payload.filepath.startsWith('/assets/')
                                                     ? '/' + payload.filepath.replace(/^\//, '')
                                                     : '/storage/' + payload.filepath;
                                             }

                                             if (imageUrl) {
                                                 const imgHtml = `<img src='${imageUrl}' alt='Blog image' style='max-width:100%; height:auto; margin: 15px 0; border-radius: 8px; display: block;' />`;
                                                 
                                                 if (this.savedRange) {
                                                     document.execCommand('insertHTML', false, imgHtml);
                                                 } else {
                                                     this.$refs.editorCanvas.innerHTML += imgHtml;
                                                 }
                                                 this.syncContent();
                                             }
                                         }
                                     };

                                     // Support native DOM events
                                     window.addEventListener('media-selected', handleSelection);

                                     // Support Livewire v3 event dispatching system
                                     if (typeof Livewire !== 'undefined') {
                                         Livewire.on('media-selected', handleSelection);
                                     }
                                },
                                savedRange: null,
                                saveSelection() {
                                    const sel = window.getSelection();
                                    if (sel.getRangeAt && sel.rangeCount) {
                                        this.savedRange = sel.getRangeAt(0);
                                    }
                                },
                                exec(cmd, val = null) {
                                    this.$refs.editorCanvas.focus();
                                    document.execCommand(cmd, false, val);
                                    this.syncContent();
                                },
                                insertTable() {
                                    const tableHtml = `
                                        <div class='fhc-article-table-wrapper'>
                                            <table class='fhc-article-table'>
                                                <thead>
                                                    <tr>
                                                        <th>Feature</th>
                                                        <th>Styletto 7IX</th>
                                                        <th>Styletto 5IX</th>
                                                        <th>Styletto 3IX</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Tier</td>
                                                        <td>Premium</td>
                                                        <td>Advanced</td>
                                                        <td>Essential</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Automatic programs</td>
                                                        <td>Most</td>
                                                        <td>More</td>
                                                        <td>Core</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Speech in noise</td>
                                                        <td>Excellent</td>
                                                        <td>Very good</td>
                                                        <td>Good</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Best for</td>
                                                        <td>Busy, noisy lifestyles</td>
                                                        <td>Mixed daily life</td>
                                                        <td>Quieter, routine settings</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Design / rechargeable / Bluetooth</td>
                                                        <td>Same</td>
                                                        <td>Same</td>
                                                        <td>Same</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p><br></p>
                                    `;
                                    this.exec('insertHTML', tableHtml);
                                },
                                // Image Drag Resizing (Pan)
                                handleImgMouseDown(e) {
                                    if (e.target.tagName === 'IMG') {
                                        const img = e.target;
                                        const rect = img.getBoundingClientRect();
                                        const mouseX = e.clientX - rect.left;
                                        const mouseY = e.clientY - rect.top;
                                        
                                        // Trigger drag resize if clicking near the bottom-right corner (within 20px)
                                        if (rect.width - mouseX < 20 && rect.height - mouseY < 20) {
                                            e.preventDefault();
                                            this.resizingImg = img;
                                            this.startX = e.clientX;
                                            this.startWidth = img.offsetWidth;
                                            
                                            const onMouseMove = (moveEvent) => {
                                                if (this.resizingImg) {
                                                    const deltaX = moveEvent.clientX - this.startX;
                                                    const newWidth = Math.max(50, this.startWidth + deltaX);
                                                    const canvasWidth = this.$refs.editorCanvas.offsetWidth;
                                                    const percentWidth = Math.min(100, Math.round((newWidth / canvasWidth) * 100));
                                                    this.resizingImg.style.width = percentWidth + '%';
                                                    this.resizingImg.style.height = 'auto';
                                                }
                                            };
                                            
                                            const onMouseUp = () => {
                                                this.resizingImg = null;
                                                window.removeEventListener('mousemove', onMouseMove);
                                                window.removeEventListener('mouseup', onMouseUp);
                                                this.syncContent();
                                            };
                                            
                                            window.addEventListener('mousemove', onMouseMove);
                                            window.addEventListener('mouseup', onMouseUp);
                                        }
                                    }
                                },
                                handleImgMouseMove(e) {
                                    if (e.target.tagName === 'IMG') {
                                        const img = e.target;
                                        const rect = img.getBoundingClientRect();
                                        const mouseX = e.clientX - rect.left;
                                        const mouseY = e.clientY - rect.top;
                                        if (rect.width - mouseX < 20 && rect.height - mouseY < 20) {
                                            img.style.cursor = 'se-resize';
                                        } else {
                                            img.style.cursor = 'default';
                                        }
                                    }
                                },
                                // Image Alignment / Sizing Options
                                alignImg(dir) {
                                    if (!this.selectedImg) return;
                                    if (dir === 'left') {
                                        this.selectedImg.style.display = 'inline-block';
                                        this.selectedImg.style.float = 'left';
                                        this.selectedImg.style.margin = '0 15px 15px 0';
                                    } else if (dir === 'right') {
                                        this.selectedImg.style.display = 'inline-block';
                                        this.selectedImg.style.float = 'right';
                                        this.selectedImg.style.margin = '0 0 15px 15px';
                                    } else if (dir === 'center') {
                                        this.selectedImg.style.display = 'block';
                                        this.selectedImg.style.float = 'none';
                                        this.selectedImg.style.margin = '15px auto';
                                    }
                                    this.syncContent();
                                },
                                resizeImg(percent) {
                                    if (!this.selectedImg) return;
                                    this.selectedImg.style.width = percent + '%';
                                    this.selectedImg.style.height = 'auto';
                                    this.syncContent();
                                },
                                deleteImg() {
                                    if (!this.selectedImg) return;
                                    this.selectedImg.remove();
                                    this.selectedImg = null;
                                    this.syncContent();
                                },
                                // Table dynamic rows / columns manipulation
                                handleEditorClick(e) {
                                    const cell = e.target.closest('td, th');
                                    if (cell) {
                                        this.selectedCell = cell;
                                        this.selectedTable = cell.closest('table');
                                        this.selectedImg = null;
                                    } else if (e.target.tagName === 'IMG') {
                                        this.selectedImg = e.target;
                                        this.selectedCell = null;
                                        this.selectedTable = null;
                                    } else {
                                        this.selectedCell = null;
                                        this.selectedTable = null;
                                        this.selectedImg = null;
                                    }
                                },
                                addRow(below = true) {
                                    if (!this.selectedCell || !this.selectedTable) return;
                                    const row = this.selectedCell.parentElement;
                                    const rowIndex = row.rowIndex;
                                    const targetIndex = below ? rowIndex + 1 : rowIndex;
                                    const newRow = this.selectedTable.insertRow(targetIndex);
                                    for (let i = 0; i < row.cells.length; i++) {
                                        const newCell = newRow.insertCell(i);
                                        newCell.innerHTML = 'Cell';
                                    }
                                    this.syncContent();
                                },
                                addColumn(right = true) {
                                    if (!this.selectedCell || !this.selectedTable) return;
                                    const cellIndex = this.selectedCell.cellIndex;
                                    const targetIndex = right ? cellIndex + 1 : cellIndex;
                                    for (let i = 0; i < this.selectedTable.rows.length; i++) {
                                        const row = this.selectedTable.rows[i];
                                        const isHeader = row.parentElement.tagName === 'THEAD' || row.cells[cellIndex].tagName === 'TH';
                                        const newCell = document.createElement(isHeader ? 'th' : 'td');
                                        newCell.innerHTML = isHeader ? 'Header' : 'Cell';
                                        row.insertBefore(newCell, row.cells[targetIndex]);
                                    }
                                    this.syncContent();
                                },
                                deleteRow() {
                                    if (!this.selectedCell || !this.selectedTable) return;
                                    this.selectedCell.parentElement.remove();
                                    this.selectedCell = null;
                                    this.selectedTable = null;
                                    this.syncContent();
                                },
                                deleteColumn() {
                                    if (!this.selectedCell || !this.selectedTable) return;
                                    const cellIndex = this.selectedCell.cellIndex;
                                    for (let i = 0; i < this.selectedTable.rows.length; i++) {
                                        this.selectedTable.rows[i].deleteCell(cellIndex);
                                    }
                                    this.selectedCell = null;
                                    this.selectedTable = null;
                                    this.syncContent();
                                },
                                deleteTable() {
                                    if (this.selectedTable) {
                                        const wrapper = this.selectedTable.closest('.fhc-article-table-wrapper');
                                        if (wrapper) {
                                            wrapper.remove();
                                        } else {
                                            this.selectedTable.remove();
                                        }
                                        this.selectedCell = null;
                                        this.selectedTable = null;
                                        this.syncContent();
                                    }
                                },
                                syncContent() {
                                    this.content = this.$refs.editorCanvas.innerHTML;
                                    $wire.set('content', this.content);
                                }
                            }"
                            x-init="initEditor()"
                            @click.away="selectedCell = null; selectedTable = null; selectedImg = null;"
                            class="bg-base-100 rounded-lg border border-base-300 flex flex-col"
                        >
                            <!-- STICKY TOOLBAR HEADER -->
                            <div class="sticky top-0 z-[45] bg-base-200 border-b border-base-300 flex flex-col shadow-sm rounded-t-lg">
                                <!-- MAIN TOOLBAR -->
                                <div class="flex flex-wrap items-center gap-1.5 p-2 bg-base-200">
                                    <button type="button" @click="exec('bold')" class="btn btn-ghost p-2" title="Bold"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" @click="exec('italic')" class="btn btn-ghost p-2" title="Italic"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" @click="exec('underline')" class="btn btn-ghost p-2" title="Underline"><i class="bi bi-type-underline"></i></button>
                                    <button type="button" @click="exec('strikeThrough')" class="btn btn-ghost p-2" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
                                    
                                    <div class="w-px h-6 bg-base-300 mx-1"></div>

                                    <button type="button" @click="exec('formatBlock', '<h2>')" class="btn btn-ghost font-bold px-2.5" title="Heading 2">H2</button>
                                    <button type="button" @click="exec('formatBlock', '<h3>')" class="btn btn-ghost font-bold px-2.5" title="Heading 3">H3</button>
                                    <button type="button" @click="exec('formatBlock', '<p>')" class="btn btn-ghost p-2" title="Normal text"><i class="bi bi-file-text"></i></button>

                                    <div class="w-px h-6 bg-base-300 mx-1"></div>

                                    <button type="button" @click="exec('justifyLeft')" class="btn btn-ghost p-2" title="Align Left"><i class="bi bi-text-left"></i></button>
                                    <button type="button" @click="exec('justifyCenter')" class="btn btn-ghost p-2" title="Align Center"><i class="bi bi-text-center"></i></button>
                                    <button type="button" @click="exec('justifyRight')" class="btn btn-ghost p-2" title="Align Right"><i class="bi bi-text-right"></i></button>

                                    <div class="w-px h-6 bg-base-300 mx-1"></div>

                                    <button type="button" @click="exec('insertUnorderedList')" class="btn btn-ghost p-2" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" @click="exec('insertOrderedList')" class="btn btn-ghost p-2" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                    
                                    <div class="w-px h-6 bg-base-300 mx-1"></div>

                                    <button type="button" @click="saveSelection(); window.dispatchEvent(new CustomEvent('open-media-selector-custom_editor_insert'))" class="btn btn-ghost p-2" title="Insert Image">
                                        <i class="bi bi-image"></i>
                                    </button>
                                    <button type="button" @click="insertTable()" class="btn btn-ghost p-2" title="Insert Table">
                                        <i class="bi bi-table"></i>
                                    </button>

                                    <button type="button" @click="exec('removeFormat')" class="btn btn-ghost p-2" title="Clear Formatting">
                                        <i class="bi bi-eraser"></i>
                                    </button>
                                </div>

                                <!-- DYNAMIC TABLE SUB-TOOLBAR (Visible only when cell is focused) -->
                                <template x-if="selectedCell && selectedTable">
                                    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-info/10 border-t border-base-300 text-xs text-info-content">
                                        <span class="font-semibold mr-1"><i class="bi bi-table mr-1"></i> Table Settings:</span>
                                        <button type="button" @click="addRow(false)" class="btn btn-xs btn-outline btn-info gap-1"><i class="bi bi-arrow-bar-up"></i> + Row Above</button>
                                        <button type="button" @click="addRow(true)" class="btn btn-xs btn-outline btn-info gap-1"><i class="bi bi-arrow-bar-down"></i> + Row Below</button>
                                        <button type="button" @click="addColumn(false)" class="btn btn-xs btn-outline btn-info gap-1"><i class="bi bi-arrow-bar-left"></i> + Col Left</button>
                                        <button type="button" @click="addColumn(true)" class="btn btn-xs btn-outline btn-info gap-1"><i class="bi bi-arrow-bar-right"></i> + Col Right</button>
                                        <div class="w-px h-4 bg-base-300 mx-1"></div>
                                        <button type="button" @click="deleteRow()" class="btn btn-xs btn-outline btn-error gap-1"><i class="bi bi-trash"></i> Row</button>
                                        <button type="button" @click="deleteColumn()" class="btn btn-xs btn-outline btn-error gap-1"><i class="bi bi-trash"></i> Col</button>
                                        <button type="button" @click="deleteTable()" class="btn btn-xs btn-error gap-1 ml-auto"><i class="bi bi-trash3-fill"></i> Delete Table</button>
                                    </div>
                                </template>

                                <!-- DYNAMIC IMAGE SUB-TOOLBAR (Visible only when image is focused) -->
                                <template x-if="selectedImg">
                                    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-success/10 border-t border-base-300 text-xs text-success-content">
                                        <span class="font-semibold mr-1"><i class="bi bi-image mr-1"></i> Image Settings:</span>
                                        <button type="button" @click="resizeImg(25)" class="btn btn-xs btn-outline btn-success">25% Width</button>
                                        <button type="button" @click="resizeImg(50)" class="btn btn-xs btn-outline btn-success">50% Width</button>
                                        <button type="button" @click="resizeImg(75)" class="btn btn-xs btn-outline btn-success">75% Width</button>
                                        <button type="button" @click="resizeImg(100)" class="btn btn-xs btn-outline btn-success">100% Width</button>
                                        <div class="w-px h-4 bg-base-300 mx-1"></div>
                                        <button type="button" @click="alignImg('left')" class="btn btn-xs btn-outline btn-success gap-1"><i class="bi bi-text-left"></i> Align Left</button>
                                        <button type="button" @click="alignImg('center')" class="btn btn-xs btn-outline btn-success gap-1"><i class="bi bi-text-center"></i> Align Center</button>
                                        <button type="button" @click="alignImg('right')" class="btn btn-xs btn-outline btn-success gap-1"><i class="bi bi-text-right"></i> Align Right</button>
                                        <div class="w-px h-4 bg-base-300 mx-1"></div>
                                        <button type="button" @click="deleteImg()" class="btn btn-xs btn-error gap-1 ml-auto"><i class="bi bi-trash"></i> Delete Image</button>
                                    </div>
                                </template>
                            </div>

                            <!-- CANVAS -->
                            <div 
                                x-ref="editorCanvas"
                                contenteditable="true"
                                @click="handleEditorClick($event)"
                                @mousedown="handleImgMouseDown($event)"
                                @mousemove="handleImgMouseMove($event)"
                                @blur="syncContent();"
                                @keyup="syncContent(); handleEditorClick($event);"
                                @paste="setTimeout(() => syncContent(), 10)"
                                class="p-5 min-h-[400px] outline-none bg-base-100 ql-editor overflow-y-auto rounded-b-lg"
                            ></div>
                        </div>
                    </div>

                    <!-- Selector specifically for Custom Editor insert content (placed outside wire:ignore) -->
                    <livewire:admin.components.media-selector target-field="custom_editor_insert" folder="blog_posts" headless="true" />

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
