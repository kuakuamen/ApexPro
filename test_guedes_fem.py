import math

# Procurando a fórmula correta de Guedes para feminino
# Existem casos onde a fórmula varia por gênero

# Teste com dados hipotéticos femininos
peso = 60  # Exemplo
idade = 30 # Exemplo

# Guedes MASCULINO (curren fórmula no código)
soma_test = 50  # Exemplo de soma

d_masc = 1.17136 - (0.06706 * math.log10(soma_test))
fat_masc = ((4.95 / d_masc) - 4.5) * 100

print("GUEDES Masculino (fórmula atual):")
print(f"  Soma: {soma_test} mm")
print(f"  Densidade: {d_masc:.4f}")
print(f"  % Gordura: {fat_masc:.2f}%")

# Possível fórmula feminina (variação 1 - apenas constante diferente)
d_fem1 = 1.16650 - (0.06706 * math.log10(soma_test))
fat_fem1 = ((4.95 / d_fem1) - 4.5) * 100

print("\nGuedes Feminino (Variação 1 - constante 1.16650):")
print(f"  Densidade: {d_fem1:.4f}")
print(f"  % Gordura: {fat_fem1:.2f}%")

# Possível fórmula feminina (variação 2 - coeficiente log diferente)
d_fem2 = 1.17136 - (0.06815 * math.log10(soma_test))
fat_fem2 = ((4.95 / d_fem2) - 4.5) * 100

print("\nGuedes Feminino (Variação 2 - coef log 0.06815):")
print(f"  Densidade: {d_fem2:.4f}")
print(f"  % Gordura: {fat_fem2:.2f}%")

print("\n--- Preciso dos dados reais do caso feminino para validar ---")
