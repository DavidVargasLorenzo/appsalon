let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
})

function iniciarApp(){
    mostrarSeccion();
    tabs(); // cambiar la seccion cuando se presione los tabs
    botonesPaginador(); // agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); //Consultar la API en el backend de php

    idCliente(); //Extraer la Id del usuario
    nombreCliente();//Añade el nombre del cliente
    seleccionarFecha(); //añade la fecha de la cita en el objeto
    seleccionarHora(); //añade la hora de la cita en el objeto

    mostrarResumen(); //Muestra el resumen de la cita

}
function mostrarSeccion(){
    // Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
    seccionAnterior.classList.remove('mostrar');
    };
    // Seleccionar la sección con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');
    //Quitar la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
    tabAnterior.classList.remove('actual');
    }
    // resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton =>{
        boton.addEventListener('click', function(e) {
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();
            botonesPaginador();
        })
    })
}

function botonesPaginador() {
    const paginaSiguiente  = document.querySelector('#siguiente');
    const paginaAnterior  = document.querySelector('#anterior');

    if(paso === pasoInicial){
        paginaSiguiente.classList.remove('ocultar');
        paginaAnterior.classList.add('ocultar');
    }else if (paso === pasoFinal){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }else{
        paginaSiguiente.classList.remove('ocultar');
        paginaAnterior.classList.remove('ocultar');
    }
    mostrarSeccion();
}
function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {
       
        if (paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
    })
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {
       
        if (paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    })
}

async function consultarAPI(){

    try {
        const url = `/api/servicios`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
       
    } catch(error) {
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach( servicio =>{
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `${precio}€`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    
    })
}

function seleccionarServicio(servicio) {
    const { id } = servicio;
    const { servicios } = cita;
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    if( servicios.some( agregado => agregado.id === servicio.id) ) {
        cita.servicios = servicios.filter( agregado => agregado.id !== servicio.id)
        divServicio.classList.remove('seleccionado');
    }else {
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
}
function idCliente(){
    cita.id = document.querySelector('#id').value; 
}

function nombreCliente(){
    cita.nombre = document.querySelector('#nombre').value; 
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha'); 
    inputFecha.addEventListener('input', function(e){

        const dia = new Date(e.target.value).getUTCDay();

        if ([6, 0].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    });
}
function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if (hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Abrimos a las 10:00 y cerramos a las 18:00', 'error' , '.formulario')
        }else {
            cita.hora = e.target.value;
        }
    })
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){

    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia){
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const formulario = document.querySelector(elemento);
    formulario.appendChild(alerta);

   if (desaparece){
    setTimeout(() => {
        alerta.remove();
    }, 3000);
   }
}

function mostrarResumen() {
    const resumen= document.querySelector('.contenido-resumen');
    // Limpiar el contenido de resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }
    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Faltan datos de servicios, Fecha u Hora ', 'error' , '.contenido-resumen', false);
        return;
    } 

    // formatear el div de resumen
    const {nombre, fecha, hora, servicios} = cita;

    const nombreCliente = document.createElement('P')
    nombreCliente.innerHTML = `<span>Nombre:</span>${nombre}`;

    //Formatear la Fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate();
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));

    const opciones = {weekday: 'long', year: 'numeric', month:'long', day: 'numeric'};
    const fechaFormateada = fechaUTC.toLocaleDateString('es-ES', opciones);


    const fechaCita = document.createElement('P')
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P')
    horaCita.innerHTML = `<span>Hora:</span> ${hora}`;

    //Heading para servicios en resumne

    const headingServicios = document.createElement('H3');
    headingServicios.textContent="Resumen de Servicios";
    resumen.appendChild(headingServicios);
    const headingCita = document.createElement('H3');
    headingCita.textContent="Resumen de Cita";
    

    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;

        const contenedorServicio = document.createElement('P')
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> ${precio}€`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);

    })

    //boton para añadir una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;


    resumen.appendChild(headingCita);
    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
}

async function reservarCita(){

    const { id, fecha, hora, servicios} = cita;

    const idServicios = servicios.map( servicio => servicio.id );

    const datos = new FormData();
    
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    // console.log([...datos]);
    try {
        //Peticion hacia la api
    const url = '/api/citas';
    const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
    });

    const resultado = await respuesta.json();

    if(resultado.resultado){
        Swal.fire({
            icon: "success",
            title: "Cita Creada",
            text: "Tu cita fue creada correctamente",
            button: 'Ok'
          }).then( () => {
            window.location.reload();
          });
    }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            html: "<strong>Algo ha salido mal...</trong><br>Vuelve a intentarlo mas tarde",
            
          });
    }
    
    
    

}