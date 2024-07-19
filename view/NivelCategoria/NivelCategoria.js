// Variable global para almacenar los niveles de peligro
let nivelPeligro = [];

// mensaje flotante de sweetalert2
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// Obtener los niveles de peligro y luego actualizar la tabla
$.get("../../controller/nivelPeligro.php", { op: "get_nivel_peligro_json" },
    function (data, textStatus, jqXHR) {
        console.log(data);
        if (data && Array.isArray(data)) {
            nivelPeligro = data;
            // Después de obtener los niveles de peligro, actualizar la tabla
            actualizarTabla();
        } else {
            console.error("Datos nivel inválido:", data);
        }
    },
    "json"
).fail(function(jqXHR, textStatus, errorThrown) {
    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
});

// Función para actualizar la tabla con datos de categoría y niveles de peligro
function actualizarTabla() {
    // Obtener los datos de categoría y actualizar la tabla
    $.get("../../controller/categoria.php", { op: "cateogia_nivel" },
        function (data, textStatus, jqXHR) {
            if (data && Array.isArray(data)) {
                // Selecciona el cuerpo de la tabla
                let tbody = $("#miTabla tbody");

                // Limpia cualquier contenido existente en el cuerpo de la tabla
                tbody.empty();

                // Recorre los datos recibidos y crea nuevas filas para la tabla
                data.forEach(function (fila) {
                    let tr = $("<tr></tr>");
                    tr.append($("<td></td>").text(fila.cat_id));
                    tr.append($("<td></td>").text(fila.cat_nom));

                    // Crear el select para niveles de peligro
                    let selectHtml = $("<select class='form-select'></select>");
                    if (nivelPeligro.length > 0) {
                        nivelPeligro.forEach(function (nivel) {
                            let option = $("<option></option>")
                                .val(nivel.ev_niv_id)
                                .text(nivel.ev_niv_nom);
                            if (nivel.ev_niv_id == fila.ev_niv_id) {
                                option.attr("selected", "selected");
                            }
                            selectHtml.append(option);
                        });
                    } else {
                        console.error("No se han recibido datos de niveles de peligro.");
                    }
                    tr.append($("<td></td>").append(selectHtml));

                    // Crear los botones de editar y borrar
                    const buttonedit   ='<button id="buttonedit"   class="btn btn-warning" type="button"><img src="../../public/img/edit.svg"></button>';
                    const buttondelete ='<button id="buttondelete" class="btn btn-danger" type="button"><img src="../../public/img/trash.svg"></button>';
                    const buttons = buttonedit + buttondelete;
                    tr.append($("<td></td>").append(buttons));

                    tbody.append(tr);
                });
            } else {
                console.error("Datos categoría inválidos:", data);
            }
        },
        "json"
    ).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error en la solicitud AJAX de categorías:", textStatus, errorThrown);
    });
}

// Función para agregar el evento change a los selects
$("body").on("change", ".form-select", function() {
    let tr = $(this).closest("tr");
    let cat_id = tr.find("td").eq(0).text();
    let cat_nom = tr.find("td").eq(1).text();
    let ev_niv_id = $(this).val();
    let data ={
        op:"update_categoria",
        cat_id: cat_id,
        cat_nom: cat_nom,
        ev_niv_id: ev_niv_id
    };

    // Enviar datos mediante una solicitud POST
    $.post("../../controller/categoria.php", data , function(response) {
        // Manejar la respuesta del servidor
        if (response.status === "success") {
            Toast.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.mensaje
            });
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Error',
                text: response.mensaje
            });
        }
    }, "json").fail(function(jqXHR, textStatus, errorThrown) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error en la solicitud: ' + textStatus
        });
    });
});

// Agregar funcionalidad al botón para mostrar la ventana SweetAlert2
$('#addButton').on('click', function() {
    let selectOptions = nivelPeligro.map(function(item) {
        return `<option value="${item.ev_niv_id}">${item.ev_niv_nom}</option>`;
    }).join('');

    Swal.fire({
        title: 'Agregar Categoría',
        html: `
            <input type="text" id="cat_nom" class="swal2-input" placeholder="Nombre de la Categoría">
            <select id="ev_niv_id" class="swal2-select">
                ${selectOptions}
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return {
                cat_nom: $('#cat_nom').val(),
                ev_niv_id: $('#ev_niv_id').val()
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/categoria.php', {
                op: 'add_categoria',
                cat_nom: result.value.cat_nom,
                ev_niv_id: result.value.ev_niv_id
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire('¡Categoría Agregada!', '', 'success');
                    // Actualizar la tabla después de agregar la categoría
                    actualizarTabla();
                } else {
                    Swal.fire('Error al agregar la categoría', response.mensaje, 'error');
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error al agregar la categoría', 'Error en la solicitud: ' + textStatus, 'error');
            });
        }
    });
});

// Función para manejar el evento de clic en el botón de borrar
$("body").on("click", "#buttondelete", function() {
    let cat_id = $(this).closest("tr").find("td:eq(0)").text();

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, bórralo'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/categoria.php", { op: "delete_categoria", cat_id: cat_id },
                function(response) {
                    if (response.status === "success") {
                        Swal.fire('¡Eliminado!', 'La categoría ha sido eliminada.', 'success');
                        // Actualizar la tabla después de eliminar la categoría
                        actualizarTabla();
                    } else {
                        Swal.fire('Error', response.mensaje, 'error');
                    }
                },
                "json"
            ).fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error', 'Error en la solicitud: ' + textStatus, 'error');
            });
        }
    });
});

// Función para manejar el evento de clic en el botón de editar
$("body").on("click", "#buttonedit", function() {
    let tr = $(this).closest("tr");
    let cat_id = tr.find("td:eq(0)").text();
    let cat_nom = tr.find("td:eq(1)").text();
    let ev_niv_id = tr.find("select").val();

    Swal.fire({
        title: 'Editar Categoría',
        html: `
            <input type="hidden" id="edit_cat_id" value="${cat_id}">
            <input type="text" id="edit_cat_nom" class="swal2-input" value="${cat_nom}">
            <select id="edit_ev_niv_id" class="swal2-select">
                ${nivelPeligro.map(item => `<option value="${item.ev_niv_id}" ${item.ev_niv_id === ev_niv_id ? 'selected' : ''}>${item.ev_niv_nom}</option>`).join('')}
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return {
                cat_id: $('#edit_cat_id').val(),
                cat_nom: $('#edit_cat_nom').val(),
                ev_niv_id: $('#edit_ev_niv_id').val()
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/categoria.php", {
                op: 'update_categoria',
                cat_id: result.value.cat_id,
                cat_nom: result.value.cat_nom,
                ev_niv_id: result.value.ev_niv_id
            }, function(response) {
                if (response.status === 'success') {
                    Swal.fire('¡Categoría Actualizada!', '', 'success');
                    // Actualizar la tabla después de editar la categoría
                    actualizarTabla();
                } else {
                    Swal.fire('Error al editar la categoría', response.mensaje, 'error');
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error al editar la categoría', 'Error en la solicitud: ' + textStatus, 'error');
            });
        }
    });
});
// jquery document ready para tranformar tabla en datatable
$(document).ready(function() {
    $('#miTabla').DataTable({
        responsive: true,
        "order": [[0, "asc"]],
        "columnDefs": [
            {
                "targets": [0],
                "orderable": false
            },
            {
                "targets": [1],
                "orderable": false
            },
            {
                "targets": [2],
                "orderable": false
            }, 
            {
                "targets": [3],
                "orderable": false
            }
        ]
    });
}); 
