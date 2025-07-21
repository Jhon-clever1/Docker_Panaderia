from flask import Flask, request, jsonify
import numpy as np
import onnxruntime as ort

# Cargar modelo ONNX (está en el mismo directorio)
session = ort.InferenceSession("modelo_kmeans.onnx")

# Mapeo día → entero EXACTO que usaste al entrenar
dia_dict = {
    "Monday": 0, "Tuesday": 1, "Wednesday": 2,
    "Thursday": 3, "Friday": 4, "Saturday": 5, "Sunday": 6
}

app = Flask(__name__)

@app.route("/predict", methods=["POST"])
def predict():
    data = request.get_json()
    hora = int(data["hora"])
    dia  = data["dia_semana"]

    dia_enc = dia_dict.get(dia, 0)
    inp     = np.array([[hora, dia_enc]], dtype=np.float32)

    # Ejecutar el modelo
    out = session.run(None, {"input": inp})
    cluster = int(out[0][0])

    return jsonify({"cluster": cluster})

# Iniciar servicio
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
