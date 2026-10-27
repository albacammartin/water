//Buscar todos los formularios que tengan la clase = form-borrar
const formulariosBorrar = document.querySelectorAll('.form-borrar');

//A cada uno le digo que hacer cuando se envíe
formulariosBorrar.forEach(function(formulario){
    formulario.addEventListener('submit', function(evento){
        const seguro = confirm('¿Seguro que quieres borrarlo? No se podrá deshacer.')

        if(!seguro){
            evento.preventDefault();
        }
    })
})