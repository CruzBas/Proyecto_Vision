README - Notas Clínicas (API y Frontend)

Resumen
- Archivos agregados/actualizados:
  - `api_notas.php`  (ENDPOINTS REST para notas)
  - `api_pacientes.php` (lista de pacientes)
  - `VerNotas` (HTML) — JavaScript para listar, buscar y eliminar notas
  - `CrearNotas.html` — JavaScript para crear notas
  - `EditarNotas` (HTML) — JavaScript para cargar y actualizar una nota
- Base de datos requerida: tabla `notas_clinicas` ya creada e integrada con tus tablas (`pacientes`, `psicologos`, `citas`).

Rutas y su propósito
- `api_notas.php?action=listar`  (GET) — devuelve todas las notas
- `api_notas.php?action=obtener&id=<ID_Nota>` (GET) — devuelve una nota
- `api_notas.php?action=buscar&q=<texto>` (GET) — busca notas por paciente/resumen/contenido
- `api_notas.php?action=crear` (POST) — crea una nota (JSON)
- `api_notas.php?action=actualizar&id=<ID_Nota>` (POST o PUT) — actualiza nota (JSON)
- `api_notas.php?action=eliminar&id=<ID_Nota>` (POST o DELETE) — elimina nota
- `api_pacientes.php` (GET) — lista pacientes para el select

URLs de ejemplo en tu entorno local (XAMPP)
- Base asumida: `http://localhost/Proyecto_Vision/`
- `VerNotas`: `http://localhost/Proyecto_Vision/VerNotas`
- `CrearNotas`: `http://localhost/Proyecto_Vision/CrearNotas.html`
- `EditarNotas` (ej. ID 5): `http://localhost/Proyecto_Vision/EditarNotas?id=5`

Pruebas con curl (Linux/macOS/Windows 10+ con curl)
1) Listar notas
curl -s "http://localhost/Proyecto_Vision/api_notas.php?action=listar" | jq

2) Obtener una nota
curl -s "http://localhost/Proyecto_Vision/api_notas.php?action=obtener&id=1" | jq

3) Buscar notas
curl -s "http://localhost/Proyecto_Vision/api_notas.php?action=buscar&q=Juan" | jq

4) Crear nota (curl)
curl -s -X POST "http://localhost/Proyecto_Vision/api_notas.php?action=crear" \
  -H "Content-Type: application/json" \
  -d '{"ID_Paciente":1,"ID_Psicologo":1,"Fecha_Sesion":"2025-12-05","Contenido":"Nota de prueba desde curl","Resumen":"Resumen de prueba"}' | jq

5) Actualizar nota (curl)
curl -s -X POST "http://localhost/Proyecto_Vision/api_notas.php?action=actualizar&id=1" \
  -H "Content-Type: application/json" \
  -d '{"Contenido":"Contenido actualizado desde curl","Resumen":"Resumen actualizado"}' | jq

6) Eliminar nota (curl)
curl -s -X POST "http://localhost/Proyecto_Vision/api_notas.php?action=eliminar&id=1"

Pruebas con PowerShell (Windows)
1) Listar notas
Invoke-RestMethod -Uri "http://localhost/Proyecto_Vision/api_notas.php?action=listar" -Method Get | ConvertTo-Json -Depth 4

2) Obtener nota
Invoke-RestMethod -Uri "http://localhost/Proyecto_Vision/api_notas.php?action=obtener&id=1" -Method Get | ConvertTo-Json -Depth 4

3) Crear nota (PowerShell)
$body = @{ ID_Paciente = 1; ID_Psicologo = 1; Fecha_Sesion = '2025-12-05'; Contenido = 'Nota creada desde PowerShell'; Resumen = 'Resumen PS' } | ConvertTo-Json
Invoke-RestMethod -Uri "http://localhost/Proyecto_Vision/api_notas.php?action=crear" -Method Post -Body $body -ContentType 'application/json'

4) Actualizar nota (PowerShell)
$body = @{ Contenido = 'Contenido actualizado desde PowerShell'; Resumen = 'Resumen actualizado PS' } | ConvertTo-Json
Invoke-RestMethod -Uri "http://localhost/Proyecto_Vision/api_notas.php?action=actualizar&id=2" -Method Post -Body $body -ContentType 'application/json'

5) Eliminar nota (PowerShell)
Invoke-RestMethod -Uri "http://localhost/Proyecto_Vision/api_notas.php?action=eliminar&id=2" -Method Post

Verificando la lista de pacientes (para el select)
curl -s "http://localhost/Proyecto_Vision/api_pacientes.php" | jq

Puntos importantes antes de probar
- Comprueba que `db.php` tiene la conexión correcta y que el servidor MySQL está corriendo.
- Ajusta `ID_PSICOLOGO` en `CrearNotas.html` si tu aplicación tiene autenticación (por ahora es un valor estático en el JS).
- Si las páginas están en subcarpetas, ajusta las rutas relativas en los fetch (por ejemplo, prepend `/Proyecto_Vision/` si necesario).

Errores comunes y soluciones
- 500 Error / JSON con message "Error de conexión a la BD": revisa `db.php` y credenciales.
- Respuestas vacías o 404 en `api_notas.php`: revisa que la tabla `notas_clinicas` exista y tenga datos; prueba la consulta en phpMyAdmin.
- CORS: los headers ya permiten cualquier origen; si trabajas con otro dominio, revisa la política CORS.

Siguientes pasos recomendados
- Reemplazar `ID_PSICOLOGO` por el ID del usuario logueado en sesión (PHP) para asignar correctamente el autor de la nota.
- Añadir validaciones en servidor para evitar inserciones corruptas.
- (Opcional) Implementar `notas_historial` para conservar versiones previas.

¿Quieres que:
- Genere una versión del `api_notas.php` que use métodos `PUT`/`DELETE` en lugar de POST para actualizar/eliminar? (más RESTful)
- Extraiga `ID_PSICOLOGO` desde la sesión `$_SESSION` en PHP y lo devuelva al frontend? 
- Añada un pequeño script SQL para crear `notas_historial` y poner un trigger que guarde versiones antes de UPDATE? 

---

