from ultralytics import YOLO
import os

# 1. Cargar el modelo base NANO (el más ligero)
model = YOLO('yolov8n.pt')

# 2. Configuración de entrenamiento ultra-optimizado
# Nota: 'data' será el archivo .yaml que describe dónde están las fotos
results = model.train(
    data='cuy_data.yaml', 
    epochs=5,           # Solo 5 vueltas para probar el calor del Mac
    imgsz=320,          # Resolución baja = CPU feliz
    batch=2,            # Solo 2 imágenes a la vez para no saturar la RAM
    workers=1,          # Un solo hilo de carga para dejarle aire al sistema
    device='cpu',       # Obligatorio: tu Mac no tiene GPU NVIDIA
    name='yacu_model'   # Nombre de tu modelo personalizado
)

print("¡Entrenamiento completado! El modelo está en runs/detect/yacu_model/weights/best.pt")