@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="relative rounded-2xl border bg-gray-900 p-6 sm:p-8 shadow-xl" style="border-color:#0f766e;">
        <div class="flex flex-col gap-4">
            <div class="min-w-0 sm:pr-[24rem]">
                <p class="text-xs font-semibold uppercase tracking-widest" style="color:#5eead4;">Painel do Personal</p>
                <h1 class="mt-2 text-3xl font-bold" style="color:#ffffff;">Olá, {{ auth()->user()->name }}!</h1>
                <p class="mt-2 max-w-2xl text-sm" style="color:#d1d5db;">
                    Acompanhe os alunos que precisam de atenção e acesse as ações principais sem sair da tela inicial.
                </p>
            </div>

            <div class="absolute flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end" style="top:24px;right:24px;">
                <a href="{{ route('personal.students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition" style="background:#0d9488;color:#ffffff;min-width:150px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" style="stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Novo Aluno
                </a>
                <a href="{{ route('personal.ai-assessment.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition" style="border-color:#14b8a6;background:#1f2937;color:#99f6e4;min-width:185px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" style="stroke:#99f6e4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Registrar Avaliação IA
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border bg-gray-800 p-4 shadow-lg" style="border-color:#334155;">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium uppercase tracking-wide" style="color:#d1d5db;">Total de Alunos</p>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" style="stroke:#67e8f9;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <p class="mt-2 text-3xl font-bold" style="color:#ffffff;">{{ $totalStudents }}</p>
            <p class="mt-2 text-xs" style="color:#d1d5db;">{{ $newThisMonth }} novos neste mês</p>
        </div>

        <div class="rounded-xl border bg-gray-800 p-4 shadow-lg" style="border-color:#334155;">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium uppercase tracking-wide" style="color:#d1d5db;">Alunos Ativos</p>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" style="stroke:#4ade80;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="mt-2 text-3xl font-bold" style="color:#4ade80;">{{ $activeStudents }}</p>
            <p class="mt-2 text-xs" style="color:#d1d5db;">{{ max($totalStudents - $activeStudents, 0) }} inativos</p>
        </div>

        <div class="rounded-xl border bg-gray-800 p-4 shadow-lg" style="border-color:#334155;">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium uppercase tracking-wide" style="color:#d1d5db;">Avaliações em Atraso</p>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" style="stroke:#fde047;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="mt-2 text-3xl font-bold" style="color:#fde047;">{{ $pendingAssessmentsCount }}</p>
            <p class="mt-2 text-xs" style="color:#d1d5db;">Mais de 30 dias sem atualização</p>
        </div>

        <div class="rounded-xl border bg-gray-800 p-4 shadow-lg" style="border-color:#334155;">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium uppercase tracking-wide" style="color:#d1d5db;">Sem Avaliação</p>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" style="stroke:#a5b4fc;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <p class="mt-2 text-3xl font-bold" style="color:#a5b4fc;">{{ $studentsWithoutAssessmentCount }}</p>
            <p class="mt-2 text-xs" style="color:#d1d5db;">Alunos sem primeira medição</p>
        </div>
    </div>

    @if($students->isEmpty())
        <section class="rounded-2xl border border-dashed bg-gray-800 p-8 text-center" style="border-color:#0d9488;">
            <h2 class="text-xl font-semibold" style="color:#ffffff;">Você ainda não tem alunos cadastrados</h2>
            <p class="mx-auto mt-2 max-w-xl text-sm" style="color:#d1d5db;">
                Comece criando seu primeiro aluno para liberar evolução, avaliações e planejamento de treino.
            </p>
            <a href="{{ route('personal.students.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition" style="background:#0d9488;color:#ffffff;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" style="stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Cadastrar Primeiro Aluno
            </a>
        </section>
    @else
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="xl:col-span-2 rounded-2xl border bg-gray-800 shadow-lg" style="border-color:#334155;">
                <div class="flex items-center justify-between border-b px-5 py-4" style="border-color:#334155;">
                    <h2 class="text-lg font-semibold" style="color:#ffffff;">Alunos com Avaliação Atrasada</h2>
                    <span class="rounded-full border px-3 py-1 text-xs font-semibold" style="border-color:#f59e0b;background:linear-gradient(180deg,#92400e,#78350f);color:#ffedd5;box-shadow:inset 0 0 0 1px rgba(245,158,11,.28);">
                        {{ $pendingAssessmentsCount }} pendência(s)
                    </span>
                </div>

                <div class="p-3 sm:p-4">
                    @if($pendingAssessmentsList->isEmpty())
                        <div class="rounded-xl border p-4 text-sm" style="border-color:#10b981;background:#064e3b;color:#d1fae5;">
                            Sem pendências no momento. Todas as avaliações estão em dia.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($pendingAssessmentsList as $item)
                                <a href="{{ route('personal.students.show', $item['student']) }}" class="group flex items-center justify-between rounded-xl border bg-gray-900 px-4 py-3 transition" style="border-color:#334155;">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold" style="color:#ffffff;">{{ $item['student']->name }}</p>
                                        <p class="mt-1 text-xs" style="color:#d1d5db;">
                                            Última avaliação:
                                            {{ $item['last_assessment_date'] ? $item['last_assessment_date']->format('d/m/Y') : 'não registrada' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold" style="background:#78350f;color:#fef3c7;">
                                            {{ $item['days_without_assessment'] }} dias
                                        </span>
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" style="stroke:#9ca3af;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl border bg-gray-800 shadow-lg" style="border-color:#334155;">
                <div class="flex items-center justify-between border-b px-5 py-4" style="border-color:#334155;">
                    <h2 class="text-lg font-semibold" style="color:#ffffff;">Últimos Alunos</h2>
                    <a href="{{ route('personal.students.index') }}" class="text-xs font-semibold" style="color:#2dd4bf;">Ver todos</a>
                </div>
                <div class="space-y-2 p-3">
                    @foreach($students->sortByDesc('created_at')->take(5) as $student)
                        <a href="{{ route('personal.students.show', $student) }}" class="block rounded-xl border bg-gray-900 px-4 py-3 transition" style="border-color:#334155;">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold" style="color:#ffffff;">{{ $student->name }}</p>
                                    <p class="text-xs" style="color:#d1d5db;">
                                        Cadastro {{ $student->created_at ? $student->created_at->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" style="{{ $student->is_active ? 'background:#065f46;color:#d1fae5;' : 'background:#374151;color:#e5e7eb;' }}">
                                    {{ $student->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    @endif
</div>
@endsection
