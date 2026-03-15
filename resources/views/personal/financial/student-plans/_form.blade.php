@php
    $isEdit = isset($sp);
    $action = $isEdit ? route('personal.financial.student-plans.update', $sp) : route('personal.financial.student-plans.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $old = fn($field, $default = '') => old($field, $isEdit ? $sp->$field : $default);
@endphp

<div class="py-6 max-w-2xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('personal.financial.student-plans') }}" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-slate-100">{{ $isEdit ? 'Editar Vínculo' : 'Vincular Plano ao Aluno' }}</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-6 space-y-5"
          x-data="{
              periodicity: '{{ $old('periodicity','monthly') }}',
              startDate: '{{ $old('start_date', now()->format('Y-m-d')) }}',
              get dueDate() {
                  if (!this.startDate) return '';
                  const d = new Date(this.startDate);
                  if      (this.periodicity === 'monthly')    d.setMonth(d.getMonth() + 1);
                  else if (this.periodicity === 'quarterly')  d.setMonth(d.getMonth() + 3);
                  else if (this.periodicity === 'semiannual') d.setMonth(d.getMonth() + 6);
                  else if (this.periodicity === 'annual')     d.setFullYear(d.getFullYear() + 1);
                  return d.toISOString().slice(0,10);
              }
          }">
        @csrf @method($method)

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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Data de Início *</label>
                <input type="date" name="start_date" x-model="startDate" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Periodicidade de Cobrança *</label>
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

        <div x-show="periodicity === 'custom'" x-cloak>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Quantidade de dias *</label>
            <input type="number" name="custom_days" value="{{ $old('custom_days') }}" min="1"
                class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        </div>

        {{-- Preview do vencimento calculado --}}
        <div x-show="periodicity !== 'custom' && startDate" x-cloak
             class="flex items-center gap-2 bg-emerald-500/8 border border-emerald-500/20 rounded-xl px-4 py-3">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-emerald-300">
                Primeiro vencimento calculado:
                <span class="font-semibold" x-text="dueDate ? new Date(dueDate + 'T00:00:00').toLocaleDateString('pt-BR') : '—'"></span>
            </p>
        </div>

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

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('personal.financial.student-plans') }}" class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200 transition-colors">Cancelar</a>
            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors">
                {{ $isEdit ? 'Salvar Alterações' : 'Vincular Plano' }}
            </button>
        </div>
    </form>
</div>
