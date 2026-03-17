'''

#panda y numpy:

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
        print(f"Error de entrada: {str(e)}")'''

#Scikit-learn, pandas y numpy:

import sys
import json
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression

def predict():
    try:
        # Recibir datos de Laravel vía stdin o argumento
        input_data = sys.argv[1] if len(sys.argv) > 1 else sys.stdin.read()
        
        if not input_data.strip():
            print(json.dumps({"stock_sugerido": 0, "error": "No llegaron datos"}))
            return

        data = json.loads(input_data)
        df = pd.DataFrame(data)
        df['created_at'] = pd.to_datetime(df['created_at'])

        # Agrupamos por mes para la gráfica
        monthly_sales = df.set_index('created_at').resample('ME')['quantity'].sum().reset_index()
        chart_labels = monthly_sales['created_at'].dt.strftime('%b %Y').tolist()
        chart_values = monthly_sales['quantity'].tolist()

        if len(monthly_sales) < 2:
            last_val = monthly_sales['quantity'].iloc[0] if not monthly_sales.empty else 0
            resultado = int(np.ceil(last_val * 1.1))
            metodo = "NumPy (Promedio simple)"
        else:
            # Entrenamiento con Scikit-learn
            X = np.array(range(len(monthly_sales))).reshape(-1, 1)
            y = monthly_sales['quantity'].values
            model = LinearRegression()
            model.fit(X, y)
            
            next_month = np.array([[len(monthly_sales)]])
            prediction = model.predict(next_month)[0]
            resultado = int(np.ceil(max(prediction, 0) * 1.1))
            metodo = "Scikit-learn (Regresión Lineal)"

        # Devolvemos el JSON para el Dashboard
        print(json.dumps({
            "stock_sugerido": resultado,
            "metodo": metodo,
            "registros": len(data),
            "chart_data": {
                "labels": chart_labels,
                "values": chart_values
            }
        }))

    except Exception as e:
        print(json.dumps({"error": str(e), "stock_sugerido": "Error"}))

if __name__ == "__main__":
    predict()