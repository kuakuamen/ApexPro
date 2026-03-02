import math

# Valores de entrada
peso = 99.1
idade = 31

# Somas que a IA apresentou
sum_guedes_ia = 40
sum_p3_ia = 48
sum_p7_ia = 103

print("=" * 60)
print("TESTANDO COM AS SOMAS QUE A IA APRESENTOU")
print("=" * 60)

# GUEDES - com soma 40 da IA
d_guedes_ia = 1.17136 - (0.06706 * math.log10(sum_guedes_ia))
fat_guedes_ia = ((4.95 / d_guedes_ia) - 4.5) * 100
fat_mass_guedes_ia = (fat_guedes_ia / 100) * peso
lean_mass_guedes_ia = peso - fat_mass_guedes_ia

print("\nGUEDES (soma = 40 - da IA)")
print(f"  Densidade: {d_guedes_ia:.4f} g/ml")
print(f"  % Gordura: {fat_guedes_ia:.2f}%")
print(f"  Massa Gordura: {fat_mass_guedes_ia:.2f} kg")
print(f"  Massa Livre: {lean_mass_guedes_ia:.2f} kg")
print(f"  IA relatou: Densidade=1.0705, %=12.4, Massa=12.3kg, Livre=86.8kg")

# POLLOCK 3 - com soma 48 da IA
d_p3_ia = 1.10938 - (0.0008267 * sum_p3_ia) + (0.0000016 * sum_p3_ia**2) - (0.0002574 * idade)
fat_p3_ia = ((4.95 / d_p3_ia) - 4.5) * 100
fat_mass_p3_ia = (fat_p3_ia / 100) * peso
lean_mass_p3_ia = peso - fat_mass_p3_ia

print("\nPOLLOCK 3 (soma = 48 - da IA)")
print(f"  Densidade: {d_p3_ia:.4f} g/ml")
print(f"  % Gordura: {fat_p3_ia:.2f}%")
print(f"  Massa Gordura: {fat_mass_p3_ia:.2f} kg")
print(f"  Massa Livre: {lean_mass_p3_ia:.2f} kg")
print(f"  IA relatou: Densidade=1.0653, %=14.8, Massa=14.7kg, Livre=84.4kg")

# POLLOCK 7 - com soma 103 da IA
d_p7_ia = 1.112 - (0.00043499 * sum_p7_ia) + (0.00000055 * sum_p7_ia**2) - (0.0002882 * idade)
fat_p7_ia = ((4.95 / d_p7_ia) - 4.5) * 100
fat_mass_p7_ia = (fat_p7_ia / 100) * peso
lean_mass_p7_ia = peso - fat_mass_p7_ia

print("\nPOLLOCK 7 (soma = 103 - da IA)")
print(f"  Densidade: {d_p7_ia:.4f} g/ml")
print(f"  % Gordura: {fat_p7_ia:.2f}%")
print(f"  Massa Gordura: {fat_mass_p7_ia:.2f} kg")
print(f"  Massa Livre: {lean_mass_p7_ia:.2f} kg")
print(f"  IA relatou: Densidade=1.0639, %=15.5, Massa=15.4kg, Livre=83.7kg")

print("\n" + "=" * 60)
print("RESUMO DE DISCREPÂNCIAS")
print("=" * 60)

print(f"\nGUEDES: Sistema calcula {d_guedes_ia:.4f}, IA reporta 1.0705 (Diferença: {abs(d_guedes_ia - 1.0705):.4f})")
print(f"POLLOCK 3: Sistema calcula {d_p3_ia:.4f}, IA reporta 1.0653 (Diferença: {abs(d_p3_ia - 1.0653):.4f})")
print(f"POLLOCK 7: Sistema calcula {d_p7_ia:.4f}, IA reporta 1.0639 (Diferença: {abs(d_p7_ia - 1.0639):.4f})")
