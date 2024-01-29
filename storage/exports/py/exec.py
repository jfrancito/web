
import requests
from requests_ntlm import HttpNtlmAuth

url = "http://10.1.0.201/ReportServer/Pages/ReportViewer.aspx?%2fReportes%2FOSIRIS%2FCON_VENTAS_CUOTA_COMERCIAL_CORREO&rs:Format=Excel"
archivo_local = "archivo_descargado.xls"

try:
    response = requests.get(url, auth=HttpNtlmAuth('Induamerica\\ADMINISTRADOR', 'T3@m5y5jdf1ndu@m3r1c@'), verify=False)
    response.raise_for_status()  # Lanza una excepción si la respuesta tiene un código de error HTTP

    with open(archivo_local, 'wb') as f:
        f.write(response.content)

    print(f"Archivo descargado correctamente en: {archivo_local}")

except requests.exceptions.RequestException as e:
    print(f"Error en la solicitud: {e}")
