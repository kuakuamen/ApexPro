import math

# Teste inversão: qual soma produz as densidades que aparecem?
peso = 99.1
idade = 31

# POLLOCK 3: densidade = 1.0718
# Inverter: 1.0718 = 1.10938 - (0.0008267 * sum) + (0.0000016 * sum²) - (0.0002574 * 31)
# Resolver equação quadrática

# Forma: 0.0000016*sum² - 0.0008267*sum + (1.10938 - 0.0002574*31 - 1.0718) = 0
a = 0.0000016
b = -0.0008267
c = 1.10938 - (0.0002574 * idade) - 1.0718

disc = b**2 - 4*a*c
sum1 = (-b + math.sqrt(disc)) / (2*a)
sum2 = (-b - math.sqrt(disc)) / (2*a)

print("POLLOCK 3 (densidade = 1.0718):")
print(f"  Soma calculada (possível 1): {sum1:.1f}")
print(f"  Soma calculada (possível 2): {sum2:.1f}")
print(f"  Se soma fosse 25: densidade = {1.10938 - (0.0008267 * 25) + (0.0000016 * 25**2) - (0.0002574 * idade):.4f}")
print(f"  Se soma fosse 39: densidade = {1.10938 - (0.0008267 * 39) + (0.0000016 * 39**2) - (0.0002574 * idade):.4f}")

# POLLOCK 7: densidade = 1.0524
print("\nPOLLOCK 7 (densidade = 1.0524):")
a7 = 0.00000055
b7 = -0.00043499
c7 = 1.112 - (0.0002882 * idade) - 1.0524

disc7 = b7**2 - 4*a7*c7
sum1_p7 = (-b7 + math.sqrt(disc7)) / (2*a7)
sum2_p7 = (-b7 - math.sqrt(disc7)) / (2*a7)

print(f"  Soma calculada (possível 1): {sum1_p7:.1f}")
print(f"  Soma calculada (possível 2): {sum2_p7:.1f}")
print(f"  Se soma fosse 98: densidade = {1.112 - (0.00043499 * 98) + (0.00000055 * 98**2) - (0.0002882 * idade):.4f}")
print(f"  Se soma fosse 142: densidade = {1.112 - (0.00043499 * 142) + (0.00000055 * 142**2) - (0.0002882 * idade):.4f}")
