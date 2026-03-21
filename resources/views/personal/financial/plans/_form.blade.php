@php
    $action = isset($plan) ? route('personal.financial.plans.update', $plan) : route('personal.financial.plans.store');
    $method = isset($plan) ? 'PUT' : 'POST';
    $old = fn($field, $default = '') => old($field, isset($plan) ? $plan->$field : $default);
@endphp

<div class="py-6 max-w-2xl mx-auto space-y-5">

    @include('personal.financial._nav', ['activeTab' => 'plans'])

    <div class="flex items-center gap-3">
        <a href="{{ route('personal.financial.plans') }}" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-colors" title="Voltar para Planos">
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

            <div x-data="{
                    periodicity: '{{ $old('periodicity', 'monthly') }}',
                    periodicityOpen: false,
                    periodicityOpts: [
                        {value:'monthly',    label:'Mensal'},
                        {value:'quarterly',  label:'Trimestral'},
                        {value:'semiannual', label:'Semestral'},
                        {value:'annual',     label:'Anual'},
                        {value:'custom',     label:'Personalizado'}
                    ]
                }" @click.outside="periodicityOpen = false">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Periodicidade *</label>
                <input type="hidden" name="periodicity" :value="periodicity">
                <div class="relative">
                    <button type="button" @click="periodicityOpen = !periodicityOpen"
                        class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-4 py-2.5 text-sm text-slate-100 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-colors">
                        <span x-text="periodicityOpts.find(o => o.value === periodicity)?.label ?? 'Mensal'"></span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="periodicityOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="periodicityOpen" x-cloak class="absolute z-20 mt-1 w-full bg-[#0f1a2e] border border-slate-700/60 rounded-xl shadow-2xl overflow-hidden">
                        <ul>
                            <template x-for="o in periodicityOpts" :key="o.value">
                                <li @click="periodicity = o.value; periodicityOpen = false"
                                    :class="periodicity === o.value ? 'bg-emerald-600/20 text-emerald-300' : 'text-slate-200 hover:bg-slate-700/50'"
                                    class="px-4 py-2.5 text-sm cursor-pointer transition-colors" x-text="o.label">
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
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
