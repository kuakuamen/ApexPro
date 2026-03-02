import math

soma = 37
peso = 49.9
f_esperado = 18.9

# Calcular qual densidade precisa pra dar 18.9%
d_precisa = 4.95 / (((f_esperado / 100) + 4.5))
print(f'Densidade precisa ser: {d_precisa:.6f} para dar {f_esperado}% gordura')
print()

log_soma = math.log10(soma)
print(f'LOG10({soma}) = {log_soma:.6f}')
print()

print('Testando fórmulas femininas:')
print()

formas = [
    (1.16505, 0.06706, 'Fem1a'),
    (1.16650, 0.06706, 'Fem1b'),
    (1.17136, 0.06434, 'Fem2'),
    (1.17136, 0.071, 'Test1'),
    (1.16055, 0.06706, 'Test2'),
]

for a, b, desc in formas:
    d_calc_form = a - (b * log_soma)
    fat = ((4.95 / d_calc_form) - 4.5) * 100
    diff = abs(d_calc_form - d_precisa)
    print(f'{desc}: D = {a} - {b}*log => D={d_calc_form:.4f}, Fat={fat:.2f}% (diff={diff:.6f})')
