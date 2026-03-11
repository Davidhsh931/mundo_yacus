import pandas as pd
import numpy as np
import sys
import json

def analizar_mundo_yacus(json_data):
    try:
        # Cargar datos
        df = pd.DataFrame(json_data)
        
        # Pre-procesamiento con Pandas
        df['created_at'] = pd.to_datetime(df['created_at'])
        
        # CAMBIO AQUÍ: 'M' ahora es 'ME' en versiones nuevas de Pandas
        ventas_mensuales = df.set_index('created_at').resample('ME')['quantity'].sum().fillna(0)
        
        # Análisis con NumPy
        datos = ventas_mensuales.values
        
        # Si solo enviamos 3 meses de prueba, NumPy hará el cálculo
        if len(datos) < 3:
            # Si hay pocos meses, usamos el promedio de lo que haya
            promedio_reciente = np.mean(datos)
        else:
            # Promedio de los últimos 3 meses
            promedio_reciente = np.mean(datos[-3:])
        
        # Factor de crecimiento (10% extra por seguridad)
        prediccion = promedio_reciente * 1.10
        
        return int(np.ceil(prediccion))
    except Exception as e:
        return f"Error interno: {str(e)}"

if __name__ == "__main__":
    try:
        # Leer el JSON de los argumentos
        raw_input = json.loads(sys.argv[1])
        print(analizar_mundo_yacus(raw_input))
    except Exception as e:
        print(f"Error de entrada: {str(e)}")