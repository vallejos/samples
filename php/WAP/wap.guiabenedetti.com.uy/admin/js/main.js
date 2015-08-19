var Barrios = {
	
	metodo:"POST", 
	url:"php/barrios_op.php", 
	lastLP:null, 
	lastLI:undefined, 
	
	nuevo: function() {
		
	}, 
	
	getData: function(id, barrio, title) {
		$('#barrios_form input[name="barrio"]').val(barrio);
		$('#barrios_form input[name="titulo"]').val(title);
		UI.showForm('barrios_form', 'Editar Barrio', 'javascript:Barrios.editar(\''+id+'\')');
	}, 
	
	editar: function(id) {
		var barrio = $('#barrios_form input[name="barrio"]').val();
		var title = $('#barrios_form input[name="titulo"]').val();
		$.ajax({
			type:this.metodo, 
			url:this.url, 
			data: { "accion":"editar", "barrio":barrio, "title":title, "id":id }, 
			success: function(res) {
				alert(res);
				UI.hideForm('barrios_form');
				Barrios.listar();
			}
		});
	}, 
	
	listar: function() {
		$.ajax({
			type:this.metodo, 
			url:this.url, 
			data: { "accion":"listar" }, 
			success: function(res) {
				$('#lista_barrios').html(res);
			}
		})
	}, 
	
	getPuntos: function(id) {
		$.ajax({
			type:this.metodo, 
			url:this.url, 
			data: { "accion":"getPuntos", "id":id }, 
			success: function(res) {
				if (Barrios.lastLP != null) $(Barrios.lastLP).html('');
				var lp = '#lp_' + id;
				Barrios.lastLP = lp;
				$(lp).html(res);
				$(lp+' a[rel="elegible"]').click(function() {
					if (Barrios.lastLI != undefined) $(Barrios.lastLI).css("background", "none");
					$(this).parent('li').css("background", "#E8E8E8");
					Barrios.lastLI = $(this).parent('li');
					//$('html, body').animate({scrollTop:0}, '300');
				});
			}
		})
	}
	
}

var Puntos = {
	
	metodo:"POST", 
	url:"php/puntos_op.php", 
	
	nuevo: function() {
		
	}, 
	
	editar: function(id_barrio, old_punto) {
		var punto = $('#puntos_form input[name="punto"]').val();
		$.ajax({
			type:this.metodo, 
			url:this.url, 
			data: { "accion":"editar", "id_barrio":id_barrio, "old_punto":old_punto, "punto":punto }, 
			success: function(res) {
				alert(res);
				UI.hideForm('puntos_form');
				Barrios.getPuntos(id_barrio);
			}
		});
	}, 
	
	getPuntoData: function(id_barrio, punto) {
		UI.showForm('puntos_form', 'Editar Punto', "javascript:Puntos.editar('"+id_barrio+"', '"+punto+"')");
		$('#puntos_form input[name="punto"]').val(punto);
	}, 
	
	getContenidos: function(id_barrio, punto) {
		UI.showForm('contenidos_form', null, null, true);
		$.ajax({
			type:this.metodo, 
			url:this.url, 
			data: { "accion":"getContenidos", "id_barrio":id_barrio, "punto":punto }, 
			success: function(res) {
				var obj = $.parseJSON(res);
				var img = obj[1];
				var txt = obj[2];
				if (img != null) {
					$('[name="img_title"]').val(img.title);
					var ac = new Date().getTime();
					$('#img_preview').html('<img src="php/thumb.php?img=../../uploads/'+img.file+'&w=310&ac='+ac+'"/>');
				}
				if (txt != null) {
					$('[name="txt_title"]').val(txt.title);
					$('[name="summary"]').val(txt.summary);
					$('[name="body"]').val(txt.body);
				}
				$('#contenidos_form .btn_enviar').click(function() { 
					Puntos.editarContenidos(obj);
					UI.hideForm('contenidos_form');
				});
			}
		})
	}, 
	
	editarContenidos: function(obj) {
		$('#contenidos_form [type="hidden"]').remove();
		var imgFlag = (obj[1] != null) ? 1 : 0;
		var txtFlag = (obj[2] != null) ? 1 : 0;
		var img_id = (imgFlag) ? obj[1].id : null;
		var txt_id = (txtFlag) ? obj[2].id : null;
		var html = '';
		html += '<input type="hidden" name="accion" value="editarContenidos"/>';
		html += '<input type="hidden" name="id_barrio" value="'+obj[0].id_barrio+'"/>';
		html += '<input type="hidden" name="punto" value="'+obj[0].punto+'"/>';
		html += '<input type="hidden" name="txtFlag" value="'+txtFlag+'"/>';
		html += '<input type="hidden" name="imgFlag" value="'+imgFlag+'"/>';
		html += '<input type="hidden" name="img_id" value="'+img_id+'"/>';
		html += '<input type="hidden" name="txt_id" value="'+txt_id+'"/>';
		$(html).appendTo('#contenidos_form');
		$('#contenidos_form').submit();
	}
	
}

var UI = {
	
	showForm: function(target, title, href, reset) {
		UI.hideAll();
		reset = (typeof(reset) != undefined) ? reset : false;
		if (reset) {
			$('#img_preview').html('');
			$('#'+target).html($('#'+target).html());
		}
		if (title != null) $('#'+target+' h3').html(title);
		if (href != null) $('#'+target+' .btn_enviar').attr("href", href);
		$('#'+target).css("display", "block");
		$('html, body').animate({scrollTop:0}, '300');
	}, 
	
	hideForm: function(target) {
		$('#'+target).css("display", "none");
	}, 
	
	hideAll: function() {
		$('#puntos_form').css("display", "none");
		$('#barrios_form').css("display", "none");
		$('#contenidos_form').css("display", "none");
	}
	
}