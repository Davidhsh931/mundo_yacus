
'''Scikit-Learn y Pythorch:'''

import sys
import json
import warnings
import os
from ultralytics import YOLO

warnings.filterwarnings("ignore")
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

def analyze_cuy(image_path):
    try:
        # 1. Cargamos TU modelo entrenado (la joya de la corona)
        # Usamos la ruta que me pasaste del contenedor
        model_path = '/var/www/datasets/cuyes/runs/detect/train2/weights/best.pt'
        
        if not os.path.exists(model_path):
            return json.dumps({"status": "error", "message": f"No se encontró el modelo en: {model_path}"})

        model = YOLO(model_path) 

        # 2. Ejecutamos la predicción
        # verbose=False para que no ensucie el output que lee Laravel
        results = model(image_path, verbose=False)
        
        # 3. Procesamos los resultados de tu entrenamiento
        found_cuy = False
        best_box = None
        conf_value = 0
        raza_detectada = "Desconocida"

        for r in results:
            if len(r.boxes) > 0:
                found_cuy = True
                # Tomamos la detección con mayor confianza
                top_box = r.boxes[0] 
                conf_value = float(top_box.conf[0]) * 100
                best_box = top_box.xyxy[0].tolist() # [x1, y1, x2, y2]
                
                # Obtenemos el nombre de la clase según tu entrenamiento
                # (Ej: Tipo 1, Tipo 2, etc.)
                raza_detectada = r.names[int(top_box.cls[0])]
                break

        # 4. Lógica de Respuesta Profesional
        if found_cuy and conf_value >= 45.0:
            result = {
                "status": "success",
                "raza_detectada": raza_detectada, 
                "confianza": f"{conf_value:.2f}%",
                "coordenadas": best_box,
                "sugerencia": f"¡Cuy {raza_detectada} detectado con éxito!"
            }
        else:
            result = {
                "status": "error",
                "raza_detectada": "No se detectó el ejemplar",
                "confianza": "0%",
                "sugerencia": "La imagen no coincide con los patrones de cuyes entrenados. Intenta otra toma."
            }
            
        return json.dumps(result)

    except Exception as e:
        return json.dumps({"status": "error", "message": str(e)})

if __name__ == "__main__":
    if len(sys.argv) > 1:
        print(analyze_cuy(sys.argv[1]))
