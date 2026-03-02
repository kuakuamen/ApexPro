import math

# Resultados mostrados na tela do usuário
densidade_guedes = 1.0678
densidade_pollock3 = 1.0718
densidade_pollock7 = 1.0524

# Idade assumida (31 anos, como nos testes anteriores)
idade = 31

# Reverter Guedes: Densidade = 1.17136 - (0.06706 * LOG10(sum))
# 1.0678 = 1.17136 - (0.06706 * LOG10(sum))
# 0.06706 * LOG10(sum) = 1.17136 - 1.0678
# LOG10(sum) = (1.17136 - 1.0678) / 0.06706
log_sum_guedes = (1.17136 - densidade_guedes) / 0.06706
sum_guedes = 10 ** log_sum_guedes
print(f"GUEDES: Soma das dobras ≈ {sum_guedes:.1f}")

# Reverter Pollock 3: Densidade = 1.10938 - (0.0008267 * sum) + (0.0000016 * sum²) - (0.0002574 * idade)
# Esta é uma equação quadrática: 0.0000016*sum² - 0.0008267*sum + (1.10938 - 0.0002574*idade - densidade) = 0
# Usando a=0.0000016, b=-0.0008267, c=(1.10938 - 0.0002574*31 - 1.0718)
a = 0.0000016
b = -0.0008267
c = 1.10938 - (0.0002574 * idade) - densidade_pollock3

discriminante = b**2 - 4*a*c
if discriminante >= 0:
    sum_p3_1 = (-b + math.sqrt(discriminante)) / (2*a)
    sum_p3_2 = (-b - math.sqrt(discriminante)) / (2*a)
    # Pegar o valor positivo mais razoável
    sum_pollock3 = sum_p3_1 if sum_p3_1 > 0 and sum_p3_1 < 200 else sum_p3_2
    print(f"POLLOCK 3: Soma das dobras ≈ {sum_pollock3:.1f}")

# Reverter Pollock 7: Similar ao Pollock 3
# Densidade = 1.112 - (0.00043499 * sum) + (0.00000055 * sum²) - (0.0002882 * idade)
a7 = 0.00000055
b7 = -0.00043499
c7 = 1.112 - (0.0002882 * idade) - densidade_pollock7

discriminante7 = b7**2 - 4*a7*c7
if discriminante7 >= 0:
    sum_p7_1 = (-b7 + math.sqrt(discriminante7)) / (2*a7)
    sum_p7_2 = (-b7 - math.sqrt(discriminante7)) / (2*a7)
    sum_pollock7 = sum_p7_1 if sum_p7_1 > 0 and sum_p7_1 < 500 else sum_p7_2
    print(f"POLLOCK 7: Soma das dobras ≈ {sum_pollock7:.1f}")

print("\n" + "="*60)
print("COMPARAÇÃO:")
print(f"Teste anterior usou: Guedes=52, Pollock3=43, Pollock7=98")
print(f"Consulta atual parece usar: Guedes≈{sum_guedes:.0f}, P3≈{sum_pollock3:.0f}, P7≈{sum_pollock7:.0f}")
print("="*60)
