import math

# Valores de exemplo da planilha
peso = 99.1
idade = 31
sum_guedes = 52  # P84 da planilha
sum_p3 = 43      # P77 da planilha
sum_p7 = 98      # Q77 da planilha

# GUEDES
d_guedes = 1.17136 - (0.06706 * math.log10(sum_guedes))
fat_guedes = ((4.95 / d_guedes) - 4.5) * 100
fat_mass_guedes = (fat_guedes / 100) * peso
lean_mass_guedes = peso - fat_mass_guedes

print("=" * 60)
print("GUEDES (Dobras: subescapular, suprailiaca, coxa)")
print(f"  Densidade: {d_guedes:.2f} g/ml")
print(f"  % Gordura: {fat_guedes:.1f}%")
print(f"  Massa de gordura: {fat_mass_guedes:.1f} kg")
print(f"  Massa livre: {lean_mass_guedes:.1f} kg")

# POLLOCK 3
d_p3 = 1.10938 - (0.0008267 * sum_p3) + (0.0000016 * sum_p3**2) - (0.0002574 * idade)
fat_p3 = ((4.95 / d_p3) - 4.5) * 100
fat_mass_p3 = (fat_p3 / 100) * peso
lean_mass_p3 = peso - fat_mass_p3

print("\n" + "=" * 60)
print("POLLOCK 3 (Dobras: tricipital, suprailiaca, coxa)")
print(f"  Densidade: {d_p3:.2f} g/ml")
print(f"  % Gordura: {fat_p3:.1f}%")
print(f"  Massa de gordura: {fat_mass_p3:.1f} kg")
print(f"  Massa livre: {lean_mass_p3:.1f} kg")

# POLLOCK 7
d_p7 = 1.112 - (0.00043499 * sum_p7) + (0.00000055 * sum_p7**2) - (0.0002882 * idade)
fat_p7 = ((4.95 / d_p7) - 4.5) * 100
fat_mass_p7 = (fat_p7 / 100) * peso
lean_mass_p7 = peso - fat_mass_p7

print("\n" + "=" * 60)
print("POLLOCK 7 (Dobras: 7 dobras)")
print(f"  Densidade: {d_p7:.2f} g/ml")
print(f"  % Gordura: {fat_p7:.1f}%")
print(f"  Massa de gordura: {fat_mass_p7:.1f} kg")
print(f"  Massa livre: {lean_mass_p7:.1f} kg")

print("\n" + "=" * 60)
print("Esperado da planilha Excel:")
print("  GUEDES: Densidade=1.06, %=18.6, Massa=18.5kg, Livre=80.6kg")
print("  POLLOCK 3: Densidade=1.07, %=13.1, Massa=13.0kg, Livre=86.1kg")
print("  POLLOCK 7: Densidade=1.07, %=14.5, Massa=14.3kg, Livre=84.8kg")
print("=" * 60)
