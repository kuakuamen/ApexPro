<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anamnese Nutricional</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
@php
    $form = old('anamnesis', $anamnesis ?? []);
    $selected = fn(string $key) => old("anamnesis.$key", $form[$key] ?? '');
    $selectedArray = fn(string $key) => old("anamnesis.$key", $form[$key] ?? []);
@endphp

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-700 bg-slate-900/80">
            <h1 class="text-2xl font-bold">Anamnese Nutricional</h1>
            <p class="text-slate-300 text-sm mt-1">Aluno: <strong>{{ $student->name }}</strong></p>
            <p class="text-slate-400 text-xs mt-1">Preencha este formulario para que {{ $professional->name }} monte seu plano alimentar com mais precisao.</p>
        </div>

        <form method="POST" action="{{ request()->fullUrl() }}" class="p-6 space-y-7">
            @csrf

            @if($errors->any())
                <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    Corrija os campos obrigatorios e tente novamente.
                </div>
            @endif

            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-cyan-300">Objetivo e corpo</h2>
                <div>
                    <label class="text-sm text-slate-300">Objetivo principal</label>
                    <select name="anamnesis[main_goal]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                        <option value="">Selecione...</option>
                        @foreach($options['goalOptions'] as $opt)
                            <option value="{{ $opt }}" {{ $selected('main_goal') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Peso atual (kg)</label>
                        <input type="number" step="0.1" name="anamnesis[weight_kg]" value="{{ $selected('weight_kg') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Altura (cm)</label>
                        <input type="number" step="0.1" name="anamnesis[height_cm]" value="{{ $selected('height_cm') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Peso desejado (kg)</label>
                        <input type="number" step="0.1" name="anamnesis[target_weight_kg]" value="{{ $selected('target_weight_kg') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-cyan-300">Saude</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-slate-300 mb-2">Doenca diagnosticada</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            @foreach($options['diseaseOptions'] as $opt)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="anamnesis[diagnosed_conditions][]" value="{{ $opt }}"
                                        {{ in_array($opt, $selectedArray('diagnosed_conditions'), true) ? 'checked' : '' }}>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <input type="text" name="anamnesis[diagnosed_conditions_other]" value="{{ $selected('diagnosed_conditions_other') }}" placeholder="Outra doenca" class="mt-2 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Medicamento continuo</label>
                        <textarea name="anamnesis[continuous_medication]" rows="5" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">{{ $selected('continuous_medication') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-cyan-300">Restricoes e alergias</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-slate-300 mb-2">Restricao/intolerancia</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            @foreach($options['restrictionOptions'] as $opt)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="anamnesis[food_restrictions][]" value="{{ $opt }}"
                                        {{ in_array($opt, $selectedArray('food_restrictions'), true) ? 'checked' : '' }}>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <input type="text" name="anamnesis[food_restrictions_other]" value="{{ $selected('food_restrictions_other') }}" placeholder="Outra restricao" class="mt-2 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <p class="text-sm text-slate-300 mb-2">Alergia alimentar</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            @foreach($options['allergyOptions'] as $opt)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="anamnesis[food_allergies][]" value="{{ $opt }}"
                                        {{ in_array($opt, $selectedArray('food_allergies'), true) ? 'checked' : '' }}>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <input type="text" name="anamnesis[food_allergies_other]" value="{{ $selected('food_allergies_other') }}" placeholder="Outra alergia" class="mt-2 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-cyan-300">Alimentacao e rotina</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Refeicoes por dia</label>
                        <input type="number" name="anamnesis[meals_per_day]" value="{{ $selected('meals_per_day') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Agua por dia (L)</label>
                        <input type="number" step="0.1" name="anamnesis[water_liters_per_day]" value="{{ $selected('water_liters_per_day') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Kcal/dia (opcional)</label>
                        <input type="number" name="anamnesis[kcal_day]" value="{{ $selected('kcal_day') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Come fora com frequencia?</label>
                        <select name="anamnesis[eats_out_frequency]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['eatsOutOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('eats_out_frequency') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Bebida alcoolica</label>
                        <select name="anamnesis[alcohol_frequency]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['alcoholOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('alcohol_frequency') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Estilo alimentar</label>
                        <select name="anamnesis[food_style]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['foodStyleOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('food_style') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Outro estilo (se houver)</label>
                        <input type="text" name="anamnesis[food_style_other]" value="{{ $selected('food_style_other') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm" placeholder="Descreva aqui">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Alimentos que nao consegue comer</label>
                        <textarea name="anamnesis[disliked_foods]" rows="3" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">{{ $selected('disliked_foods') }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Alimentos favoritos</label>
                        <textarea name="anamnesis[favorite_foods]" rows="3" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">{{ $selected('favorite_foods') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-cyan-300">Treino e comportamento</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Periodo de treino</label>
                        <select name="anamnesis[training_period]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['trainingPeriodOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('training_period') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Come antes do treino?</label>
                        <select name="anamnesis[pre_workout_meal]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['preWorkoutOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('pre_workout_meal') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Come apos o treino?</label>
                        <select name="anamnesis[post_workout_meal]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['postWorkoutOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('post_workout_meal') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Come por ansiedade/estresse?</label>
                        <select name="anamnesis[emotional_eating]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['emotionalEatingOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('emotional_eating') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Historico com dieta</label>
                        <select name="anamnesis[diet_history]" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                            <option value="">Selecione...</option>
                            @foreach($options['dietHistoryOptions'] as $opt)
                                <option value="{{ $opt }}" {{ $selected('diet_history') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">Horario com mais fome</label>
                        <input type="time" name="anamnesis[most_hungry_time]" value="{{ $selected('most_hungry_time') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Horario com menos fome</label>
                        <input type="time" name="anamnesis[least_hungry_time]" value="{{ $selected('least_hungry_time') }}" class="mt-1 w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm">
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-5 py-3 text-sm font-semibold">
                    Enviar respostas
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
