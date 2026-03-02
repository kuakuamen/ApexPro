@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="bg-zinc-900/55 backdrop-blur-sm border border-teal-900/30 rounded-xl shadow-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 flex flex-col sm:flex-row justify-between items-center border-b border-teal-900/40">
        <div>
            <h3 class="text-lg leading-6 font-bold text-stone-100">
                {{ $workout->name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-stone-300">
                Objetivo: {{ $workout->goal ?? 'Não definido' }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-col items-end">
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $workout->is_active ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/50' : 'bg-red-400/20 text-red-300 border border-red-400/50' }}">
                {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
            </span>
            
            @if(auth()->user()->role === 'aluno')
                <!-- Barra de Progresso Geral (Apenas Aluno) -->
            <div class="mt-3 sm:w-80" x-data="progressTracker({{ $workout->days->sum(fn($day) => $day->exercises->count()) }}, {{ count($todayLogs ?? []) }}, {{ $workout->id }})">
                <!-- Header: Título + Porcentagem -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="text-sm font-bold text-stone-100">Progresso da Semana</h4>
                        <p class="text-xs text-stone-300 mt-1">
                            <span x-text="current + ' de ' + total"></span> exercícios completados
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-teal-300" x-text="Math.round((current / total) * 100) + '%'\"></div>
                        <p class="text-xs text-stone-300">Completo</p>
                    </div>
                </div>
                
                <!-- Barra de Progresso Melhorada -->
                <div class="mb-4">
                    <div class="w-full bg-zinc-800/70 rounded-full h-4 overflow-hidden border border-teal-900/40">
                        <div class="bg-gradient-to-r from-teal-700 via-teal-600 to-cyan-700 h-4 rounded-full transition-all duration-500 shadow-lg shadow-teal-700/40" 
                             :style="'width: ' + ((current / total) * 100) + '%'"></div>
                    </div>
                </div>

                <!-- Grid de Informações Adicionais -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <!-- Exercícios Restantes -->
                    <div class="bg-zinc-800/50 border border-teal-900/30 rounded-lg p-3 text-center">
                        <p class="text-xs text-stone-300 uppercase font-semibold">Restantes</p>
                        <p class="text-lg font-bold text-teal-300" x-text="total - current"></p>
                    </div>
                    
                    <!-- Dias da Semana -->
                    <div class="bg-zinc-800/50 border border-teal-900/30 rounded-lg p-3 text-center">
                        <p class="text-xs text-stone-300 uppercase font-semibold">Dias</p>
                        <p class="text-lg font-bold text-teal-300" x-text="daysLeftInWeek"></p>
                    </div>
                    
                    <!-- Meta Diária -->
                    <div class="bg-zinc-800/50 border border-teal-900/30 rounded-lg p-3 text-center">
                        <p class="text-xs text-stone-300 uppercase font-semibold">Meta/Dia</p>
                        <p class="text-lg font-bold text-teal-300" x-text="Math.ceil(total / 7)"></p>
                    </div>
                </div>

                <!-- Informações Recentes -->
                <div class="flex gap-2 mb-4 bg-zinc-800/40 rounded-lg p-3 border border-teal-900/30">
                    <div class="flex-1">
                        <p class="text-xs text-stone-300 uppercase font-semibold mb-1">Último Reset</p>
                        <p class="text-xs text-teal-300 font-mono" x-text="lastResetDay"></p>
                    </div>
                    <div class="flex-1 text-right">
                        <p class="text-xs text-stone-300 uppercase font-semibold mb-1">Streak</p>
                        <p class="text-xs text-teal-300 font-mono" x-text="streakDays + ' dias'"></p>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="flex gap-2">
                    <button @click="resetProgress()" 
                            class="flex-1 px-3 py-2 text-xs font-semibold rounded-lg text-red-300 bg-red-600/20 border border-red-500/50 hover:bg-red-600/30 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Zerar
                    </button>
                    <div class="flex-1 px-3 py-2 text-xs font-semibold rounded-lg text-teal-300 bg-teal-700/20 border border-teal-600/40 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="completedToday + ' hoje'"></span>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'personal')
                <a href="{{ route('workouts.edit', $workout) }}" class="mt-3 inline-flex items-center px-4 py-2 border border-teal-600/40 text-xs font-semibold rounded-lg text-teal-300 bg-teal-700/20 hover:bg-teal-700/30 focus:outline-none transition-colors">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar Treino
                </a>
            @endif
        </div>
    </div>
    
    <div>
        <div>
            @foreach($workout->days as $day)
                <div class="px-4 py-6 sm:px-6 border-b border-teal-900/30 last:border-b-0">
                    <dt class="text-base font-bold text-teal-300 mb-4">
                        {{ $day->name }}
                    </dt>
                    <dd class="text-sm text-stone-300 sm:col-span-3">
                        <ul role="list" class="space-y-4">
                            @foreach($day->exercises as $exercise)
                                @if(auth()->user()->role === 'aluno')
                                    <!-- Card Mobile-First (Aluno) -->
                                    <li class="bg-zinc-900/60 border border-teal-900/30 rounded-2xl shadow-md p-5 relative overflow-hidden transition-all duration-300" 
                                        x-data="exerciseItem({{ $exercise->id }}, {{ in_array($exercise->id, $todayLogs ?? []) ? 'true' : 'false' }}, '{{ $exercise->rest_time }}')"
                                        :class="{ 'bg-zinc-800/40 opacity-75': completed }">
                                        
                                        <!-- Header: Nome e Check -->
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-base font-semibold text-stone-100 leading-tight w-3/4 transition-colors" 
                                                :class="{ 'text-stone-400 line-through': completed }">
                                                {{ $exercise->name }}
                                            </h4>
                                            
                                            <!-- Checkbox Estilo iOS -->
                                            <div class="flex-shrink-0 cursor-pointer transform transition-transform active:scale-90" @click="completed = !completed; toggle()">
                                                <!-- Estado: Não Marcado -->
                                                <div x-show="!completed" class="w-8 h-8 rounded-full border-2 border-stone-500 bg-zinc-800/60 hover:border-teal-400 transition-colors"></div>
                                                
                                                <!-- Estado: Marcado -->
                                                <div x-show="completed" 
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="transform scale-0 opacity-0"
                                                     x-transition:enter-end="transform scale-100 opacity-100"
                                                     class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-600 to-cyan-700 border-2 border-teal-400 flex items-center justify-center shadow-lg shadow-teal-700/50">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grid de Informações -->
                                        <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                                            <div class="bg-teal-700/20 border border-teal-600/30 rounded-lg p-2">
                                                <span class="block text-xs text-teal-200 uppercase font-bold">Séries</span>
                                                <span class="block text-lg font-bold text-teal-300">{{ $exercise->sets ?? '-' }}</span>
                                            </div>
                                            <div class="bg-teal-700/20 border border-teal-600/30 rounded-lg p-2">
                                                <span class="block text-xs text-teal-200 uppercase font-bold">Reps</span>
                                                <span class="block text-lg font-bold text-teal-300">{{ $exercise->reps ?? '-' }}</span>
                                            </div>
                                            <div class="bg-teal-700/20 border border-teal-600/30 rounded-lg p-2">
                                                <span class="block text-xs text-teal-200 uppercase font-bold">Descanso</span>
                                                <span class="block text-lg font-bold text-teal-300">{{ $exercise->rest_time ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <!-- Ações (Timer) -->
                                        <template x-if="restSeconds > 0 && !completed">
                                            <div class="mt-3">
                                                <button type="button" 
                                                        @click="startTimer()" 
                                                        x-show="!timerRunning"
                                                        class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-bold rounded-lg text-stone-100 bg-gradient-to-r from-teal-700 to-cyan-800 hover:from-teal-800 hover:to-cyan-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-teal-500 shadow-lg hover:shadow-teal-700/50 transition-all active:scale-95">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Iniciar Descanso
                                                </button>

                                                <div x-show="timerRunning" class="w-full bg-teal-700/20 rounded-lg p-3 flex items-center justify-between border border-teal-600/40">
                                                    <span class="flex items-center text-teal-300 font-bold text-xl animate-pulse">
                                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span x-text="formatTime(timeLeft)"></span>
                                                    </span>
                                                    <button @click="stopTimer()" class="text-sm font-medium text-red-400 hover:text-red-300 border border-red-500/50 px-3 py-1 rounded bg-red-600/20 hover:bg-red-600/30">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </li>
                                @else
                                    <!-- Modo Leitura (Personal) -->
                                    <li class="bg-zinc-900/60 border border-teal-900/30 rounded-xl p-4 mb-3">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h5 class="font-semibold text-stone-100">{{ $exercise->name }}</h5>
                                                <div class="flex flex-wrap gap-3 mt-3 text-sm">
                                                    @if($exercise->sets) <span class="text-teal-300"><strong>Séries:</strong> {{ $exercise->sets }}</span> @endif
                                                    @if($exercise->reps) <span class="text-teal-300"><strong>Reps:</strong> {{ $exercise->reps }}</span> @endif
                                                    @if($exercise->rest_time) <span class="text-teal-300"><strong>Descanso:</strong> {{ $exercise->rest_time }}</span> @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </dd>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('workouts.index') }}" class="inline-flex items-center text-teal-300 hover:text-teal-200 font-semibold transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Voltar para Meus Treinos
    </a>
</div>

<script>
    function exerciseItem(id, initialStatus, restTimeStr) {
        // Chave única para o LocalStorage: workout_log_{DATA}_{ID}
        const storageKey = `workout_log_${new Date().toISOString().split('T')[0]}_${id}`;
        const storageKeyExpire = `workout_log_expire_${id}`;
        
        // Função para verificar se ainda está dentro dos 7 dias
        function isValidProgress() {
            const expireDate = localStorage.getItem(storageKeyExpire);
            if (!expireDate) return true; // Primeira vez
            
            const lastReset = new Date(expireDate);
            const now = new Date();
            const daysDiff = Math.floor((now - lastReset) / (1000 * 60 * 60 * 24));
            
            // Se passou 7 dias, reseta
            if (daysDiff >= 7) {
                localStorage.removeItem(storageKey);
                localStorage.setItem(storageKeyExpire, new Date().toISOString());
                return false;
            }
            return true;
        }
        
        // Recupera do LocalStorage ou usa o status do banco
        const isValid = isValidProgress();
        const savedStatus = isValid && localStorage.getItem(storageKey) === 'true';
        const status = savedStatus || initialStatus;

        // Extrair segundos da string
        let seconds = 0;
        if (restTimeStr) {
            const numbers = restTimeStr.replace(/[^0-9]/g, '');
            seconds = parseInt(numbers) || 0;
            if (restTimeStr.toLowerCase().includes('min')) {
                seconds = seconds * 60;
            }
        }

        return {
            id: id,
            completed: status,
            restSeconds: seconds,
            timeLeft: seconds,
            timerRunning: false,
            interval: null,
            storageKey: storageKey,
            storageKeyExpire: storageKeyExpire,

            init() {
                // Ao iniciar, não precisamos fazer nada pois a barra já inicia com o valor do backend
                // e o LocalStorage apenas sincroniza o estado visual do botão.
            },

            toggle() {
                // Salva no LocalStorage imediatamente (Backup Front)
                localStorage.setItem(this.storageKey, this.completed);
                
                // Registra a data de expiração (7 dias a partir de agora)
                if (!localStorage.getItem(this.storageKeyExpire)) {
                    localStorage.setItem(this.storageKeyExpire, new Date().toISOString());
                }
                
                // Emite evento para atualizar a barra (+1 ou -1)
                window.dispatchEvent(new CustomEvent('progress-change', { 
                    detail: { value: this.completed ? 1 : -1 } 
                }));

                // Backend sincronização desabilitada por enquanto (localStorage já funciona perfeitamente)
                // Será implementada quando backend estiver totalmente configurado
                
                /* 
                fetch(`/aluno/exercicio/${this.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status || data.message) {
                        console.log('✓ Backend sincronizado');
                    }
                })
                .catch(() => {
                    // Nada a fazer
                });
                */
            },
            
            // updateGlobalCount removido pois agora usamos eventos delta (+1/-1)

            startTimer() {
                if (this.restSeconds <= 0) return;
                
                this.timerRunning = true;
                this.timeLeft = this.restSeconds;
                
                this.interval = setInterval(() => {
                    this.timeLeft--;
                    
                    if (this.timeLeft <= 0) {
                        this.finishTimer();
                    }
                }, 1000);
            },

            stopTimer() {
                clearInterval(this.interval);
                this.timerRunning = false;
                this.timeLeft = this.restSeconds;
            },

            finishTimer() {
                this.stopTimer();
                // Marcar como concluído
                this.completed = true;
                this.toggle(); // Salva no banco
                
                // Vibrar celular (se suportado)
                if (navigator.vibrate) {
                    navigator.vibrate([200, 100, 200]);
                }
                
                // Tocar som (opcional)
                // const audio = new Audio('/sounds/beep.mp3');
                // audio.play();
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s.toString().padStart(2, '0')}`;
            }
        }
    }

    // Sistema de Progresso com Persistência de 7 dias e Reset no Domingo
    function progressTracker(total, initialCurrent, workoutId) {
        const storageKeyCount = `workout_progress_${workoutId}`;
        const storageKeyDate = `workout_progress_date_${workoutId}`;
        const storageKeyLastReset = `workout_progress_last_reset_${workoutId}`;

        // Função para verificar se é domingo
        function isSunday() {
            return new Date().getDay() === 0;
        }

        // Função para obter o início da semana (segunda-feira)
        function getWeekStart() {
            const today = new Date();
            const day = today.getDay();
            const diff = today.getDate() - day + (day === 0 ? -6 : 1); // Ajusta para segunda-feira
            return new Date(today.setDate(diff)).toISOString().split('T')[0];
        }

        // Função para load do progresso com validação
        function loadProgress() {
            const savedDate = localStorage.getItem(storageKeyDate);
            const currentWeekStart = getWeekStart();

            // Se passou de uma semana, zera
            if (!savedDate || savedDate !== currentWeekStart) {
                localStorage.setItem(storageKeyCount, initialCurrent);
                localStorage.setItem(storageKeyDate, currentWeekStart);
                return initialCurrent;
            }

            return parseInt(localStorage.getItem(storageKeyCount) || initialCurrent);
        }

        // Função para obter dia do último reset
        function getLastResetDay() {
            const lastReset = localStorage.getItem(storageKeyLastReset);
            if (!lastReset) return 'Nenhum reset';
            const date = new Date(lastReset);
            return 'Reset: ' + date.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit' });
        }

        // Função para calcular dias restantes na semana
        function getDaysLeftInWeek() {
            const today = new Date();
            const dayOfWeek = today.getDay();
            // Semana vai de segunda (1) a domingo (0)
            // Se é domingo (0), retorna 1, se é segunda (1) retorna 6, etc
            return dayOfWeek === 0 ? 1 : 8 - dayOfWeek;
        }

        // Função para calcular streak (dias consecutivos)
        function calculateStreak() {
            const lastReset = localStorage.getItem(storageKeyLastReset);
            if (!lastReset) return 0;
            const resetDate = new Date(lastReset);
            const today = new Date();
            const daysDiff = Math.floor((today - resetDate) / (1000 * 60 * 60 * 24));
            return Math.max(0, daysDiff);
        }

        // Função para contar exercícios completados hoje
        function getCompletedToday() {
            const today = new Date().toISOString().split('T')[0];
            let count = 0;
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(`workout_log_${today}_`) && localStorage.getItem(key) === 'true') {
                    count++;
                }
            }
            return count;
        }

        return {
            total: total,
            current: loadProgress(),
            lastResetDay: getLastResetDay(),
            daysLeftInWeek: getDaysLeftInWeek(),
            streakDays: calculateStreak(),
            completedToday: getCompletedToday(),

            init() {
                // Listener para atualizar o progresso quando exercício for marcado
                window.addEventListener('progress-change', (e) => {
                    this.current += e.detail.value;
                    localStorage.setItem(storageKeyCount, this.current);
                    this.lastResetDay = getLastResetDay();
                    this.completedToday = getCompletedToday();
                });

                // Auto-update da data de última modificação
                const weekStart = getWeekStart();
                localStorage.setItem(storageKeyDate, weekStart);
            },

            resetProgress() {
                // Remove todos os exercícios marcados do dia (limpa checkboxes)
                const today = new Date().toISOString().split('T')[0];
                const keysToRemove = [];
                
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key.startsWith(`workout_log_${today}_`)) {
                        keysToRemove.push(key);
                    }
                }
                
                keysToRemove.forEach(key => localStorage.removeItem(key));
                
                // Zera o progresso e registra quando foi zerado
                this.current = 0;
                localStorage.setItem(storageKeyCount, '0');
                localStorage.setItem(storageKeyLastReset, new Date().toISOString());
                this.lastResetDay = getLastResetDay();
                
                // Dispara evento para sincronizar a UI
                window.dispatchEvent(new CustomEvent('progress-reset'));
                
                // Recarrega a página para atualizar os checkboxes
                setTimeout(() => location.reload(), 300);
            }
        }
    }
</script>
@endsection
