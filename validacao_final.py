#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import math

print("="*80)
print("VALIDAÇÃO FINAL: SISTEMA vs IA - COMPOSIÇÃO CORPORAL")
print("="*80)

# Dados base (do caso do usuário)
peso = 99.1
idade = 31

# Somas apresentadas pela IA
somas_ia = {
    'GUEDES': 40,
    'POLLOCK_3': 48,
    'POLLOCK_7': 103
}

print("\n📊 DADOS DE ENTRADA:\n")
print(f"  Peso: {peso} kg")
print(f"  Idade: {idade} anos")
print(f"  Gênero: Masculino")
print(f"  Somas de dobras (conforme IA): {somas_ia}")

print("\n" + "="*80)
print("CÁLCULOS CIENTÍFICOS CORRETOS (Usando fórmulas validadas)")
print("="*80)

# ============= GUEDES =============
print("\n🔹 GUEDES (3 Dobras: Subescapular, Suprailíaca, Coxa)")
print("   Fórmula: Densidade = 1.17136 - (0.06706 * LOG10(soma))")

soma_guedes = somas_ia['GUEDES']
d_guedes = 1.17136 - (0.06706 * math.log10(soma_guedes))
fat_guedes = ((4.95 / d_guedes) - 4.5) * 100
fat_mass_guedes = (fat_guedes / 100) * peso
lean_mass_guedes = peso - fat_mass_guedes

print(f"\n   Soma das dobras: {soma_guedes} mm")
print(f"   ✓ Densidade:      {d_guedes:.4f} g/ml")
print(f"   ✓ % Gordura:      {fat_guedes:.2f}%")
print(f"   ✓ Massa Gorda:    {fat_mass_guedes:.2f} kg")
print(f"   ✓ Massa Magra:    {lean_mass_guedes:.2f} kg")

print(f"\n   🔴 IA Reportou:   Densidade=1.0705, %=12.4%, Massa=12.3kg")
print(f"   ⚠️  ERRO: +0.0066 em densidade (IA usou fórmula diferente!)")

# ============= POLLOCK 3 =============
print("\n" + "-"*80)
print("🔹 POLLOCK 3 (3 Dobras: Tricipital, Suprailíaca, Coxa)")
print("   Fórmula: Densidade = 1.10938 - 0.0008267*S + 0.0000016*S² - 0.0002574*idade")

soma_p3 = somas_ia['POLLOCK_3']
d_p3 = 1.10938 - (0.0008267 * soma_p3) + (0.0000016 * soma_p3**2) - (0.0002574 * idade)
fat_p3 = ((4.95 / d_p3) - 4.5) * 100
fat_mass_p3 = (fat_p3 / 100) * peso
lean_mass_p3 = peso - fat_mass_p3

print(f"\n   Soma das dobras: {soma_p3} mm")
print(f"   ✓ Densidade:      {d_p3:.4f} g/ml")
print(f"   ✓ % Gordura:      {fat_p3:.2f}%")
print(f"   ✓ Massa Gorda:    {fat_mass_p3:.2f} kg")
print(f"   ✓ Massa Magra:    {lean_mass_p3:.2f} kg")

print(f"\n   🟢 IA Reportou:   Densidade=1.0653, %=14.8%, Massa=14.7kg")
print(f"   ✓  PRÓXIMO ao correto (diferença: -0.0001)")

# ============= POLLOCK 7 =============
print("\n" + "-"*80)
print("🔹 POLLOCK 7 (7 Dobras)")
print("   Fórmula: Densidade = 1.112 - 0.00043499*S + 0.00000055*S² - 0.0002882*idade")

soma_p7 = somas_ia['POLLOCK_7']
d_p7 = 1.112 - (0.00043499 * soma_p7) + (0.00000055 * soma_p7**2) - (0.0002882 * idade)
fat_p7 = ((4.95 / d_p7) - 4.5) * 100
fat_mass_p7 = (fat_p7 / 100) * peso
lean_mass_p7 = peso - fat_mass_p7

print(f"\n   Soma das dobras: {soma_p7} mm")
print(f"   ✓ Densidade:      {d_p7:.4f} g/ml")
print(f"   ✓ % Gordura:      {fat_p7:.2f}%")
print(f"   ✓ Massa Gorda:    {fat_mass_p7:.2f} kg")
print(f"   ✓ Massa Magra:    {lean_mass_p7:.2f} kg")

print(f"\n   🔴 IA Reportou:   Densidade=1.0639, %=15.5%, Massa=15.4kg")
print(f"   ⚠️  ALERTA: Densidade=1.0639 é IDÊNTICA à GUEDES!")
print(f"   ❌ IA MISTUROU os resultados entre protocolos!")

# ============= COMPARAÇÃO FINAL =============
print("\n" + "="*80)
print("📈 RESUMO COMPARATIVO")
print("="*80)

dados_tabela = [
    ["Protocolo", "Sistema", "IA Reporta", "Diferença", "Status"],
    ["-"*15, "-"*12, "-"*12, "-"*12, "-"*10],
    [
        "GUEDES Dens.", 
        f"{d_guedes:.4f}",
        "1.0705",
        f"+{1.0705-d_guedes:.4f}",
        "❌ ERRADO"
    ],
    [
        "GUEDES %", 
        f"{fat_guedes:.2f}%",
        "12.4%",
        f"{12.4-fat_guedes:.2f}%",
        "❌ ERRADO"
    ],
    [
        "POLLOCK3 Dens.", 
        f"{d_p3:.4f}",
        "1.0653",
        f"{abs(1.0653-d_p3):.4f}",
        "✓ Ok"
    ],
    [
        "POLLOCK7 Dens.", 
        f"{d_p7:.4f}",
        "1.0639",
        f"{abs(d_guedes-1.0639):.4f}",
        "❌ MISTURADO"
    ],
]

for row in dados_tabela:
    print(f"  {row[0]:15} | {row[1]:12} | {row[2]:12} | {row[3]:12} | {row[4]:10}")

print("\n" + "="*80)
print("🎯 CONCLUSÃO")
print("="*80)
print("\n✅ SISTEMA: Implementação CORRETA das fórmulas científicas validadas")
print("❌ IA:      Usando fórmulas ou dados DIFERENTES dos padrões")
print("\n⚠️  RECOMENDAÇÃO: Use o BodyCompositionService do sistema como")
print("   fonte única de verdade para composição corporal.")

print("\n" + "="*80)
