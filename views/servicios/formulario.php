<div class="campo">
    <label for="nombre">Nombre</label>
    <input 
        type="text"
        id="nombre"
        placeholder="Nombre servicio"
        name="nombre"
        value="<?php echo $servicio->nombre?>"
        required
    />
</div>
<div class="campo">
    <label for="precio">Precio</label>
    <input 
        type="number"
        id="precio"
        placeholder="Precio servicio"
        name="precio"
        step="0.01"
        value="<?php echo $servicio->precio?>"
        required
    />
</div>