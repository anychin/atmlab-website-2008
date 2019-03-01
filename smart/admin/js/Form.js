function Form(id, url)
{
	var data = {isValid:true,id:id};
	var form = jq("#" + id);
	var that = this;
	var formAnnotations = {};
	var formTag = jq("form", form);
	var loading = jq(".loading", form);
	formAnnotations = Annotation.getFormAnnotations(id);
	
	this.getId = function(){
		return id;
	}
	
	if(formTag.length > 0)
		formTag.bind("submit", function(){
			return false;
		});
		
	var submitButton = jq("input:submit, input:image, .submit", form);
	
	submitButton.bind("click", function(){
		submitForm();
		return false;
	});
	
	initInternal();
	
	function parseFields()
	{
		var fields = {};
		jq(".formItem", form).each(function(i, item){
			var control;
			item = jq(item);
			var annotations = Annotation.getAnnotations(item);
			var type = getType(item, annotations);
			var name = jq("input, select, textarea", item).attr("name");
			if(annotations.name != undefined)
				name = annotations.name;
			var errorContainer = jq(".errorMessage", item);
			if(annotations.targetErrorMessage != undefined)
				errorContainer = jq("#" + annotations.targetErrorMessage);
			var errors = [];
			for(var annotation in annotations){
				if(annotation.indexOf("error.") > -1)
					errors[annotation.replace("error.","")] = annotations[annotation];
			}
			var field = 
			{
				name:name, 
				field:item,
				annotations:annotations, 
				type:type,
				val:null, 
				error:errorContainer,
				errors:errors
			};
			if(name != undefined)
			{
				fields[name] = field;
				field.addError = delegate(field, function(error){
					data.addError(this.name , error);
				});
			}
			if(field.type == "text" || field.type == "email" || field.type == "password" || field.type == "int" || field.type == "integer" || field.type == "float" || field.type == "long"){
				control = jq("input:text,input:password", item);
				if(annotations["no-enter"] == undefined && formAnnotations["no-enter"] == undefined)
					control.bind("keypress", function(e){
						if(e.which == 13)
							submitForm();
					});
			}
			if(field.type == "textarea"){
				control = jq("textarea", item);
				if(annotations["no-enter"] == undefined && formAnnotations["no-enter"] == undefined)
					control.bind("keypress", function(e){
						if(e.which == 13 && e.ctrlKey == true){
							submitForm();
							return false;
						}
					});
			}
			if(field.type == "datepicker"){
				jq("input", item).datepicker({
					/*
					month_names: ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"],
					short_month_names: ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"],
					short_day_names: ["вс", "пн", "вт", "ср", "чт", "пт", "сб"]
					*/
				});
			}
		});
		return fields;
	}
	
	function value(item, annotations, type, name){
		if(annotations.value != undefined)
			return callMethod(annotations.value, item);
		if(type == "text" || type == "password" || type == "hidden" || type == "int" || type == "integer" || type == "float" || type == "long")
			return jq.trim(jq("input", item).val());
		if(type == "email"){
			var re = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
			var v = jq.trim(jq("input", item).val());
			if(!v.match(re) && v != '')
				data.addError(name, "not.valid");
			return v;
		}
		if(type == "richtext"){
			var i = jq("textarea", item).attr("id");
			return tinyMCE.get(i).getContent();
		}
		if(type == "textarea")
			return jq.trim(jq("textarea", item).val());
		if(type == "select"){
			var v = jq.trim(jq("select", item).val()); 
			return v == "nill" ? null : v;
		} 
		
		if(type == "checkbox")
		{
			var input = jq("input", item);
			return checked(input.attr("name"));
		}
		if(type == "date")
			return getDateValue(item, name);
		if(type == "radio")
		{
			var c = jq("input[type=radio]", item).is("[checked]");
			if(!c)
				return null;
			return jq("input[type=radio][checked]", item).val()
		}
		if(type == "datepicker"){
			var d = jq("input", item).val().split("\.");
			return d[2] + "-" + d[1] + "-" + d[0];
		}
		return null;
	}
	
	function getDateValue(item, name){
		var day;
		var month;
		var year;
		var time = "";
		var input = jq("input[name=time]", item);
		var yearInput = jq("input[name=year]", item);
		var val = 1;
		jq("select", item).each(function(i, sel){
			sel = jq(sel);
			if(sel.val() == "nill")
				val = null;
			if(sel.is("[name=day]"))
				day = sel.val();
			if(sel.is("[name=month]"))
				month = sel.val();
			if(sel.is("[name=year]"))
				year = sel.val();
		});
		if(yearInput.length ==1 && yearInput.css("display") != "none")
		{
			year = "nill";
			var rp = new RegExp("[1-9][0-9][0-9][0-9]");
			val = null;
			if(yearInput.val().match(rp)){
				val = 1;
				year = yearInput.val();
			}
		}
		if(val == null)
			return null;
		//time
		if(input.length > 0 && jq.trim(input.val()) != ""){
			var v = jq.trim(input.val());
			if(v != undefined){
				var regExp = new RegExp("\\d\\d:\\d\\d");
				if(v.match(regExp)){
					var vs = v.split(":");
					var h = parseInt(vs[0]);
					var m = parseInt(vs[1]);
					if(h > 23 || m > 59)
					{
						data.addError(name, "notvalid");
						time = null;
					}
				}else{
					data.addError(name, "notvalid");
					time = null;
				}
				v = v + ":00";
			}
			if(time != null)
				time = v;
		}
		var result = year + "-";
		var date = new Date();
		date.setYear(year);
		date.setMonth(month);
		date.setDate(day);
		if(date.getMonth() != month)
		{
			data.addError(name, "notvalid");
		}
		if(month.length < 2)
			result += "0" + (eval(month) + 1) + "-";
		else
			result += (eval(month) + 1) + "-";
		if(day.length < 2)
			result += "0" + day;
		else
			result += day;
		if(time != null)
			result += " " + time;
		return result;
	}
	
	function submitForm()
	{
		getFormData();
		validate();
		if(data.isValid)
			sendFormData();
	}
	
	this.submit = function(){
		submitForm();
	}
	
	function initInternal()
	{
		data.fields = parseFields();
		data.addError = function(f, er){
			data.isValid = false;
			data.fields[f].field.removeClass("formItem");
			data.fields[f].field.addClass("formItemError");
			if(data.fields[f].errors[er] != undefined)
				data.fields[f].error.html(data.fields[f].errors[er]);
			that.setFormError("");
			
		}
		data.clearError = function(f){
			data.fields[f].field.removeClass("formItemError");
			data.fields[f].field.addClass("formItem");
			data.fields[f].error.html("");
			clearFormError();
		}
		data.addFormError = function(er){
			that.setFormError(er);
		}
	}
	
	this.setVal = function(name, val){
		var f = data.fields[name];
		f.val = val;
	}
	
	function onError(msg){
		showLoading(false);
		if(formAnnotations.onError != undefined)
			callMethod(formAnnotations.onError, msg);
		else
			that.setFormError("error");
		data.isValid = false;
	}
	
	function clearFormError(){
		var formError = jq(".formErrorMessage", form);
		if(formError.length > 0)
			formError.html("");
	}
	
	function showLoading(l){
		if(loading.length > 0){
			if(l){
				submitButton.css({display:"none"});
				loading.css({display:"inline"});
			}else{
				submitButton.css({display:"inline"});
				loading.css({display:"none"});
			}
		}
	}
	
	this.setFormError = function(error){
		
		var formError = jq("#" + id + " .formErrorMessage");
		if(formError.length == 0)
			return;
		if(error == "")
			formError.html(getMessage(id));
		else
			formError.html(getMessage(id + "." + error));
		data.isValid = false;
	}
	
	function onGetResponse(response){
		var o = XMLParser.deserialize(response);
		var name = "response";
		if(formAnnotations.response != undefined)
			name = formAnnotations.response;
		var d = o[name];
		if(formAnnotations.onResponse != undefined)
		{
			callMethod(formAnnotations.onResponse, d, data);
			return;
		}
		if(d.valid == "true")
		{
			if(formAnnotations.onSuccess != undefined)
				callMethod(formAnnotations.onSuccess, d, data);
			else
				if(formAnnotations.successUrl != undefined)
				{
					if(formAnnotations.successUrl == "this")
						document.location.href = document.location.href;
					else
						document.location.href = formAnnotations.successUrl;
				}
		}
		else
		{
			var errors = d.errors[0].error;
			for(var i = 0; i < errors.length; i++)
				if(data.fields[errors[i].name] != undefined)
					data.fields[errors[i].name].addError(errors[i].value);
			showLoading(false);	
		}
	}
	
	function sendFormData()
	{
		var post = true;
		if(formAnnotations.method == "get")
			post = false;
		var method = post ? "POST" : "GET";
		var d = getFormObject();
		if(formAnnotations.beforeSend != undefined)
			d = callMethod(formAnnotations.beforeSend, d);
		var sendUrl = url + formAnnotations.name;
		if(formAnnotations.url != undefined)
			sendUrl = formAnnotations.url;
		showLoading(true);
		jq.ajax({
			type:method,
			data:d,
			cache:false,
			url:sendUrl,
			dataType:"html",
			success:onGetResponse,
			error:onError
		});
		
	}
	
	this.loadingSwitch = function(val){
		showLoading(val);
	}
	
	
	function getFormObject()
	{
		var formData = {};
		for(var i in data.fields)
			formData[i] = data.fields[i].val;
		return formData;
	}
	
	function getFormData()
	{
		data.isValid = true;
		clearFormError();
		var fields = data.fields;
		for(var i in fields)
		{
		
			var field = fields[i];
			field.field.removeClass("formItemError");
			field.field.addClass("formItem");
			field.error.html("");
			field.val = value(field.field, field.annotations, field.type, field.name);
		}
	}
	
	this.getData = function(){
		return data;
	};
	
	
	function validate()
	{
		for(var field in data.fields)
		{
			var f = data.fields[field];
			if(f.annotations.validate != undefined)
				callMethod(f.annotations.validate, f, data.fields);
			else
			{
				if(f.annotations.required == "true" && (!f.val || f.val == '' || f.val == null||f.val=="<p>&nbsp;</p>"))
					f.addError("required");
				if(f.val == "")
					continue;
				if(f.type == "int" || f.type == "integer")
				{
					var regExp = /^([\d]+)$/;
					if(!f.val.match(regExp))
						f.addError("format.wrong");
				}
				if(f.type == "long")
				{
					var regExp = /^([\d]+)$/;
					if(!f.val.match(regExp))
						f.addError("format.wrong");
				}
				if(f.type == "float")
				{
					var regExp = /^([\d]+(\.)?[\d]*)$/;
					if(!f.val.match(regExp))
						f.addError("format.wrong");
				}
				if(f.annotations.length != undefined){
					var l = parseInt(f.annotations.length);
					if(f.val.length < l)
						f.addError("length.wrong");
				}
				if(f.annotations.minlength != undefined){
					var minl = parseInt(f.annotations.minlength);
					if(f.val.length < minl)
						f.addError("length.wrong");
				}
			}
			
		}
		if(formAnnotations.validate != undefined)
			callMethod(formAnnotations.validate, data.fields);
	}
	
	function checked(name){
		var res = false;
		jq.each(jq("input:checked"), function(){
			if(jq(this).attr("name") == name)
				res = true;
		});
		return res;
	}
	
	function callMethod(name){
		var a = [];
		for(var i = 1; i < arguments.length; i++)
			a.push(arguments[i]);
		if(name instanceof Function)
		{
			name.apply(name.instance, a);
			return;
		}
		var target = window;
		var method = name;
		if(name.indexOf('.') > -1)
		{
			var arr = name.split('.');
			target = window[arr[0]];
			method = arr[1];
		}
		if(target == undefined)
			return null;
		if (target[method] == undefined)
			return null;
			
		return target[method].apply(this, a);
	}
	
	function delegate(t, f){
		return function(){
			f.apply(t, arguments);
		}
	}
	
	function getType(item, annotations){
		var type = undefined;
		if(annotations.type != undefined)
			return annotations.type;
		var input = jq("input", item);
		if(input.length == 1)
			type = input.attr("type");
		if(type == undefined)
		{
			var input = jq("select", item);
			if(input.length > 0)
				type = "select";
			else
			{
				var input = jq("textarea", item);
				if(input.length > 0)
					type = "textarea";
			}
		}
		return type;
	}
	
	function addFormAnnotation(name, value){
		formAnnotations[name] = value;
	}
	
	function addFieldAnnotation(field, name, value){
		data.fields[field].annotations[name] = value;
	}
	
	this.addAnnotation = function(name, value){
		addFormAnnotation(name, value);
	}
	
	this.addFieldAnnotation = function(field, name, value){
		addFieldAnnotation(field, name, value);
	}
}

Form.init = function(formUrl)
{
	var forms = {};
	jq(".form").each(function(){
		var id = jq(this).attr("id");
		forms[id] = new Form(id, formUrl);
	});
	return forms;
}

var Annotation = {};

Annotation.getFormAnnotations = function(f)
{
	var annotations = jq("#" + f + "> .annotation");
	if(annotations.length == 0)
		return {};
	return Annotation.parseAnnotations(annotations.html());
}

Annotation.getAnnotations = function(formItem){
	var annotations = jq(".annotation", formItem);
	if(annotations.length == 0)
		return {};
	return Annotation.parseAnnotations(annotations.html());
}

Annotation.parseAnnotations = function(str){
	if(str == "")
		return {};
	var annotations = str.split(',');
	var res = {};
	for(var i = 0; i < annotations.length; i++)
	{
		var s = jq.trim(annotations[i]);
		var a = s.split('=');
		res[a[0]] = a[1];
	}
	return res;
}