/**
 * admin.js
 * 
 * Lógica del cliente para el panel de administración usando jQuery AJAX:
 * - Cargar usuarios desde panelAdmin.php
 * - Cambiar rol de un usuario.
 * - Eliminar usuario.
 * 
 * Requiere importar jQuery:
 * <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 */

function rolLabel(rol){
  if(rol == 1) return 'Paciente';
  if(rol == 2) return 'Doctor';
  if(rol == 3) return 'Administrador';
  return 'Desconocido';
}

// Cargar usuarios y renderizar tabla
function cargarUsuarios(){
  $.ajax({
    url: 'panelAdmin.php',
    method: 'GET',
    data: {
      action: 'list',
      nombre: $('#f_nombre').val() || '',
      correo: $('#f_correo').val() || '',
      rol: $('#f_rol').val() || ''
    },
    dataType: 'json',
    success: function(data){
      const tbody = $('#tbody_usuarios');
      tbody.empty();
      data.items.forEach(u => {
        tbody.append(`
          <tr>
            <td class="p-4">${u.ID_Usuario}</td>
            <td class="p-4">${u.ID_Paciente || u.ID_Psicologo || '-'}</td>
            <td class="p-4">${u.Nombre_Paciente || u.Nombre_Psicologo}</td>
            <td class="p-4">${u.Apellido_Paciente || u.Apellido_Psicologo}</td>
            <td class="p-4">${u.email}</td>
            <td class="p-4">${u.Telefono_Paciente || u.Pisicologo_Telefono || '-'}</td>
            <td class="p-4">${rolLabel(u.ID_Role)}</td>
            <td class="p-4 text-right">
              <button onclick="cambiarRol(${u.ID_Usuario},2)" class="px-2 py-1 bg-blue-500 text-white rounded">Doctor</button>
              <button onclick="cambiarRol(${u.ID_Usuario},1)" class="px-2 py-1 bg-green-500 text-white rounded">Paciente</button>
              <button onclick="cambiarRol(${u.ID_Usuario},3)" class="px-2 py-1 bg-purple-500 text-white rounded">Admin</button>
              <button onclick="eliminarUsuario(${u.ID_Usuario})" class="px-2 py-1 bg-red-500 text-white rounded">Eliminar</button>
            </td>
          </tr>
        `);
      });
    },
    error: function(xhr){
      alert('Error al cargar usuarios: ' + xhr.responseText);
    }
  });
}

// Cambiar rol de un usuario
function cambiarRol(id, rol){
    let rolTexto = rolLabel(rol);
    if(!confirm(`¿Seguro que deseas cambiar de rol al usuario ${id} a ${rolTexto}`)) return;
  $.ajax({
    url: 'panelAdmin.php?action=updateRol',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ usuario_id:id, rol_nuevo:rol }),
    dataType: 'json',
    success: function(data){
      alert(data.message || data.error);
      cargarUsuarios();
    },
    error: function(xhr){
      alert('Error al cambiar rol: ' + xhr.responseText);
    }
  });
}

// Eliminar usuario
function eliminarUsuario(id){
  if(!confirm(`¿Seguro que deseas eliminar al usuario ${id} Esta accion no se puede deshacer.?`)) return;
  $.ajax({
    url: 'panelAdmin.php?action=deleteUser',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ id }),
    dataType: 'json',
    success: function(data){
      alert(data.message || data.error);
      cargarUsuarios();
    },
    error: function(xhr){
      alert('Error al eliminar usuario: ' + xhr.responseText);
    }
  });
}

// Inicializa la tabla
$(document).ready(function(){
  cargarUsuarios();

  // Recargar automáticamente al cambiar filtros
  $('#f_nombre, #f_correo, #f_rol').on('input change', function(){
    cargarUsuarios();
  });
});
