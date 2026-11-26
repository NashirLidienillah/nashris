<div x-data="{
    open: false,
    title: 'Konfirmasi Tindakan',
    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmText: 'Konfirmasi',
    cancelText: 'Batal',
    type: 'danger', // danger (rose), success (emerald), primary (indigo), warning (amber)
    targetForm: null,
    targetFormId: null,

    openModal(detail) {
        this.title = detail.title || 'Konfirmasi Tindakan';
        this.message = detail.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        this.confirmText = detail.confirmText || (detail.type === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan');
        this.cancelText = detail.cancelText || 'Batal';
        this.type = detail.type || 'danger';
        this.targetForm = detail.form || null;
        this.targetFormId = detail.formId || null;
        this.open = true;
    },
    submitAction() {
        if (this.targetForm) {
            this.targetForm.submit();
        } else if (this.targetFormId) {
            const f = document.getElementById(this.targetFormId);
            if (f) f.submit();
        }
        this.open = false;
    }
}"
@open-confirm.window="openModal($event.detail)"
x-show="open"
x-cloak
@keydown.escape.window="open = false"
class="fixed inset-0 z-50 overflow-y-auto"
role="dialog"
aria-modal="true">

    <!-- Backdrop with soft blur -->
    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             @click.outside="open = false"
             class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-6 relative">

            <div class="flex items-start gap-4">
                <!-- Icon based on type -->
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     :class="{
                         'bg-rose-50 text-rose-600 border border-rose-100': type === 'danger',
                         'bg-emerald-50 text-emerald-600 border border-emerald-100': type === 'success',
                         'bg-amber-50 text-amber-600 border border-amber-100': type === 'warning',
                         'bg-indigo-50 text-indigo-600 border border-indigo-100': type === 'primary' || type === 'info'
                     }">
                    <template x-if="type === 'danger'">
                        <svg class="w-5 h-5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </template>
                    <template x-if="type === 'success'">
                        <svg class="w-5 h-5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="type === 'warning'">
                        <svg class="w-5 h-5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <template x-if="type === 'primary' || type === 'info'">
                        <svg class="w-5 h-5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-slate-900 leading-snug" x-text="title"></h3>
                    <p class="mt-1.5 text-xs text-slate-500 leading-relaxed" x-text="message"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button"
                        @click="open = false"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition active:scale-95"
                        x-text="cancelText">
                    Batal
                </button>
                <button type="button"
                        @click="submitAction()"
                        class="px-4 py-2.5 rounded-xl text-white text-xs font-semibold transition active:scale-95 shadow-xs flex items-center gap-1.5"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-rose-600/20': type === 'danger',
                            'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-emerald-600/20': type === 'success',
                            'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 shadow-amber-600/20': type === 'warning',
                            'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-indigo-600/20': type === 'primary' || type === 'info'
                        }">
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>
