@php
    $isEdit = isset($sp);
    $action = $isEdit ? route('personal.financial.student-plans.update', $sp) : route('personal.financial.student-plans.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $old = fn($field, $default = '') => old($field, $isEdit ? $sp->$field : $default);

    $defaultDueDate = $isEdit
        ? $sp->due_date->format('Y-m-d')
        : now()->addMonth()->format('Y-m-d');
@endphp

<div class="py-6 max-w-2xl mx-auto space-y-5">

    @include('personal.financial._nav', ['activeTab' => 'vinculos'])

    <div class="flex items-center gap-3">
        <a href="{{ route('personal.financial.student-plans') }}" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-colors" title="Voltar para Vínculos">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-slate-100">{{ $isEdit ? 'Editar Vínculo' : 'Vincular Plano ao Aluno' }}</h1>
    </div>

    @if($errors->has('student_id'))
    <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-orange-200 text-sm font-medium">Vínculo não permitido</p>
            <p class="text-orange-300/80 text-xs mt-0.5">{{ $errors->first('student_id') }}</p>
            <a href="{{ route('personal.financial.student-plans') }}" class="inline-flex items-center gap-1 text-xs text-orange-400 hover:text-orange-200 mt-2 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                Ir para Vínculos e editar o plano existente
            </a>
        </div>
    </div>
    @elseif($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- ── Formulário ── --}}
    <div x-data="{
            isEdit: {{ $isEdit ? 'true' : 'false' }},
            periodicity: '{{ $old('periodicity','monthly') }}',
            startDate: '{{ $old('start_date', now()->format('Y-m-d')) }}',
            dueDate: '{{ old('due_date', $defaultDueDate) }}',
            showModal: false,
            payMethod: 'pix',
            skipPayment: false,
            discountType: 'none',
            discountValue: '',

            init() {
                this.$watch('startDate',  () => this.recalcDue());
                this.$watch('periodicity', () => this.recalcDue());
            },

            recalcDue() {
                if (!this.startDate || this.periodicity === 'custom') return;
                const d = new Date(this.startDate + 'T00:00:00');
                if      (this.periodicity === 'monthly')    d.setMonth(d.getMonth() + 1);
                else if (this.periodicity === 'quarterly')  d.setMonth(d.getMonth() + 3);
                else if (this.periodicity === 'semiannual') d.setMonth(d.getMonth() + 6);
                else if (this.periodicity === 'annual')     d.setFullYear(d.getFullYear() + 1);
                this.dueDate = d.toISOString().slice(0,10);
            },

            trySubmit() {
                if (this.isEdit) {
                    this.$refs.form.submit();
                } else {
                    this.showModal = true;
                }
            },

            confirmAndSubmit() {
                this.showModal = false;
                this.$refs.form.submit();
            }
        }">

        <form x-ref="form" method="POST" action="{{ $action }}" class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-6 space-y-5"
              @submit.prevent="trySubmit()">
            @csrf @method($method)

            {{-- Hidden: forma de pagamento para o controller --}}
            @if(!$isEdit)
                <input type="hidden" name="payment_method" :value="skipPayment ? '' : payMethod">
                <input type="hidden" name="skip_payment" :value="skipPayment ? '1' : '0'">
                <input type="hidden" name="discount_type" :value="discountType">
                <input type="hidden" name="discount_value" :value="discountValue">
            @endif

            {{-- Aluno --}}
            @if(!$isEdit)
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Aluno *</label>
                <select name="student_id" required class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="">Selecione o aluno</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="bg-slate-800/40 rounded-xl px-4 py-3">
                <p class="text-xs text-slate-400 mb-0.5">Aluno</p>
                <p class="text-slate-100 font-medium">{{ $sp->student->name }}</p>
            </div>
            @endif

            {{-- Plano --}}
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Plano *</label>
                <select name="financial_plan_id" required class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="">Selecione o plano</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->id }}" {{ $old('financial_plan_id', $isEdit ? $sp->financial_plan_id : '') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} — R$ {{ number_format($p->price, 2, ',', '.') }} ({{ $p->periodicityLabel() }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Data de Início + Periodicidade --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Data de Início *</label>
                    <input type="date" name="start_date" x-model="startDate" required
                        class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Periodicidade *</label>
                    <select name="periodicity" x-model="periodicity" required
                        class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                        <option value="monthly">Mensal</option>
                        <option value="quarterly">Trimestral</option>
                        <option value="semiannual">Semestral</option>
                        <option value="annual">Anual</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
            </div>

            {{-- Dias personalizados --}}
            <div x-show="periodicity === 'custom'" x-cloak>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Quantidade de dias *</label>
                <input type="number" name="custom_days" value="{{ $old('custom_days') }}" min="1"
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>

            {{-- Data de Vencimento (editável) --}}
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Data de Vencimento *</label>
                <input type="date" name="due_date" x-model="dueDate" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                <p class="text-xs text-slate-500 mt-1">Preenchido automaticamente com base na periodicidade. Edite se necessário.</p>
            </div>

            {{-- Status (apenas edição) --}}
            @if($isEdit)
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Status *</label>
                <select name="status" required class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="active"    {{ $sp->status === 'active'    ? 'selected' : '' }}>Ativo</option>
                    <option value="overdue"   {{ $sp->status === 'overdue'   ? 'selected' : '' }}>Atrasado</option>
                    <option value="suspended" {{ $sp->status === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                </select>
            </div>
            @endif

            {{-- Botões --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('personal.financial.student-plans') }}" class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200 transition-colors">Cancelar</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors">
                    {{ $isEdit ? 'Salvar Alterações' : 'Vincular Plano' }}
                </button>
            </div>
        </form>

        {{-- ── Modal de Pagamento (apenas create) ── --}}
        @if(!$isEdit)
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-black/60" @click="showModal = false"></div>

            <div class="relative bg-[#0f1a2e] border border-slate-700/60 rounded-2xl p-6 w-full max-w-sm shadow-2xl"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/15 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">Pagamento de Entrada</p>
                        <p class="text-xs text-slate-400">Selecione a forma de pagamento</p>
                    </div>
                </div>

                {{-- Info fixa --}}
                <div x-show="!skipPayment" class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-4 py-3 mb-4 space-y-1">
                    <p class="text-xs text-emerald-300 font-medium">O pagamento de hoje será registrado automaticamente como <strong>Pago</strong>.</p>
                    <p class="text-xs text-slate-400">O próximo vencimento (<span class="text-slate-200" x-text="dueDate ? new Date(dueDate + 'T00:00:00').toLocaleDateString('pt-BR') : ''"></span>) ficará como <strong>Pendente</strong>.</p>
                </div>
                <div x-show="skipPayment" x-cloak class="bg-orange-500/10 border border-orange-500/20 rounded-xl px-4 py-3 mb-4 space-y-1">
                    <p class="text-xs text-orange-300 font-medium">O mês atual ficará registrado como <strong>Pendente</strong>.</p>
                    <p class="text-xs text-slate-400">O próximo vencimento (<span class="text-slate-200" x-text="dueDate ? new Date(dueDate + 'T00:00:00').toLocaleDateString('pt-BR') : ''"></span>) também ficará como <strong>Pendente</strong>.</p>
                </div>

                {{-- Toggle: Pagamento não realizado --}}
                <div class="mb-4">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" class="sr-only" x-model="skipPayment">
                            <div :class="skipPayment ? 'bg-orange-500' : 'bg-slate-700'" class="w-9 h-5 rounded-full transition-colors"></div>
                            <div :class="skipPayment ? 'translate-x-4' : 'translate-x-0.5'" class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform"></div>
                        </div>
                        <span class="text-xs font-medium text-slate-300">Pagamento não realizado</span>
                    </label>
                </div>

                {{-- Forma de pagamento --}}
                <div class="mb-4" x-show="!skipPayment">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Forma de Pagamento</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['pix' => 'Pix', 'card' => 'Cartão', 'cash' => 'Dinheiro', 'other' => 'Outro'] as $val => $lbl)
                        <button type="button" @click="payMethod = '{{ $val }}'"
                            :class="payMethod === '{{ $val }}'
                                ? 'bg-teal-600/80 border-teal-500 text-white'
                                : 'bg-slate-800/60 border-slate-700/50 text-slate-300 hover:border-teal-500/40'"
                            class="border rounded-xl px-3 py-2 text-xs font-medium transition-colors">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Desconto --}}
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Desconto</label>
                    <div class="flex gap-2">
                        <select x-model="discountType" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 shrink-0">
                            <option value="none">Sem desconto</option>
                            <option value="fixed">R$ fixo</option>
                            <option value="percent">Percentual</option>
                        </select>
                        <input x-show="discountType !== 'none'" x-cloak
                            type="number" min="0" step="0.01"
                            x-model="discountValue"
                            :placeholder="discountType === 'percent' ? 'Ex: 10 (%)' : 'Ex: 20,00'"
                            class="flex-1 bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    </div>
                </div>

                {{-- Ações --}}
                <div class="flex gap-3">
                    <button type="button" @click="showModal = false"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-slate-400 hover:text-slate-200 bg-slate-800/50 hover:bg-slate-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" @click="confirmAndSubmit()"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-white font-medium bg-emerald-600 hover:bg-emerald-700 transition-colors">
                        Confirmar e Vincular
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /x-data --}}
</div>
