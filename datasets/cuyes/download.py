from roboflow import Roboflow

# Tu clave privada real que generaste en la imagen 75d727
rf = Roboflow(api_key="gTTGpl0MTO7IC5oJP5Oi") 

# El workspace es mundo-yacus
# El project es el nombre que le diste al hacer el Fork (revisa tu URL si tienes dudas)
project = rf.workspace("mundo-yacus").project("guinea-pig")

# Si es la primera vez que generas en TU cuenta, será la versión 1
version = project.version(1)

dataset = version.download("yolov8")

print("✅ ¡Cuyes en casa!")