<h1 class="nombre-pagina"> Crear Cuenta </h1>
<p class="descripcion-pagina">Rellena el siguiente formulario para crear una cuenta</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>
<form action="/crear-cuenta" class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input
            type="text"
            id="nombre"
            placeholder="Tú Nombre"
            name="nombre"
            value="<?php echo s($usuario->nombre);?>"
        />
    </div>
    <div class="campo">
        <label for="apellido">Apellido</label>
        <input
            type="text"
            id="apellido"
            placeholder="Tú Apellido"
            name="apellido"
            value="<?php echo s($usuario->apellido);?>"
        />
    </div>
    <div class="campo">
        <label for="telefono">Telefono</label>
        <input
            type="numb"
            id="telefono"
            placeholder="Tú Telefono"
            name="telefono"
            value="<?php echo s($usuario->telefono);?>"
        />
    </div>
    <div class="campo">
        <label for="email">E-mail</label>
        <input
            type="email"
            id="email"
            placeholder="Tú E-mail"
            name="email"
            value="<?php echo s($usuario->email);?>"
        />
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            placeholder="Tú Password"
            name="password"
        />
    </div>

    <input type="submit" value="Crear Cuenta" class="boton">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/olvide">¿Olvidaste tu Password?</a>
</div>