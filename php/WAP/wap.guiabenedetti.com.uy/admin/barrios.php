<?php $p = (isset($_GET['p'])) ? $_GET['p'] : 0; ?>

<div id="principal">
	<ul>
		<li>
			<span class="list_itm list_top b_id">ID</span>
			<span class="list_itm list_top b_bar">Barrio</span>
			<span class="list_itm list_top b_tit">Titulo</span>
			<span class="list_itm list_top b_acc">Acciones</span>
		</li>
	</ul>
	<ul id="lista_barrios"></ul>
</div>

<div id="lateral">
	
	<form id="barrios_form">
		<h3></h3>
		<label>Barrio</label><br/>
		<input type="text" name="barrio"/><br/>
		<label>Titulo</label><br/>
		<input type="text" name="titulo"/><br/>
		<a class="btn_enviar" href="#">Enviar</a>
		<a class="btn_cancel" href="javascript:UI.hideForm('barrios_form')">Cancelar</a>
	</form>
	
	<form id="puntos_form">
		<h3></h3>
		<label>Punto</label><br/>
		<input type="text" name="punto"/><br/>
		<a class="btn_enviar" href="#">Enviar</a>
		<a class="btn_cancel" href="javascript:UI.hideForm('puntos_form')">Cancelar</a>
	</form>
	
	<form id="contenidos_form" enctype="multipart/form-data" method="POST" action="php/puntos_op.php" target="puntos_frame">
		<iframe id="puntos_frame" name="puntos_frame" src="php/puntos_op.php" style="display:none"></iframe>
		<div id="img_data">
			<h3>Imagen</h3>
			<hr/>
			<label>Titulo:</label><br/>
			<input type="text" name="img_title"/><br/>
			<input type="file" name="img"/><br/><br/>
			<label>Preview:</label><br/>
			<div id="img_preview"></div><br/>
		</div>
		<div id="txt_data">
			<h3>Texto</h3>
			<hr/>
			<label>Titulo:</label><br/>
			<input type="text" name="txt_title"/><br/>
			<label>Sumario:</label><br/>
			<textarea cols="35" rows="3" name="summary"></textarea>
			<label>Cuerpo:</label><br/>
			<textarea cols="35" rows="3" name="body"></textarea>
		</div>
		<a class="btn_enviar" href="#">Enviar</a>
		<a class="btn_cancel" href="javascript:UI.hideForm('contenidos_form')">Cancelar</a>
	</form>
	
</div>

<script>Barrios.listar();</script>