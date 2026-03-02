@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Coluna da Esquerda: Imagens -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Imagens Enviadas</h3>
            <div class="space-y-4">
                <div class="relative group">
                    <span class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">Frente</span>
                    <img src="{{ route('photo.show', [$assessment->id, 'front']) }}" alt="Frente" class="w-full h-auto rounded-lg border border-gray-200">
                </div>
                <div class="relative group">
                    <span class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">Perfil</span>
                    <img src="{{ route('photo.show', [$assessment->id, 'side']) }}" alt="Lado" class="w-full h-auto rounded-lg border border-gray-200">
                </div>
                <div class="relative group">
                    <span class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">Costas</span>
                    <img src="{{ route('photo.show', [$assessment->id, 'back']) }}" alt="Costas" class="w-full h-auto rounded-lg border border-gray-200">
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna da Direita: Análise da IA -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Status -->
        <div class="bg-white shadow rounded-lg p-6 border-l-4 {{ $assessment->status === 'approved' ? 'border-green-500' : 'border-yellow-500' }}">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Resultado da Análise</h2>
                    <p class="text-sm text-gray-500">Realizada em {{ $assessment->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $assessment->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($assessment->status) }}
                </span>
            </div>
        </div>

        <!-- Análise Postural (IA) -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center">
                <svg class="w-6 h-6 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900">Análise Postural (IA)</h3>
            </div>
            <div class="p-6">
                @php $analysis = $assessment->ai_analysis_data; @endphp
                
                @if($analysis)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Desvios Identificados</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li><strong class="text-gray-900">Lordose:</strong> {{ $analysis['posture_analysis']['lordosis'] }}</li>
                                <li><strong class="text-gray-900">Escoliose:</strong> {{ $analysis['posture_analysis']['scoliosis'] }}</li>
                                <li><strong class="text-gray-900">Ombros:</strong> {{ $analysis['posture_analysis']['shoulders'] }}</li>
                                <li><strong class="text-gray-900">Cabeça:</strong> {{ $analysis['posture_analysis']['head_position'] }}</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Foco Sugerido</h4>
                            <div class="mb-2">
                                <span class="text-xs font-semibold text-green-600 uppercase">Fortalecer</span>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($analysis['suggested_focus']['strengthen'] as $item)
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-blue-600 uppercase">Alongar</span>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($analysis['suggested_focus']['stretch'] as $item)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-semibold text-gray-700 mb-2">Recomendação de Treino</h4>
                        <div class="bg-blue-50 p-4 rounded-md">
                            <p class="text-blue-900"><strong>Tipo:</strong> {{ $analysis['workout_recommendation']['type'] }}</p>
                            <p class="text-blue-900"><strong>Frequência:</strong> {{ $analysis['workout_recommendation']['frequency'] }}</p>
                            <p class="text-blue-800 mt-2 text-sm"><em>"{{ $analysis['workout_recommendation']['priority'] }}"</em></p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 italic">A análise ainda está sendo processada...</p>
                @endif
            </div>
        </div>

        <!-- Área do Personal (Aprovação) -->
        @if(auth()->user()->role === 'personal')
            <div class="bg-white shadow rounded-lg overflow-hidden border border-indigo-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
                    <h3 class="text-lg font-medium text-indigo-900">Área do Personal</h3>
                </div>
                <div class="p-6">
                    <form action="#" method="POST"> <!-- TODO: Rota de aprovação -->
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Feedback / Ajustes</label>
                            <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" rows="3" placeholder="Escreva aqui se concorda com a IA ou se deseja alterar algo..."></textarea>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Aprovar e Criar Treino</button>
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Rejeitar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
