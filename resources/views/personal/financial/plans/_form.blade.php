@php
    $action = isset($plan) ? route('personal.financial.plans.update', $plan) : route('personal.financial.plans.store');
    $method = isset($plan) ? 'PUT' : 'POST';
    $old = fn($field, $default = '') => old($field, isset($plan) ? $plan->$field : $default);
@endphp

<div class="py-6 max-w-2xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('personal.financial.plans') }}" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-slate-100">{{ isset($plan) ? 'Editar Plano' : 'Novo Plano' }}</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-6 space-y-5">
        @csrf @method($method)

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Nome do Plano *</label>
                <input type="text" name="name" value="{{ $old('name') }}" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 placeholder-slate-500"
                    placeholder="Ex: Consultoria Mensal">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Valor (R$) *</label>
                <input type="number" name="price" value="{{ $old('price') }}" min="0" step="0.01" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>

            <div x-data="{ periodicity: '{{ $old('periodicity', 'monthly') }}' }">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Periodicidade *</label>
                <select name="periodicity" x-model="periodicity" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="monthly">Mensal</option>
                    <option value="quarterly">Trimestral</option>
                    <option value="semiannual">Semestral</option>
                    <option value="annual">Anual</option>
                    <option value="custom">Personalizado</option>
                </select>
                <div x-show="periodicity === 'custom'" x-cloak class="mt-3">
                    <label class="block text-xs text-slate-400 mb-1">Quantidade de dias *</label>
                    <input type="number" name="custom_days" value="{{ $old('custom_days') }}" min="1"
                        class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Descrição</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 placeholder-slate-500 resize-none"
                    placeholder="Detalhes do plano...">{{ $old('description') }}</textarea>
            </div>

            @if(isset($plan))
            <div class="sm:col-span-2 flex items-center gap-3">
                <input type="checkbox" name="active" id="active" value="1" {{ $old('active', $plan->active ? '1' : '') == '1' ? 'checked' : '' }}
                    class="w-4 h-4 rounded text-emerald-500 bg-slate-700 border-slate-600">
                <label for="active" class="text-sm text-slate-300">Plano ativo</label>
            </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('personal.financial.plans') }}" class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200 transition-colors">Cancelar</a>
            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors">
                {{ isset($plan) ? 'Salvar Alterações' : 'Criar Plano' }}
            </button>
        </div>
    </form>
</div>
