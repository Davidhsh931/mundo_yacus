import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
import nltk
from nltk.sentiment import SentimentIntensityAnalyzer

print('\n' + '='*50)
print('SISTEMA DE IA - SENATI - DAVID AQUINO')
print('='*50)

# 1. Datos
print('1. Procesando datos con Pandas/Numpy...')
df = pd.DataFrame({'precio': np.random.randn(100).cumsum() + 100}).astype(np.float32)

# 2. NLP
print('2. Ejecutando análisis de sentimiento NLTK...')
nltk.download('vader_lexicon', quiet=True)
sia = SentimentIntensityAnalyzer()
print(f'   Resultado NLP: {sia.polarity_scores("Mercado estable")["compound"]}')

# 3. Gráfico
print('3. Generando visualización Seaborn...')
plt.figure(figsize=(10,5))
sns.lineplot(data=df['precio'], color='blue')
plt.title('Analisis Financiero - Entregable 2')
plt.savefig('/var/www/tendencia.png')

print('\n✅ PROCESO COMPLETADO EXITOSAMENTE')
print('='*50 + '\n')
