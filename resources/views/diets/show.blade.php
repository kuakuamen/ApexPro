@extends('layouts.app')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center bg-green-50 border-b border-green-200">
        <div>
            <h3 class="text-lg leading-6 font-medium text-green-900">
                {{ $diet->name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-green-700">
                Objetivo: {{ $diet->goal ?? 'Não definido' }}
            </p>
        </div>
        <div>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $diet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $diet->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </div>
    </div>
    
    <div class="border-t border-gray-200">
        <dl>
            @foreach($diet->meals as $meal)
                <div class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 sm:col-span-3 mb-2 flex items-center">
                        <span class="text-lg font-bold text-green-700">{{ $meal->name }}</span>
                        @if($meal->time)
                            <span class="ml-3 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ \Carbon\Carbon::parse($meal->time)->format('H:i') }}
                            </span>
                        @endif
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alimento</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kcal</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obs</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($meal->foods as $food)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $food->name }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $food->quantity }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $food->calories }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 italic">{{ $food->observation }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('diets.index') }}" class="text-green-600 hover:text-green-900 font-medium">
        &larr; Voltar para Minhas Dietas
    </a>
</div>
@endsection
